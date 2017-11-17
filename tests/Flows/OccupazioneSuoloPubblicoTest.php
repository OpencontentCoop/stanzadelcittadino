<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\Flows;

use AppBundle\Entity\OccupazioneSuoloPubblico;
use AppBundle\Entity\Allegato;
use AppBundle\Entity\AsiloNido;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Ente;
use AppBundle\Entity\ModuloCompilato;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Services\CPSUserProvider;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\All;
use Tests\AppBundle\Base\AbstractAppTestCase;

class OccupazioneSuoloPubblicoTest extends AbstractAppTestCase
{

    /**
     * @var CPSUserProvider
     */
    protected $userProvider;

    /**
     * @var CPSUser
     */
    private $currentUser;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        system('rm -rf ' . __DIR__ . "/../../../var/uploads/pratiche/allegati/*");

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

        $this->expectedAttachmentDescriptions = array();
    }

    public function occupazioneSuoloPubblicoDataProvider()
    {
        return array(
            array(OccupazioneSuoloPubblico::TIPOLOGIA_PERMANENTE),
            array(OccupazioneSuoloPubblico::TIPOLOGIA_TEMPORANEA)
        );
    }

    /**
     * @dataProvider occupazioneSuoloPubblicoDataProvider
     *
     * @param $tipologia

     */
    public function testICanFillOutTheOccupazioneSuoloPubblicoAsLoggedUser($tipologia)
    {
        $fqcn = OccupazioneSuoloPubblico::class;
        $flow = 'ocsdc.form.flow.occupazionesuolopubblico';
        $entityName = 'AppBundle:OccupazioneSuoloPubblico';
        $fillData = array();

        // ente
        $ente = $this->createEnti()[0];
        $erogatore = $this->createErogatoreWithEnti([$ente]);

        // servizio
        $servizio = $this->createServizioWithErogatore($erogatore, 'Occupazione Suolo Pubblico', $fqcn, $flow);

        // utente
        $this->currentUser = $this->createCPSUser();

        // mailer
        $mockMailer = $this->setupSwiftmailerMock([$this->currentUser]);
        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockMailer) {
            $kernel->getContainer()->set('swiftmailer.mailer.default', $mockMailer);
        });

        // esecuzione della richiesta
        $this->clientRequestAsCPSUser($this->currentUser, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));

        // deve redirezionare a compila
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $crawler = $this->client->followRedirect();

        // ricavo la pratica dal percorso
        $currentUriParts = explode('/', $this->client->getHistory()->current()->getUri());
        $currentPraticaId = array_pop($currentUriParts);
        $currentPratica = $this->em->getRepository($entityName)->find($currentPraticaId);
        $this->assertEquals($fqcn, get_class($currentPratica));
        $this->assertEquals(0, $currentPratica->getModuliCompilati()->count());

        $nextButton = $this->translator->trans('button.next', [], 'CraueFormFlowBundle');
        $finishButton = $this->translator->trans('button.finish', [], 'CraueFormFlowBundle');

        $this->selezioneComune($crawler, $nextButton, $ente, $form, $currentPratica, $erogatore);

        $this->accettazioneIstruzioni($crawler, $nextButton, $form);

        $this->datiRichiedente($crawler, $nextButton, $fillData, $form);

        $this->orgRichiedente($crawler, $nextButton, $fillData, $form);

        $this->occupazione($crawler, $nextButton, $fillData, $form);

        $this->tipologiaOccupazione($tipologia, $crawler, $nextButton, $fillData, $form);

        if ($tipologia == OccupazioneSuoloPubblico::TIPOLOGIA_TEMPORANEA) {
            $this->tempoOccupazione($crawler, $nextButton, $fillData, $form);
        }

        $form = $crawler->selectButton($finishButton)->form();
        $this->client->submit($form);

        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code");
        $this->client->followRedirect();

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code");
        $this->assertContains($currentPraticaId, $this->client->getRequest()->getRequestUri());

        $this->em->refresh($currentPratica);

        $this->assertEquals(
            $currentPratica->getRichiedenteNome(),
            $this->currentUser->getNome()
        );

        //modulo stampato
        $this->assertEquals(1, $currentPratica->getModuliCompilati()->count());
        /** @var ModuloCompilato $pdfExportedForm */
        $pdfExportedForm = $currentPratica->getModuliCompilati()->get(0);
        $this->assertNotNull($pdfExportedForm);
        $this->assertTrue($pdfExportedForm instanceof ModuloCompilato);

        $this->assertNotNull($currentPratica->getSubmissionTime());
        $submissionDate = new \DateTime();
        $submissionDate->setTimestamp($currentPratica->getSubmissionTime());

        $this->assertEquals('Modulo ' . $currentPratica->getServizio()->getName() . ' compilato il ' . $submissionDate->format($this->container->getParameter('ocsdc_default_datetime_format')),
            $pdfExportedForm->getDescription());
    }

    /**
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function orgRichiedente(&$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $crawler->filter('form[name="occupazione_suolo_pubblico_org_richiedente"] input[type="text"]')
            ->each(function ($node, $i) use (&$fillData) {
                self::fillFormInputWithDummyText($node, $i, $fillData);
            });

        $form = $crawler->selectButton($nextButton)->form($fillData);
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function occupazione(&$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $crawler->filter('form[name="occupazione_suolo_pubblico_occupazione"] input[type="text"]')
            ->each(function ($node, $i) use (&$fillData) {
                self::fillFormInputWithDummyText($node, $i, $fillData);
            });

        $form = $crawler->selectButton($nextButton)->form($fillData);
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function tipologiaOccupazione($identifier, &$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $form = $crawler->selectButton($nextButton)->form(array(
            'occupazione_suolo_pubblico_tipologia_occupazione[tipologiaOccupazione]' => $identifier,
        ));
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function tempoOccupazione(&$crawler, $nextButton, &$fillData, &$form)
    {
        $form = $crawler->selectButton($nextButton)->form(array(
            'occupazione_suolo_pubblico_tempo_occupazione[inizioOccupazioneGiorno]' => '01-09-2016',
            'occupazione_suolo_pubblico_tempo_occupazione[inizioOccupazioneOra]' => '12:00',
            'occupazione_suolo_pubblico_tempo_occupazione[fineOccupazioneGiorno]' => '01-09-2017',
            'occupazione_suolo_pubblico_tempo_occupazione[fineOccupazioneOra]' => '12:00',
        ));

        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

}
