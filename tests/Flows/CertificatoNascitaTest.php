<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\Flows;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\AsiloNido;
use AppBundle\Entity\CertificatoNascita;
use AppBundle\Entity\ComponenteNucleoFamiliare;
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
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class CertificatoNascitaTest
 */
class CertificatoNascitaTest extends AbstractAppTestCase
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
    public function testICanFillOutTheCertificatoNascitaAsLoggedUser()
    {
        //create an ente
        $ente = $this->createEnti()[0];
        $erogatore = $this->createErogatoreWithEnti([$ente]);
        //create the autolettura service bound to that ente
        $fqcn = CertificatoNascita ::class;
        $flow = 'ocsdc.form.flow.certificatonascita';
        $servizio = $this->createServizioWithErogatore($erogatore, 'Certificato Nascita', $fqcn, $flow);

        $user = $this->createCPSUser();

        $mockMailer = $this->setupSwiftmailerMock([$user]);
        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockMailer) {
            $kernel->getContainer()->set('swiftmailer.mailer.default', $mockMailer);
        });

        $this->clientRequestAsCPSUser($user, 'GET', $this->router->generate(
            'pratiche_new',
            ['servizio' => $servizio->getSlug()]
        ));
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $crawler = $this->client->followRedirect();

        $currentUriParts = explode('/', $this->client->getHistory()->current()->getUri());
        $currentPraticaId = array_pop($currentUriParts);
        $currentPratica = $this->em->getRepository('AppBundle:CertificatoNascita')->find($currentPraticaId);
        $this->assertEquals(CertificatoNascita::class, get_class($currentPratica));
        $this->assertEquals(0, $currentPratica->getModuliCompilati()->count());

        $nextButton = $this->translator->trans('button.next', [], 'CraueFormFlowBundle');
        $finishButton = $this->translator->trans('button.finish', [], 'CraueFormFlowBundle');

        $this->selezioneComune($crawler, $nextButton, $ente, $form, $currentPratica, $erogatore);
        $this->accettazioneIstruzioni($crawler, $nextButton, $form);
        $this->datiRichiedente($crawler, $nextButton, $fillData, $form);

        $form = $crawler->selectButton($finishButton)->form();
        $this->client->submit($form);
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $this->client->followRedirect();

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code");
        $this->assertContains($currentPraticaId, $this->client->getRequest()->getRequestUri());

        $this->em->refresh($currentPratica);

        $this->assertEquals(
            $currentPratica->getRichiedenteNome(),
            $user->getNome()
        );

        //modulo stampato
        $this->assertEquals(1, $currentPratica->getModuliCompilati()->count());
        $pdfExportedForm = $currentPratica->getModuliCompilati()->get(0);
        $this->assertNotNull($pdfExportedForm);
        $this->assertTrue($pdfExportedForm instanceof ModuloCompilato);

        $this->assertNotNull($currentPratica->getSubmissionTime());
        $submissionDate = new \DateTime();
        $submissionDate->setTimestamp($currentPratica->getSubmissionTime());

        $this->assertEquals('Modulo '.$currentPratica->getServizio()->getName().' compilato il '.$submissionDate->format($this->container->getParameter('ocsdc_default_datetime_format')), $pdfExportedForm->getDescription());
    }
}
