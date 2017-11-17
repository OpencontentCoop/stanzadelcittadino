<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\Flows;

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
use AppBundle\Form\IscrizioneAsiloNido\IscrizioneAsiloNidoFlow;
use AppBundle\Logging\LogConstants;
use AppBundle\Services\CPSUserProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\AppBundle\Base\AbstractAppTestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PraticaControllerTest
 */
class RipresaModuloDraftTest extends AbstractAppTestCase
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
    public function testICanResumeFillingTheFormFromTheLastFilledStep()
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
        $this->assertEquals(0, $currentPratica->getModuliCompilati()->count());

        $nextButton = $this->translator->trans('button.next', [], 'CraueFormFlowBundle');
        $finishButton = $this->translator->trans('button.finish', [], 'CraueFormFlowBundle');

        $this->selezioneComune($crawler, $nextButton, $ente, $form, $currentPratica, $erogatore);
        $this->accettazioneIstruzioni($crawler, $nextButton, $form);
        /** @var AsiloNido $asiloSelected*/
        $this->selezioneAsilo($ente, $crawler, $nextButton, $asiloSelected, $form);
        $this->terminiFruizione($asiloSelected, $crawler, $nextButton, $form);

        $this->em->persist($currentPratica);
        $this->em->refresh($currentPratica);
        $this->assertEquals(IscrizioneAsiloNidoFlow::STEP_ACCETTAZIONE_UTILIZZO_NIDO, $currentPratica->getLastCompiledStep());

        $this->client->request('GET', $this->router->generate('home'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $currentPraticaResumeEditUrl = $this->router->generate('pratiche_compila', [
            'pratica' => $currentPraticaId,
            'instance' => $currentPratica->getInstanceId(),
            'step' => $currentPratica->getLastCompiledStep(),
        ]);
        $crawler = $this->client->request('GET', $currentPraticaResumeEditUrl);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $currentStep = intval($crawler->filterXPath('//input[@name="flow_iscrizioneAsiloNido_step"]')->getNode(0)->getAttribute('value'));
        $this->assertEquals($currentPratica->getLastCompiledStep(), $currentStep);
    }

    /**
     * Step specifici di questo flusso
     */

    /**
     * @param Ente $ente
     * @param Crawler $crawler
     * @param $nextButton
     * @param AsiloNido $asiloSelected
     * @param $form
     * @return AsiloNido
     */
    protected function selezioneAsilo($ente, &$crawler, $nextButton, &$asiloSelected, &$form)
    {
        // Selezione del asilo
        $asili = $ente->getAsili();
        $key = rand(1, count($asili)) - 1;
        $asiloSelected = $asili[$key];
        $form = $crawler->selectButton($nextButton)->form(array(
            'iscrizione_asilo_nido_seleziona_nido[struttura]' => $asiloSelected->getId(),
        ));
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @param AsiloNido $asiloSelected
     * @param Crawler $crawler
     * @param $nextButton
     * @param $form
     */
    protected function terminiFruizione($asiloSelected, &$crawler, $nextButton, &$form)
    {
        // Termini di fruizione della struttura
        $this->assertContains($asiloSelected->getSchedaInformativa(), $this->client->getResponse()->getContent());

        $form = $crawler->selectButton($nextButton)->form(array(
            'iscrizione_asilo_nido_utilizzo_nido[accetto_utilizzo]' => 1,
        ));
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }

    /**
     * @param AsiloNido $asiloSelected
     * @param Crawler $crawler
     * @param $nextButton
     * @param $form
     */
    protected function selezioneOrari($asiloSelected, &$crawler, $nextButton, &$form)
    {
        $orarioSelected = null;
        foreach ($asiloSelected->getOrari() as $orario) {
            $this->assertContains($orario, $this->client->getResponse()->getContent());
            $orarioSelected = $orario;
        }

        $form = $crawler->selectButton($nextButton)->form(array(
            'iscrizione_asilo_nido_orari[periodo_iscrizione_da]' => '01-09-2016',
            'iscrizione_asilo_nido_orari[periodo_iscrizione_a]' => '01-09-2017',
            'iscrizione_asilo_nido_orari[struttura_orario]' => $orarioSelected,
        ));
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
    }


}
