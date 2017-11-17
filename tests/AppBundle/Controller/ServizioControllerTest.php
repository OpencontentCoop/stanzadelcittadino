<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Ente;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Services\CPSUserProvider;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class ServizioControllerTest
 *
 * @package Tests\AppBundle\Controller
 */
class ServizioControllerTest extends AbstractAppTestCase
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
        $this->em->getConnection()->executeQuery('DELETE FROM servizio_erogatori')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM erogatore_ente')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM ente_asili')->execute();
        $this->cleanDb(Allegato::class);
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(Servizio::class);
        $this->cleanDb(User::class);
        $this->cleanDb(Ente::class);
    }

    /**
     * @test
     */
    public function testIndexAsLoggedInUser()
    {
        $user = $this->createCPSUser();

        $repo = $this->em->getRepository("AppBundle:Servizio");

        $this->createServizioWithAssociatedErogatori([], 'Primo servizio');

        $serviceCountAfterInsert = count($repo->findAll());

        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('servizi_list'));
        $renderedServicesCount = $crawler->filter('.servizio')->count();
        $this->assertEquals( $serviceCountAfterInsert, $renderedServicesCount );
    }

    /**
     * @test
     */
    public function testICanSeeAServiceDetailAsLoggedInUser()
    {
        $user = $this->createCPSUser();

        $servizio = $this->createServizioWithAssociatedErogatori([], 'Secondo servizio');

        $servizioDetailUrl = $this->router->generate('servizi_show', ['slug' => $servizio->getSlug()], Router::ABSOLUTE_URL);

        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('servizi_list'));
        $detailLink = $crawler->selectLink($servizio->getName())->link()->getUri();

        $this->assertEquals($servizioDetailUrl, $detailLink);

        $this->clientRequestAsCPSUser($user, 'GET', $detailLink);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    public function testICanSeeANewPraticaLinkInServiceDetailsAsLoggedInUser()
    {
        $user = $this->createCPSUser();
        $erogatore = $this->createErogatoreWithEnti($this->createEnti());
        $servizio = $this->createServizioWithAssociatedErogatori([$erogatore], 'Terzo servizio');

        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('servizi_show', ['slug' => $servizio->getSlug()], Router::ABSOLUTE_URL));

        $newPraticaUrl = $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        );

        $this->assertContains($newPraticaUrl, $this->client->getResponse()->getContent(), 'Stringa non trovata');

    }

    /**
     * @test
     */
    public function testICanSeeServicesAsAnonymousUser()
    {
        $user = $this->createCPSUser();
        $this->setupPraticheForUser($user);

        $numberServices = rand(1, 10);

        for ($i = 1; $i <= $numberServices; $i++) {
            $this->createServizioWithAssociatedErogatori([], 'Servizio'.$i);
        }
        $this->em->flush();

        $repo = $this->em->getRepository("AppBundle:Servizio");
        $serviceCountAfterInsert = count($repo->findAll());
        $crawler = $this->client->request('GET', $this->router->generate('servizi_list'));
        $renderedServices = $crawler->filter('.servizio');
        $renderedServicesCount = $renderedServices->count();
        $this->assertEquals($serviceCountAfterInsert, $renderedServicesCount);
    }
}
