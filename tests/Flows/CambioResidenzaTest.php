<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\Flows;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\AsiloNido;
use AppBundle\Entity\CambioResidenza;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Ente;
use AppBundle\Entity\ModuloCompilato;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Form\CambioResidenza\TipologiaOccupazioneDettaglioType;
use AppBundle\Services\CPSUserProvider;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\All;
use Tests\AppBundle\Base\AbstractAppTestCase;

class CambioResidenzaTest extends AbstractAppTestCase
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
     * @var string[]
     */
    private $expectedAttachmentDescriptions = array();

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

    public function cambioResidenzaDataProvider()
    {
        return array(
            array(CambioResidenza::PROVENIENZA_COMUNE, CambioResidenza::OCCUPAZIONE_PROPRIETARIO),
            array(CambioResidenza::PROVENIENZA_ALTRO_COMUNE, CambioResidenza::OCCUPAZIONE_PROPRIETARIO),
            array(CambioResidenza::PROVENIENZA_ESTERO, CambioResidenza::OCCUPAZIONE_PROPRIETARIO),
            array(CambioResidenza::PROVENIENZA_AIRE, CambioResidenza::OCCUPAZIONE_PROPRIETARIO),
            array(CambioResidenza::PROVENIENZA_ALTRO, CambioResidenza::OCCUPAZIONE_PROPRIETARIO),
            array(CambioResidenza::PROVENIENZA_COMUNE, CambioResidenza::OCCUPAZIONE_LOCAZIONE),
            array(CambioResidenza::PROVENIENZA_COMUNE, CambioResidenza::OCCUPAZIONE_LOCAZIONE_ERP),
            array(CambioResidenza::PROVENIENZA_COMUNE, CambioResidenza::OCCUPAZIONE_COMODATO),
            array(CambioResidenza::PROVENIENZA_COMUNE, CambioResidenza::OCCUPAZIONE_USUFRUTTO),
            array(CambioResidenza::PROVENIENZA_COMUNE, CambioResidenza::OCCUPAZIONE_AUTOCERTIFICAZIONE),
        );
    }

    /**
     * @dataProvider cambioResidenzaDataProvider
     *
     * @param $provenienza
     * @param $occupazione
     */
    public function testICanFillOutTheCambioResidenzaAsLoggedUser($provenienza, $occupazione)
    {
        $fqcn = CambioResidenza::class;
        $flow = 'ocsdc.form.flow.cambioresidenza';
        $entityName = 'AppBundle:CambioResidenza';
        $fillData = array();

        // ente
        $ente = $this->createEnti()[0];
        $erogatore = $this->createErogatoreWithEnti([$ente]);

        // servizio
        $servizio = $this->createServizioWithErogatore($erogatore, 'Cambio residenza', $fqcn, $flow);

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

        $this->dichiarazioneProvenienza($provenienza, $crawler, $nextButton, $fillData, $form);

        $this->datiResidenza($crawler, $nextButton, $fillData, $form);

        $this->composizioneNucleoFamiliare($crawler, $nextButton, $form, 0, 5);

        $this->composizioneNucleoFamiliare(
            $crawler, $nextButton, $form, 0, 5,
            '.persone_residenti',
            'cambio_residenza_persone_attualmente_residenti[persone_residenti]'
        );

        $this->tipologiaOccupazione($occupazione, $crawler, $nextButton, $fillData, $form);

        $this->informazioniAccertamento($crawler, $nextButton, $fillData, $form);

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

        /** @var Allegato[] $allegati */
        $allegati = $currentPratica->getAllegati()->toArray();
        $this->assertEquals(count($this->expectedAttachmentDescriptions), count($allegati));
        foreach($allegati as $allegato){
            $this->assertContains($allegato->getDescription(), $this->expectedAttachmentDescriptions);
        }

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
     * @param $identifier
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function dichiarazioneProvenienza($identifier, &$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $form = $crawler->selectButton($nextButton)->form(array(
            'cambio_residenza_dichiarazione_provenienza[provenienza]' => $identifier,
        ));
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");

        if ($identifier !== CambioResidenza::PROVENIENZA_COMUNE) {
            $this->dichiarazioneProvenienzaDettaglio($identifier, $crawler, $nextButton, $fillData, $form);
        }
    }

    /**
     * @param $identifier
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function dichiarazioneProvenienzaDettaglio($identifier, &$crawler, $nextButton, &$fillData, &$form)
    {
        $filterString = null;
        switch ($identifier) {
            case CambioResidenza::PROVENIENZA_ALTRO_COMUNE:
                $filterString = 'input[name="cambio_residenza_dichiarazione_provenienza_dettaglio[comuneDiProvenienza]"]';
                break;

            case CambioResidenza::PROVENIENZA_ESTERO:
                $filterString = 'input[name="cambio_residenza_dichiarazione_provenienza_dettaglio[statoEsteroDiProvenienza]"]';
                break;

            case CambioResidenza::PROVENIENZA_AIRE:
                $filterString = 'input[name="cambio_residenza_dichiarazione_provenienza_dettaglio[statoEsteroDiProvenienza]"], input[name="cambio_residenza_dichiarazione_provenienza_dettaglio[comuneEsteroDiProvenienza]"]';
                break;

            case CambioResidenza::PROVENIENZA_ALTRO:
                $filterString = 'textarea[name="cambio_residenza_dichiarazione_provenienza_dettaglio[altraProvenienza]"]';
                break;
        }

        if ($filterString) {
            $crawler->filter($filterString)
                    ->each(function ($node, $i) use (&$fillData) {
                        self::fillFormInputWithDummyText($node, $i, $fillData);
                    });

            $form = $crawler->selectButton($nextButton)->form($fillData);
            $crawler = $this->client->submit($form);
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        }
    }

    /**
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function datiResidenza(&$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $crawler->filter('form[name="cambio_residenza_dati_residenza"] input[type="text"]')
                ->each(function ($node, $i) use (&$fillData) {
                    self::fillFormInputWithDummyText($node, $i, $fillData);
                });

        $form = $crawler->selectButton($nextButton)->form($fillData);
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @param $identifier
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function tipologiaOccupazione($identifier, &$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $form = $crawler->selectButton($nextButton)->form(array(
            'cambio_residenza_tipologia_occupazione[tipoOccupazione]' => $identifier,
        ));

        $allegato = null;
        if ($identifier == CambioResidenza::OCCUPAZIONE_LOCAZIONE_ERP) {
            $allegato = $this->addAllegato(TipologiaOccupazioneDettaglioType::OCCUPAZIONE_LOCAZIONE_ERP_FILE_DESCRIPTION);
        } elseif ($identifier == CambioResidenza::OCCUPAZIONE_AUTOCERTIFICAZIONE) {
            $allegato = $this->addAllegato(TipologiaOccupazioneDettaglioType::OCCUPAZIONE_AUTOCERTIFICAZIONE_FILE_DESCRIPTION);
        }

        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");

        $this->tipologiaOccupazioneDettaglio($identifier, $crawler, $nextButton, $fillData, $form, $allegato);
    }

    /**
     * @param $identifier
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     * @param Allegato $allegato
     */
    private function tipologiaOccupazioneDettaglio(
        $identifier,
        &$crawler,
        $nextButton,
        &$fillData,
        &$form,
        Allegato $allegato = null
    ) {
        $fillData = array();
        $crawler->filter('form[name="cambio_residenza_tipologia_occupazione_dettaglio"] input[type="text"]')
                ->each(function ($node, $i) use (&$fillData) {
                    self::fillFormInputWithDummyText($node, $i, $fillData);
                });

        switch ($identifier) {
            case CambioResidenza::OCCUPAZIONE_LOCAZIONE:
            case CambioResidenza::OCCUPAZIONE_COMODATO:
                $fillData['cambio_residenza_tipologia_occupazione_dettaglio[contrattoData]'] = '01-09-2016';
                break;

            case CambioResidenza::OCCUPAZIONE_USUFRUTTO:
                $fillData['cambio_residenza_tipologia_occupazione_dettaglio[usufruttuarioInfo]'] = 'test';
                break;

            case CambioResidenza::OCCUPAZIONE_LOCAZIONE_ERP:
                $fillData['cambio_residenza_tipologia_occupazione_dettaglio[verbaleConsegna][choose]'] = $allegato->getId();
                break;

            case CambioResidenza::OCCUPAZIONE_AUTOCERTIFICAZIONE:
                $fillData['cambio_residenza_tipologia_occupazione_dettaglio[autocertificazione][choose]'] = $allegato->getId();
                break;
        }

        $form = $crawler->selectButton($nextButton)->form($fillData);
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    private function addAllegato($description)
    {
        $user = $this->em->getRepository('AppBundle:CPSUser')->find($this->currentUser->getId());

        $allegato = new Allegato();
        $allegato->setOwner($user);
        $allegato->setDescription($description);
        $allegato->setFilename('somefile.txt');
        $allegato->setOriginalFilename('somefile.txt');
        $this->em->persist($allegato);
        $this->em->flush();

        $this->expectedAttachmentDescriptions[] = $description;

        return $allegato;
    }

    /**
     * @param Crawler $crawler
     * @param $nextButton
     * @param $fillData
     * @param $form
     */
    private function informazioniAccertamento(&$crawler, $nextButton, &$fillData, &$form)
    {
        $fillData = array();
        $crawler->filter('form[name="cambio_residenza_informazioni_accertamento"] textarea')
                ->each(function ($node, $i) use (&$fillData) {
                    self::fillFormInputWithDummyText($node, $i, $fillData);
                });


        $form = $crawler->selectButton($nextButton)->form($fillData);
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }
}
