<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\APIController;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Ente;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Logging\LogConstants;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class ApiControllerTest
 */
class ApiControllerTest extends AbstractAppTestCase
{

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->em->getConnection()->executeQuery('DELETE FROM servizio_erogatori')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM erogatore_ente')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM ente_asili')->execute();
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(User::class);
        $this->cleanDb(Ente::class);
        $this->cleanDb(Servizio::class);
    }

    /**
     * @test
     */
    public function testStatusAPI()
    {
        $expectedResponse = (object) [
            'status' => 'ok',
            'version' => APIController::CURRENT_API_VERSION,
        ];
        $this->client->request('GET', '/api/'.APIController::CURRENT_API_VERSION.'/status');

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($expectedResponse, $response);
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    /**
     * @test
     */
    public function testGetServizi()
    {
        $servizio1 = $this->createServizioWithAssociatedErogatori([]);
        $servizio2 = $this->createServizioWithAssociatedErogatori([]);

        $expectedResponse = [
            (object) [
                'name' => $servizio1->getName(),
                'slug' => $servizio1->getSlug(),
            ],
            (object) [
                'name' => $servizio2->getName(),
                'slug' => $servizio2->getSlug(),
            ],
        ];

        $this->client->request('GET', '/api/'.APIController::CURRENT_API_VERSION.'/services');
        $response = json_decode($this->client->getResponse()->getContent(), false);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @test
     */
    public function testPraticaStatusCanBeUpdatedViaProtectedAPI()
    {
        $this->setupMockedLogger([
            LogConstants::PRATICA_CHANGED_STATUS,
            LogConstants::PRATICA_UPDATED_STATUS_FROM_GPA,
        ]);
        $user = $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);
        $initialStatusCount = $pratica->getStoricoStati()->count();

        $rawStatusChange = [
            'evento' => Pratica::STATUS_SUBMITTED,
            'timestamp' => 123,
            'responsabile' => 'Contessa Serbelloni Mazzanti Viendalmare',
            'operatore' => 'pippo',
            'struttura' => 'Anagrafe',
        ];
        $this->client->request(
            'POST',
            $this->formatPraticaStatusUpdateRoute($pratica),
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'gpa',
                'PHP_AUTH_PW' => 'gpapass',
            ),
            json_encode(
                $rawStatusChange
            )
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());

        $this->em->refresh($pratica);
        $finalStatusCount = $pratica->getStoricoStati()->count();
        $this->assertEquals($initialStatusCount+1, $finalStatusCount);
        $newStatusTimestamp = $pratica->getLatestTimestampForStatus($rawStatusChange['evento']);
        $this->assertEquals($rawStatusChange['timestamp'], $newStatusTimestamp);
        $statoCambiato = $pratica->getStoricoStati()[$newStatusTimestamp];
        $this->assertContains([$rawStatusChange['evento'], $rawStatusChange], $statoCambiato);
    }


    /**
     * @test
     */
    public function testPraticaStatusThrowsIfMissingBody()
    {
        $this->setupMockedLogger([
            LogConstants::PRATICA_ERROR_IN_UPDATED_STATUS_FROM_GPA,
        ]);
        $user = $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        $this->client->request(
            'POST',
            $this->formatPraticaStatusUpdateRoute($pratica),
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'gpa',
                'PHP_AUTH_PW' => 'gpapass',
            ),
            null
        );
        $this->assertEquals('', $this->client->getResponse()->getContent());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testPraticaStatusThrowsIfMissingMandatoryFields()
    {
        $this->setupMockedLogger([
            LogConstants::PRATICA_ERROR_IN_UPDATED_STATUS_FROM_GPA,
        ]);
        $user = $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        //missing operatore
        $rawStatusChange = [
            'evento' => Pratica::STATUS_SUBMITTED,
            'timestamp' => 123,
            'responsabile' => 'Contessa Serbelloni Mazzanti Viendalmare',
            //'operatore' => 'pippo',
            'struttura' => 'Anagrafe',
        ];

        $this->client->request(
            'POST',
            $this->formatPraticaStatusUpdateRoute($pratica),
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'gpa',
                'PHP_AUTH_PW' => 'gpapass',
            ),
            json_encode($rawStatusChange)
        );
        $this->assertEquals('', $this->client->getResponse()->getContent());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testPraticaStatusAPIIsProtected()
    {
        $user = $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        $client = static::createClient();
        $client->restart();
        $client->request(
            'POST',
            $this->formatPraticaStatusUpdateRoute($pratica)
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testPraticaStatusAPIIsProtectedWithRoleChecking()
    {
        $user = $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        $client = static::createClient();
        $client->restart();
        $client->request(
            'POST',
            $this->formatPraticaStatusUpdateRoute($pratica),
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'gpa_no_role',
                'PHP_AUTH_PW' => 'gpapass',
            )
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testSchedaInformativaAPIIsProtected()
    {
        $enti = $this->createEnti();
        $erogatori = $this->createErogatoreWithEnti($enti);
        $servizio = $this->createServizioWithAssociatedErogatori([$erogatori]);
        $client = static::createClient();
        $client->restart();
        $client->request(
            'GET',
            $this->formatSchedaInformativaUpdateRoute($servizio, $enti[0])
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testSchedaInformativaAPIIsProtectedWithRoleChecking()
    {
        $enti = $this->createEnti();
        $erogatori = $this->createErogatoreWithEnti($enti);
        $servizio = $this->createServizioWithAssociatedErogatori([$erogatori]);
        $client = static::createClient();
        $client->restart();
        $client->request(
            'GET',
            $this->formatSchedaInformativaUpdateRoute($servizio, $enti[0]),
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'ez_no_role',
                'PHP_AUTH_PW' => 'ez',
            )
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }


    /**
     * @test
     */
    public function testSchedaInformativaAPIReturnsErrorIfMissingMandatoryQueryStringParameter()
    {
        $enti = $this->createEnti();
        $erogatori = $this->createErogatoreWithEnti($enti);
        $servizio = $this->createServizioWithAssociatedErogatori([$erogatori]);
        $client = static::createClient();
        $client->restart();
        $url = $this->formatSchedaInformativaUpdateRoute($servizio, $enti[0]);
        $client->request(
            'GET',
            $url,
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'ez',
                'PHP_AUTH_PW' => 'ez',
            )
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testQueryingTheEndpointAddsTheSchedaInformativaToServizioForEnte()
    {
        $remoteUrl = 'http://www.comune.trento.it/api/opendata/v2/content/read/629089';
        $enti = $this->createEnti();
        $ente = $enti[0];
        $erogatori = $this->createErogatoreWithEnti($enti);
        $servizio = $this->createServizioWithAssociatedErogatori([$erogatori]);
        $client = static::createClient();
        $client->restart();
        $url = $this->formatSchedaInformativaUpdateRoute($servizio, $ente, $remoteUrl);
        $client->request(
            'GET',
            $url,
            array(),
            array(),
            array(
                'PHP_AUTH_USER' => 'ez',
                'PHP_AUTH_PW' => 'ez',
            )
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

        $expectedContent = json_decode(file_get_contents($remoteUrl), true);
        $this->assertTrue(array_key_exists('data', $expectedContent));
        $this->assertTrue(array_key_exists('metadata', $expectedContent));

        $this->em->persist($servizio);
        $this->em->refresh($servizio);
        $schedaInformativa = $servizio->getSchedaInformativaPerEnte($ente);
        $this->assertEquals($expectedContent, $schedaInformativa);
    }

    /**
     * @param Pratica $pratica
     * @return string
     */
    private function formatPraticaStatusUpdateRoute(Pratica $pratica):string
    {
        $route = '/api/'.APIController::CURRENT_API_VERSION.'/pratica/'.$pratica->getId().'/status';

        return $route;
    }

    /**
     * @param Servizio $servizio
     * @param Ente $ente
     * @return string
     */
    private function formatSchedaInformativaUpdateRoute(Servizio $servizio, Ente $ente, $remoteUrl = null):string
    {
        $route = '/api/'.APIController::CURRENT_API_VERSION.'/schedaInformativa/'.$servizio->getSlug().'/'.$ente->getCodiceMeccanografico();

        $remoteUrl ? $route .= '?remote='.urlencode($remoteUrl) : null;

        return $route;
    }
}
