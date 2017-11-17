<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Ente;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Logging\LogConstants;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class UserControllerTest
 */
class UserControllerTest extends AbstractAppTestCase
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->em->getConnection()->executeQuery('DELETE FROM servizio_erogatori')->execute();
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Allegato::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(CPSUser::class);
        $this->cleanDb(Servizio::class);
        $this->cleanDb(Ente::class);
    }

    /**
     * @test
     */
    public function testICannotAccessUserDashboardAsAnonymousUser()
    {
        $this->client->request('GET', $this->router->generate('user_dashboard'));
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testICanAccessUserDashboardAsLoggedUser()
    {
        $user = $this->createCPSUser();
        $this->clientRequestAsCPSUser(
            $user,
            'GET',
            $this->router->generate(
                'user_dashboard'
            )
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testICannotAccessUserProfileAsAnonymousUser()
    {
        $this->client->request('GET', $this->router->generate('user_profile'));
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testICanAccessUserProfileAsLoggedUser()
    {
        $user = $this->createCPSUser();
        $this->client->request('GET', $this->router->generate('user_profile'));
        $this->clientRequestAsCPSUser(
            $user,
            'GET',
            $this->router->generate(
                'user_dashboard'
            )
        );
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testUseCPSValuesAsDefaultContactInfoWhenCreatingUser()
    {
        $user = $this->createCPSUser();
        $this->assertEquals($user->getCellulare(), $user->getCellulareContatto());
        $this->assertEquals($user->getEmail(), $user->getEmailContatto());
    }

    public function testICanChangeMyContactInfoAsLoggedUser()
    {
        $mockLogger = $this->getMockLogger();
        $mockLogger->expects($this->exactly(1))
                   ->method('info')
                   ->with(LogConstants::USER_HAS_CHANGED_CONTACTS_INFO);
        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockLogger) {
            $kernel->getContainer()->set('logger', $mockLogger);
        });

        $user = $this->createCPSUser();

        $testEmail = rand(1, 10).'@example.com';
        $testCellulare = rand(1, 10);

        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_profile'));
        $form = $crawler->selectButton($this->translator->trans('user.profile.salva_informazioni_profilo'))->form([
            'form[email_contatto]' => $testEmail,
            'form[cellulare_contatto]' => $testCellulare,
        ]);
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");

        $this->em->refresh($user);
        $this->assertEquals($testCellulare, $user->getCellulareContatto());
        $this->assertEquals($testEmail, $user->getEmailContatto());
    }

    public function testICanGetUserRecentNewsAsLoggedUser()
    {
        $expectedData = 2;
        $totalExpectedData = 0;
        $enti = $this->createEnti();
        $responses = [];
        for($i=0; $i < count($enti); $i++){
            $responses[] = $this->getComunwebRemoteSuccessResponse($expectedData);
            $totalExpectedData += $expectedData;
        }

        $mockGuzzleClient = $this->getMockGuzzleClient($responses);

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockGuzzleClient) {
            $kernel->getContainer()->set('guzzle.client.comunweb', $mockGuzzleClient);
        });

        $user = $this->createCPSUser();
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_latest_news'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $data = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertEquals($totalExpectedData, count($data));
    }

    public function testICanGetUserRecentNewsFilteredByMyPraticaEnteAsLoggedUser()
    {
        $this->markTestSkipped("An exception occurred while executing 'INSERT INTO ente (id, name, slug, codice_meccanografico, protocollo_parameters, site_url) VALUES (?, ?, ?, ?, ?, ?)' with params [\"1abd2b79-98a3-4a80-83a6-a1f84ec38720\", \"Ente di prova\", \"ente-di-prova\", \"L378\", \"a:0:{}\", \"http:\/\/example.com\"]:");
        $expectedData = 2;
        $enti = $this->createEnti();
        $erogatore = $this->createErogatoreWithEnti($enti);
        $responses = [];
        for($i=0; $i < count($enti); $i++){
            $responses[] = $this->getComunwebRemoteSuccessResponse($expectedData);
        }

        $mockGuzzleClient = $this->getMockGuzzleClient($responses);

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockGuzzleClient) {
            $kernel->getContainer()->set('guzzle.client.comunweb', $mockGuzzleClient);
        });

        $user = $this->createCPSUser();
        $this->createPratica($user, null, null, $erogatore);
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_latest_news'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $data = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertEquals($expectedData, count($data));
    }

    public function testLogIfUserRecentNewsRequestFail()
    {
        $enti = $this->createEnti();
        $responses = [];
        for($i=0; $i < count($enti); $i++){
            $responses[] = $this->getComunwebRemoteErrorResponse();
        }
        $mockGuzzleClient = $this->getMockGuzzleClient($responses);
        $mockLogger = $this->getMockLogger();

        $mockLogger->expects($this->exactly(count($enti)))->method('error');

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockGuzzleClient, $mockLogger) {
            $kernel->getContainer()->set('guzzle.client.comunweb', $mockGuzzleClient);
            $kernel->getContainer()->set('logger', $mockLogger);
        });

        $user = $this->createCPSUser();
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_latest_news'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $data = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertEquals(0, count($data));
    }

    public function testICanGetUserRecentDeadlinesAsLoggedUser()
    {
        $expectedData = 2;
        $totalExpectedData = 0;
        $enti = $this->createEnti();
        $responses = [];
        for($i=0; $i < count($enti); $i++){
            $responses[] = $this->getComunwebRemoteSuccessResponse($expectedData);
            $totalExpectedData += $expectedData;
        }

        $mockGuzzleClient = $this->getMockGuzzleClient($responses);

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockGuzzleClient) {
            $kernel->getContainer()->set('guzzle.client.comunweb', $mockGuzzleClient);
        });

        $user = $this->createCPSUser();
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_latest_deadlines'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $data = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);
        $this->assertInternalType('array', $data);
        $this->assertEquals($totalExpectedData, count($data));
    }

    public function testICanGetUserRecentDeadlinesFilteredByMyPraticaEnteAsLoggedUser()
    {
        $this->markTestSkipped("An exception occurred while executing 'INSERT INTO ente (id, name, slug, codice_meccanografico, protocollo_parameters, site_url) VALUES (?, ?, ?, ?, ?, ?)' with params [\"6708a709-a192-46f5-b377-5336ee73c167\", \"Ente di prova\", \"ente-di-prova\", \"L378\", \"a:0:{}\", \"http:\/\/example.com\"]:");
        $expectedData = 2;
        $enti = $this->createEnti();
        $responses = [];
        for($i=0; $i < count($enti); $i++){
            $responses[] = $this->getComunwebRemoteSuccessResponse($expectedData);
        }
        $responses = [
            $this->getComunwebRemoteSuccessResponse($expectedData),
            $this->getComunwebRemoteSuccessResponse($expectedData)
        ];

        $mockGuzzleClient = $this->getMockGuzzleClient($responses);

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockGuzzleClient) {
            $kernel->getContainer()->set('guzzle.client.comunweb', $mockGuzzleClient);
        });

        $user = $this->createCPSUser();
        $this->createPratica($user, null, null, $this->createErogatoreWithEnti([$enti[0]]));
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_latest_deadlines'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $data = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertEquals($expectedData, count($data));
    }

    public function testLogIfUserRecentDeadlinesRequestFail()
    {
        $enti = $this->createEnti();
        $responses = [];
        for($i=0; $i < count($enti); $i++){
            $responses[] = $this->getComunwebRemoteErrorResponse();
        }
        $mockGuzzleClient = $this->getMockGuzzleClient($responses);
        $mockLogger = $this->getMockLogger();

        $mockLogger->expects($this->exactly(count($enti)))->method('error');

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockGuzzleClient, $mockLogger) {
            $kernel->getContainer()->set('guzzle.client.comunweb', $mockGuzzleClient);
            $kernel->getContainer()->set('logger', $mockLogger);
        });

        $user = $this->createCPSUser();
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('user_latest_deadlines'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $data = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true);

        $this->assertInternalType('array', $data);
        $this->assertEquals(0, count($data));
    }
}
