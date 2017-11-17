<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\TerminiUtilizzo;
use AppBundle\Entity\User;
use AppBundle\Services\CPSUserProvider;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\VarDumper\VarDumper;
use Tests\AppBundle\Base\AbstractAppTestCase;

class DefaultControllerTest extends AbstractAppTestCase
{

    /**
     * @var CPSUserProvider
     */
    protected $userProvider;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->userProvider = $this->container->get('ocsdc.cps.userprovider');
        $this->cleanDb(Allegato::class);
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(User::class);
        $this->cleanDb(TerminiUtilizzo::class);
    }

    /**
     * @test
     */
    public function testIndex()
    {
        $this->client->request('GET', '/');

        $this->assertContains('Stanza del cittadino', $this->client->getResponse()->getContent());
    }

    /**
     * @test
     */
    public function testPrivacy()
    {
        $this->client->request('GET', '/');
        $this->assertContains('Informativa per il trattamento dati personali', $this->client->getResponse()->getContent());

        $this->client->request('GET', '/privacy');
        $this->assertContains('Informativa per il trattamento dati personali', $this->client->getResponse()->getContent());
    }

    /**
     * @dataProvider protectedRoutesProvider
     * @param array $route
     */
    public function testIGetAccessDeniedErrorWhenAccessProtectedResourcesAsAnonymousUser($route)
    {
        $this->client->request('GET', $route);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function protectedRoutesProvider()
    {
        return array(
            array('/pratiche/'),
        );
    }

    /**
     * @test
     */
    public function testIAmRedirectedToTermAcceptPageWhenIAccessForTheFirstTimeAsLoggedInUser()
    {
        $this->createDefaultTerm(true);
        $user = $this->createCPSUser(false, true);

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('pratiche'));

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals($this->router->generate('terms_accept', ['r' => 'pratiche']), $response->headers->get('location'));
    }

    /**
     * @test
     */
    public function testIAmRedirectedToUserProfilePageWhenIAcceptTermsForTheFirstTimeAndMyProfileIsNotCompleteAsLoggedInUser()
    {
        $user = $this->createCPSUser(true, false);

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('pratiche'));

        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals($this->router->generate('user_profile', ['r' => 'pratiche']), $response->headers->get('location'));
    }

    /**
     * @test
     */
    public function testIAmRedirectedToOriginalPageWhenIAcceptTermsForTheFirstTimeAsLoggedInUser()
    {
        $this->createDefaultTerm(true);
        $user = $this->createCPSUser(false, true);

        $this->client->followRedirects();
        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('pratiche'));
        $form = $crawler->selectButton($this->translator->trans('salva'))->form();
        $this->client->submit($form);
        $this->assertEquals(
            $this->client->getRequest()->getUri(),
            $this->router->generate('pratiche', [], Router::ABSOLUTE_URL)
        );
    }

    /**
     * @test
     */
    public function testIAmRedirectedToOriginalPageWhenIAcceptTermsAndCompleteProfileForTheFirstTimeAsLoggedInUser()
    {
        $user = $this->createCPSUser(false, false);

        $this->client->followRedirects();
        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('pratiche'));
        $form = $crawler->selectButton($this->translator->trans('salva'))->form();
        $crawler = $this->client->submit($form);

        $this->assertEquals(
            $this->client->getRequest()->getUri(),
            $this->router->generate('user_profile', ['r' => 'pratiche'], Router::ABSOLUTE_URL)
        );

        $fillData = array();
        $crawler->filter('form[id="edit_user_profile"] input')
                ->each(function (Crawler $node, $i) use (&$fillData) {
                    if ($node->attr('readonly') === null) {
                        $fillData[$node->attr('name')] = $node->attr('type') == 'email' ? 'test@test.it' : 'test';
                    }
                });
        $form = $crawler->selectButton($this->translator->trans('user.profile.salva_informazioni_profilo'))->form($fillData);
        $crawler = $this->client->submit($form);

        $this->assertEquals(
            $this->client->getRequest()->getUri(),
            $this->router->generate('pratiche', [], Router::ABSOLUTE_URL)
        );
    }

    /**
     * @test
     */
    public function testOriginalQueryParametersArepreservedWhenIAmRedirectedToOriginalPageAfterAcceptingTermsForTheFirstTimeAsLoggedInUser()
    {
        $this->createDefaultTerm(true);
        $user = $this->createCPSUser(false);

        $params = [
            'servizio' => 'someservice',
            'a' => 'b',
            'c' => 'd',
            'e' => 11,
        ];

        $this->client->followRedirects();
        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('pratiche_new', $params));
        $form = $crawler->selectButton($this->translator->trans('salva'))->form();
        $this->client->submit($form);
        $this->assertEquals(
            $this->client->getRequest()->getUri(),
            $this->router->generate('pratiche_new', $params, Router::ABSOLUTE_URL)
        );
        unset($params['servizio']);
        $this->assertEquals(
            $params,
            $this->client->getRequest()->query->all()
        );
    }

    /**
     * @test
     */
    public function testIAmNotRedirectedToTermAcceptPageIfTermsAreAccepted()
    {
        $user = $this->createCPSUser();

        $this->clientRequestAsCPSUser($user, 'GET', '/');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testICanAcceptTermsConditionsAsLoggedInUser()
    {
        $mockLogger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $mockLogger->expects($this->exactly(2))->method('info');

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockLogger) {
            $kernel->getContainer()->set('logger', $mockLogger);
        });

        $this->createDefaultTerm(true);

        $user = $this->createCPSUser(false);

        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('terms_accept'));

        $this->doTestISeeMyNameAsLoggedInUser($user, $this->client->getResponse());

        $form = $crawler->selectButton($this->translator->trans('salva'))
            ->form();

        $this->client->submit($form);

        $this->em->refresh($user);

        $this->assertTrue($this->container->get('ocsdc.cps.terms_acceptance_checker')->checkIfUserHasAcceptedMandatoryTerms($user));
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
