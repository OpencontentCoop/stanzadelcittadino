<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\PraticheController;
use AppBundle\Entity\Allegato;
use AppBundle\Entity\AsiloNido;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Ente;
use AppBundle\Entity\IscrizioneAsiloNido;
use AppBundle\Entity\ModuloCompilato;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Form\Base\DatiRichiedenteType;
use AppBundle\Logging\LogConstants;
use AppBundle\Services\CPSUserProvider;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class PraticaControllerTest
 */
class PraticaControllerTest extends AbstractAppTestCase
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

        system('rm -rf '.__DIR__."/../../../var/uploads/pratiche/allegati/*");

        $this->userProvider = $this->container->get('ocsdc.cps.userprovider');
        $this->em->getConnection()->executeQuery('DELETE FROM servizio_erogatori')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM erogatore_ente')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM ente_asili')->execute();
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Allegato::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(Servizio::class);
        $this->cleanDb(AsiloNido::class);
        $this->cleanDb(OperatoreUser::class);
        $this->cleanDb(Ente::class);
        $this->cleanDb(User::class);
    }

    /**
     * @test
     */
    public function testICanSeeMyPraticheInTableView()
    {
        $user = $this->createCPSUser();
        $this->setupPraticheForUser($user);

        $crawler = $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate('pratiche'));

        $this->doTestISeeMyNameAsLoggedInUser($user, $this->client->getResponse());

        $repo = $this->em->getRepository("AppBundle:Pratica");
        $praticheDraft = $repo->findBy(
            [
                'user' => $user,
                'status' => Pratica::STATUS_DRAFT
            ]
        );

        $pratichePending = $repo->findBy(
            [
                'user' => $user,
                'status' => [
                    Pratica::STATUS_PENDING,
                    Pratica::STATUS_SUBMITTED,
                    Pratica::STATUS_REGISTERED,
                ]
            ]
        );

        $praticheCompleted = $repo->findBy(
            [
                'user' => $user,
                'status' => Pratica::STATUS_COMPLETE
            ]
        );

        $praticheCancelled = $repo->findBy(
            [
                'user' => $user,
                'status' => Pratica::STATUS_CANCELLED
            ]
        );

        $praticheCount = $crawler->filter('.list.draft')->filter('.pratica')->count();
        $this->assertEquals(count($praticheDraft), $praticheCount);

        $praticheCount = $crawler->filter('.list.pending')->filter('.pratica')->count();
        $this->assertEquals(count($pratichePending), $praticheCount);

        $praticheCount = $crawler->filter('.list.completed')->filter('.pratica')->count();
        $this->assertEquals(count($praticheCompleted), $praticheCount);

        $praticheCount = $crawler->filter('.list.cancelled')->filter('.pratica')->count();
        $this->assertEquals(count($praticheCancelled), $praticheCount);
    }

    /**
     * @test
     */

    public function testAsLoggedUserISeeAllMyPratiche()
    {
        $myUser = $this->createCPSUser();
        $this->createPratiche($myUser);

        $otherUser = $this->createCPSUser();
        $this->createPratiche($otherUser);

        $repo = $this->em->getRepository("AppBundle:Pratica");
        $myUserPraticheCountAfterInsert = count($repo->findByUser($myUser));

        $otherUserPraticheCountAfterInsert = count($repo->findByUser($otherUser));
        $this->assertGreaterThan(0, $otherUserPraticheCountAfterInsert);

        $crawler = $this->clientRequestAsCPSUser($myUser, 'GET', '/pratiche/');

        $nodes = $crawler->filterXPath('//*[@class="pratica" and @data-user="'.$myUser->getId().'"]');
        //the data-user property gets rendered twice
        $renderedPraticheCount = $nodes->count();
        $this->assertEquals($myUserPraticheCountAfterInsert, $renderedPraticheCount);

        $renderedOtherUserPraticheCount = $crawler->filterXPath('//*[@data-user="'.$otherUser->getId().'"]')->count();
        $this->assertEquals(0, $renderedOtherUserPraticheCount);

        $this->doTestISeeMyNameAsLoggedInUser($myUser, $this->client->getResponse());
    }

    public function testAsACPSUserICanSeeTheExportedModuloDownloadLinkOnThePraticaDetailPage()
    {
        $myUser = $this->createCPSUser();
        $this->createPratiche($myUser);
        $repo = $this->em->getRepository("AppBundle:Pratica");
        /** @var Pratica $pratica */
        $pratica = $repo->findByUser($myUser)[0];
        $pratica->setStatus(Pratica::STATUS_SUBMITTED);

        $now = new \DateTime('now');
        $fileName = 'aaaaa.pdf';

        $moduloCompilato = new ModuloCompilato();
        $moduloCompilato->setFilename($fileName);
        $moduloCompilato->setOriginalFilename('Modulo Iscrizione Nido '.$now->format('Ymdhi'));
        $moduloCompilato->setDescription(
            $this->container->get('translator')->trans(
                'pratica.modulo.descrizione',
                [ 'nomeservizio' => $pratica->getServizio()->getName(), 'datacompilazione' => $now->format($this->container->getParameter('ocsdc_default_datetime_format')) ]
            )
        );

        $pratica->addModuloCompilato($moduloCompilato);

        $this->em->persist($moduloCompilato);
        $this->em->persist($pratica);
        $this->em->flush();

        $crawler = $this->clientRequestAsCPSUser($myUser, 'GET', '/pratiche/'.$pratica->getId());

        $nodes = $crawler->filterXPath('//*[@class="modulo"]');
        $renderedModuliCount = $nodes->count();

        $this->assertEquals($pratica->getModuliCompilati()->count(),$renderedModuliCount);

        $this->doTestISeeMyNameAsLoggedInUser($myUser, $this->client->getResponse());
    }

    /**
     * @test
     */
    public function testAsACPSUserICanSeeThePraticaDetailPage()
    {
        $myUser = $this->createCPSUser();
        $this->createPratiche($myUser);
        $repo = $this->em->getRepository("AppBundle:Pratica");
        /** @var Pratica $pratica */
        $pratica = $repo->findByUser($myUser)[0];

        $now = new \DateTime('now');
        $fileName = 'aaaaa.pdf';

        $moduloCompilato = new ModuloCompilato();
        $moduloCompilato->setFilename($fileName);
        $moduloCompilato->setOriginalFilename('Modulo Iscrizione Nido '.$now->format('Ymdhi'));
        $moduloCompilato->setDescription(
            $this->container->get('translator')->trans(
                'pratica.modulo.descrizione',
                [ 'nomeservizio' => $pratica->getServizio()->getName(), 'datacompilazione' => $now->format($this->container->getParameter('ocsdc_default_datetime_format')) ]
            )
        );

        $pratica->addModuloCompilato($moduloCompilato);

        $this->em->persist($moduloCompilato);


        $allegati = $this->setupNeededAllegatiForAllInvolvedUsers(3, $myUser);
        foreach ($allegati as $allegato) {
            $pratica->addAllegato($allegato);
        }

        $operatore = $this->createOperatoreUser('p', 'p');
        $pratica->setOperatore($operatore);
        $pratica->setStatus(Pratica::STATUS_PENDING);
        sleep(1);
        $pratica->setStatus(Pratica::STATUS_CANCELLED);

        $this->em->persist($pratica);
        $this->em->flush();
        $this->em->refresh($pratica);

        $crawler = $this->clientRequestAsCPSUser($myUser, 'GET', '/pratiche/'.$pratica->getId());

        $nodes = $crawler->filterXPath('//*[@class="modulo"]');
        $renderedModuliCount = $nodes->count();

        $this->assertEquals($pratica->getModuliCompilati()->count(),$renderedModuliCount);

        $nodes = $crawler->filterXPath('//*[contains(@class, "sidebar")]//*[contains(@class, "fa-calendar")]');
        $this->assertEquals($pratica->getStoricoStati()->count(), $nodes->count());

        //count allegati
        $nodes = $crawler->filterXPath('//*[@data-title="Nome del file"]');
        $this->assertEquals($pratica->getAllegati()->count(), $nodes->count());

        $this->doTestISeeMyNameAsLoggedInUser($myUser, $this->client->getResponse());
    }

    /**
     * @test
     */
    public function testAsACPSUserICanSeeTheEditLinkForAPraticaInDraftStatus()
    {
        $myUser = $this->createCPSUser();
        $pratica = $this->createPratica($myUser, null, Pratica::STATUS_DRAFT);

        $crawler = $this->clientRequestAsCPSUser($myUser, 'GET', '/pratiche/'.$pratica->getId());

        $nodes = $crawler->filterXPath('//*[@data-action="edit_draft"]');
        $this->assertEquals(1, $nodes->count());

        $this->doTestISeeMyNameAsLoggedInUser($myUser, $this->client->getResponse());
    }

    /**
     * @test
     */
    public function testAsACPSUserICannotSeePraticheOfOthersUsers()
    {
        $myUser = $this->createCPSUser();
        $otherUser = $this->createCPSUser();
        $pratica = $this->createPratica($otherUser, null, Pratica::STATUS_DRAFT);

        $this->clientRequestAsCPSUser($myUser, 'GET', '/pratiche/'.$pratica->getId());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

    }

    /**
     * @test
     */
    public function testANewPraticaIsPersistedWhenIStartTheFormApplicationAsLoggedUser()
    {
        $mockLogger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $mockLogger->expects($this->exactly(1))
            ->method('info')
            ->with(LogConstants::PRATICA_CREATED);

        $this->container->set('logger', $mockLogger);
        $user = $this->createCPSUser();

        $praticheRepository = $this->em->getRepository('AppBundle:Pratica');
        $tutteLePratiche = count($praticheRepository->findAll());
        $miePratiche = count($praticheRepository->findByUser($user));

        $servizio = $this->createServizioWithAssociatedErogatori([], 'Terzo servizio');

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));

        $tutteLePraticheNew = count($praticheRepository->findAll());
        $miePraticheNew = count($praticheRepository->findByUser($user));

        $this->assertEquals(++$tutteLePratiche, $tutteLePraticheNew);
        $this->assertEquals(++$miePratiche, $miePraticheNew);
    }

    /**
     * @test
     */
    public function testANewPraticaIsPersistedWithEnteSetFromLinkWhenIStartTheFormApplicationAsLoggedUser()
    {
        $mockLogger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $mockLogger->expects($this->exactly(1))
            ->method('info')
            ->with(LogConstants::PRATICA_CREATED);

        $this->container->set('logger', $mockLogger);
        $user = $this->createCPSUser();

        $praticheRepository = $this->em->getRepository('AppBundle:Pratica');
        $tutteLePratiche = count($praticheRepository->findAll());
        $miePratiche = count($praticheRepository->findByUser($user));

        $servizio = $this->createServizioWithAssociatedErogatori([], 'Terzo servizio');

        $ente = $this->createEnti()[0];
        $enteSlug = $ente->getSlug();

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            [
                'servizio' => $servizio->getSlug(),
                PraticheController::ENTE_SLUG_QUERY_PARAMETER => $enteSlug,
            ]
        ));

        $newPraticaUrl = $this->client->getResponse()->headers->get('Location');
        $newPraticaParameters = $this->router->match($newPraticaUrl);
        $pratica = $praticheRepository->find($newPraticaParameters['pratica']);
        $this->assertEquals($enteSlug, $pratica->getEnte()->getSlug());

        $tutteLePraticheNew = count($praticheRepository->findAll());
        $miePraticheNew = count($praticheRepository->findByUser($user));

        $this->assertEquals($tutteLePratiche + 1, $tutteLePraticheNew);
        $this->assertEquals($miePratiche + 1, $miePraticheNew);
    }

    /**
     * @test
     */
    public function testANewPraticaIsPersistedWithNoEnteSetIfLinkHasNonMatchingSlugWhenIStartTheFormApplicationAsLoggedUser()
    {
        $mockLogger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $mockLogger->expects($this->exactly(2))
            ->method('info')
            ->with($this->callback(function ($arg) {

                return in_array(
                    $arg,
                    [
                    LogConstants::PRATICA_CREATED,
                    LogConstants::PRATICA_WRONG_ENTE_REQUESTED,
                    ]
                );
            }));

        $this->container->set('logger', $mockLogger);
        $user = $this->createCPSUser();

        $praticheRepository = $this->em->getRepository('AppBundle:Pratica');
        $tutteLePratiche = count($praticheRepository->findAll());
        $miePratiche = count($praticheRepository->findByUser($user));

        $servizio = $this->createServizioWithAssociatedErogatori([], 'Terzo servizio');

        //Lo slug viene passato da gedmo sluggable
        $enteSlug = 'roncella-ionica';


        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            [
                'servizio' => $servizio->getSlug(),
                PraticheController::ENTE_SLUG_QUERY_PARAMETER => $enteSlug,
            ]
        ));

        $newPraticaUrl = $this->client->getResponse()->headers->get('Location');
        $newPraticaParameters = $this->router->match($newPraticaUrl);
        $pratica = $praticheRepository->find($newPraticaParameters['pratica']);
        $this->assertNull($pratica->getEnte());

        $tutteLePraticheNew = count($praticheRepository->findAll());
        $miePraticheNew = count($praticheRepository->findByUser($user));

        $this->assertEquals($tutteLePratiche + 1, $tutteLePraticheNew);
        $this->assertEquals($miePratiche + 1, $miePraticheNew);
    }

    /**
     * @test
     */
    public function testISeeSceltaComuneFormWhenIStartTheFormAsLoggedUser()
    {
        $mockLogger = $this->getMockLogger();
        $mockLogger->expects($this->exactly(2))
            ->method('info')
            ->with($this->callback(function ($subject) {
                $expectedArgs = [
                    LogConstants::PRATICA_CREATED,
                    LogConstants::PRATICA_COMPILING_STEP,
                ];

                return in_array($subject, $expectedArgs);
            }));

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockLogger) {
            $kernel->getContainer()->set('logger', $mockLogger);
        });
        $user = $this->createCPSUser();

        $servizio = $this->createServizioWithAssociatedErogatori([], 'Altro servizio');

        $this->client->followRedirects();
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));

        $this->assertContains('pratica_seleziona_ente', $this->client->getResponse()->getContent());

        $this->doTestISeeMyNameAsLoggedInUser($user, $this->client->getResponse());
    }

    /**
     * @test
     */
    public function testInputFieldsForMyCPSDataAreDisabled()
    {
        $ente = $this->createEnteWithAsili();
        $erogatore = $this->createErogatoreWithEnti([$ente]);

        $fqcn = IscrizioneAsiloNido::class;
        $flow = 'ocsdc.form.flow.asilonido';
        $servizio = $this->createServizioWithErogatore($erogatore, 'Iscrizione Asilo Nido', $fqcn, $flow);

        $user = $this->createCPSUser();

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $crawler = $this->client->followRedirect();

        $currentUriParts = explode('/', $this->client->getHistory()->current()->getUri());
        $currentPraticaId = array_pop($currentUriParts);
        $currentPratica = $this->em->getRepository('AppBundle:IscrizioneAsiloNido')->find($currentPraticaId);
        $this->assertEquals(get_class($currentPratica), IscrizioneAsiloNido::class);

        $nextButton = $this->translator->trans('button.next', [], 'CraueFormFlowBundle');
        $finishButton = $this->translator->trans('button.finish', [], 'CraueFormFlowBundle');

        // Selezione del comune
        $this->selezioneComune($crawler, $nextButton, $ente, $form, $currentPratica, $erogatore);

        // Accettazioni istruzioni
        $this->accettazioneIstruzioni($crawler, $nextButton, $form);

        // Selezione del asilo
        $this->nextStep($crawler, $nextButton, $form);

        // Termini di fruizione della struttura
        $this->nextStep($crawler, $nextButton, $form);

        // Selezione orari
        $form = $crawler->selectButton($nextButton)->form(array(
            'iscrizione_asilo_nido_orari[periodo_iscrizione_da]' => '01-09-2016',
            'iscrizione_asilo_nido_orari[periodo_iscrizione_a]' => '01-09-2017'
        ));
        $crawler = $this->client->submit($form);

        // Dati richiedente
        $fillData = array();
        $crawler->filter('form[name="pratica_richiedente"] input[type="text"]')
            ->each(function ($node, $i) use (&$fillData) {
                self::fillFormInputWithDummyText($node, $i, $fillData);
            });
        $form = $crawler->selectButton($nextButton)->form();

        foreach (DatiRichiedenteType::CAMPI_RICHIEDENTE as $campo => $statoDisabledAtteso) {
            $field = $form->get('pratica_richiedente['.$campo.']');
            switch ($campo) {
                case 'richiedente_telefono':
                    $statoDisabledAtteso = $user->getTelefono() == null ? false : true;
                    break;
                case 'richiedente_email':
                    $statoDisabledAtteso = $user->getEmail() == null ? false : true;
                    break;
                default:
                    break;
            }
            $this->assertEquals($statoDisabledAtteso, $field->isDisabled(), 'Il campo '.$field->getName().' doveva essere disabled: '.$statoDisabledAtteso);
        }

        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @test
     */
    public function testIgetRedirectedIfITryToCreateANewPraticaOnAServiceWithPraticheInDraft()
    {
        $ente = $this->createEnteWithAsili();
        $erogatore = $this->createErogatoreWithEnti([$ente]);
        $fqcn = IscrizioneAsiloNido::class;
        $flow = 'ocsdc.form.flow.asilonido';
        $servizio = $this->createServizioWithErogatore($erogatore, 'Iscrizione Asilo Nido', $fqcn, $flow);
        $user = $this->createCPSUser();

        $this->createPratica($user, null, Pratica::STATUS_DRAFT, $erogatore, $servizio);

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $this->assertEquals(
            $this->client->getResponse()->headers->get('location'),
            $this->router->generate(
                'pratiche_list_draft',
                ['servizio' => $servizio->getSlug()]
            )
        );
    }

    /**
     * @test
     */
    public function testISeeChoiceOfEnteWhenIStartTheFormAsLoggedUser()
    {
        $mockLogger = $this->getMockLogger();
        $mockLogger->expects($this->exactly(2))
            ->method('info')
            ->with($this->callback(function ($subject) {
                $expectedArgs = [
                    LogConstants::PRATICA_CREATED,
                    LogConstants::PRATICA_COMPILING_STEP,
                ];

                return in_array($subject, $expectedArgs);
            }));

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockLogger) {
            $kernel->getContainer()->set('logger', $mockLogger);
        });

        $user = $this->createCPSUser();

        $ente = $this->createEnteWithAsili('L781');
        $erogatore = $this->createErogatoreWithEnti([$ente]);
        $ente1 = $this->createEnteWithAsili('L782');
        $erogatore1 = $this->createErogatoreWithEnti([$ente1]);
        $ente2 = $this->createEnteWithAsili('L783');
        $erogatore2 = $this->createErogatoreWithEnti([$ente2]);

        $servizio = $this->createServizioWithAssociatedErogatori([$erogatore, $erogatore1, $erogatore2], 'Altro servizio');

        $this->client->followRedirects();
        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));

        $this->assertContains('pratica_seleziona_ente', $this->client->getResponse()->getContent());
    }
}
