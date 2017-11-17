<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Allegato;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Pratica;
use AppBundle\Form\Base\AllegatoType;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use AppBundle\Logging\LogConstants;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Mapping\PropertyMapping;

/**
 * Class AllegatoController
 * Dobbiamo fornire gli allegati sia agli operatori che agli utenti, quindi non montiamo la rotta sotto una url generica
 * Lasciamo il compito a ogni singola action
 * @Route("")
 */
class AllegatoController extends Controller
{

    /**
     * @param Request $request
     * @Route("/pratiche/allegati/new",name="allegati_create_cpsuser")
     * @Template()
     * @return mixed
     */
    public function cpsUserCreateAllegatoAction(Request $request)
    {
        $allegato = new Allegato();

        $form = $this->createForm(AllegatoType::class, $allegato, ['helper' => new TestiAccompagnatoriProcedura($this->get('translator'))]);
        $form->add($this->get('translator')->trans('salva'), SubmitType::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $allegato->setOwner($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($allegato);
            $em->flush();

            return new RedirectResponse($this->get('router')->generate('allegati_list_cpsuser'));
        }

        return [
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ];
    }

    /**
     * @param Request  $request
     * @param Allegato $allegato
     * @Route("/pratiche/allegati/{allegato}", name="allegati_download_cpsuser")
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function cpsUserAllegatoDownloadAction(Request $request, Allegato $allegato)
    {
        $logger = $this->get('logger');
        $user = $this->getUser();
        if ($allegato->getOwner() === $user) {
            $logger->info(
                LogConstants::ALLEGATO_DOWNLOAD_PERMESSO_CPSUSER,
                [
                    'user' => $user->getId().' ('.$user->getNome().' '.$user->getCognome().')',
                    'originalFileName' => $allegato->getOriginalFilename(),
                    'allegato' => $allegato->getId(),
                ]
            );

            return $this->createBinaryResponseForAllegato($allegato);
        }
        $this->logUnauthorizedAccessAttempt($allegato, $logger);
        throw new NotFoundHttpException(); //security by obscurity
    }

    /**
     * @param Request  $request
     * @param Allegato $allegato
     * @Route("/operatori/allegati/{allegato}", name="allegati_download_operatore")
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function operatoreAllegatoDownloadAction(Request $request, Allegato $allegato)
    {
        $logger = $this->get('logger');
        $user = $this->getUser();
        $isOperatoreAmongstTheAllowedOnes = false;
        $becauseOfPratiche = [];

        foreach ($allegato->getPratiche() as $pratica) {
            if ($pratica->getOperatore() === $user) {
                $becauseOfPratiche[] = $pratica->getId();
                $isOperatoreAmongstTheAllowedOnes = true;
            }
        }

        if ($isOperatoreAmongstTheAllowedOnes) {
            $logger->info(
                LogConstants::ALLEGATO_DOWNLOAD_PERMESSO_OPERATORE,
                [
                    'user' => $user->getId().' ('.$user->getNome().' '.$user->getCognome().')',
                    'originalFileName' => $allegato->getOriginalFilename(),
                    'allegato' => $allegato->getId(),
                    'pratiche' => $becauseOfPratiche,
                ]
            );

            return $this->createBinaryResponseForAllegato($allegato);
        }
        $this->logUnauthorizedAccessAttempt($allegato, $logger);
        throw new NotFoundHttpException(); //security by obscurity
    }

    /**
     * @param Request  $request
     * @param Allegato $allegato
     * @Route("/operatori/risposta/{allegato}", name="risposta_download_operatore")
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function operatoreRispostaDownloadAction(Request $request, Allegato $allegato)
    {
        $logger = $this->get('logger');
        $user = $this->getUser();
        $isOperatoreAmongstTheAllowedOnes = false;
        $becauseOfPratiche = [];

        $repo = $this->getDoctrine()->getRepository('AppBundle:Pratica');
        $pratiche = $repo->findBy(
            array('rispostaOperatore' => $allegato)
        );

        foreach ($pratiche as $pratica) {
            if ($pratica->getOperatore() === $user) {
                $becauseOfPratiche[] = $pratica->getId();
                $isOperatoreAmongstTheAllowedOnes = true;
            }
        }

        if ($isOperatoreAmongstTheAllowedOnes) {
            $logger->info(
                LogConstants::ALLEGATO_DOWNLOAD_PERMESSO_OPERATORE,
                [
                    'user' => $user->getId().' ('.$user->getNome().' '.$user->getCognome().')',
                    'originalFileName' => $allegato->getOriginalFilename(),
                    'allegato' => $allegato->getId(),
                    'pratiche' => $becauseOfPratiche,
                ]
            );

            return $this->createBinaryResponseForAllegato($allegato);
        }
        $this->logUnauthorizedAccessAttempt($allegato, $logger);
        throw new NotFoundHttpException(); //security by obscurity
    }

    /**
     * @Route("/operatori/{pratica}/risposta_non_firmata",name="allegati_download_risposta_non_firmata")
     * @param Pratica $pratica
     */
    public function scaricaRispostaNonFirmata(Pratica $pratica){

        if( $pratica->getOperatore() !== $this->getUser() ){
            throw new AccessDeniedHttpException();
        }

        if( $pratica->getEsito() === null) {
            throw new NotFoundHttpException();
        }

        $unsignedResponse = $this->get('ocsdc.modulo_pdf_builder')->createUnsignedResponseForPratica($pratica);
        return $this->createBinaryResponseForAllegato($unsignedResponse);
    }


    /**
     * @Route("/pratiche/allegati/", name="allegati_list_cpsuser")
     * @Template()
     */
    public function cpsUserListAllegatiAction()
    {
        $user = $this->getUser();
        $allegati = [];
        if ($user instanceof CPSUser) {
            /** @var Query $query */
            $query = $this->getDoctrine()
                ->getManager()
                ->createQuery("SELECT allegato 
                FROM AppBundle\Entity\Allegato allegato 
                WHERE allegato INSTANCE OF AppBundle\Entity\Allegato 
                AND allegato.owner = :user
                ORDER BY allegato.filename ASC")
                ->setParameter('user', $this->getUser());

            $retrievedAllegati = $query->getResult();
            foreach ($retrievedAllegati as $allegato) {
                $deleteForm = $this->createDeleteFormForAllegato($allegato);
                $allegati[] = [
                    'allegato' => $allegato,
                    'deleteform' => $deleteForm ? $deleteForm->createView() : null,
                ];
            }
        }

        return [
            'allegati' => $allegati,
            'user' => $this->getUser(),
        ];
    }


    /**
     * @param Request  $request
     * @param Allegato $allegato
     * @Route("/pratiche/allegati/{allegato}/delete",name="allegati_delete_cpsuser")
     * @Method("DELETE")
     * @return RedirectResponse
     */
    public function cpsUserDeleteAllegatoAction(Request $request, Allegato $allegato)
    {
        $deleteForm = $this->createDeleteFormForAllegato($allegato);
        if ($deleteForm instanceof Form) {
            $deleteForm->handleRequest($request);

            if ($this->canDeleteAllegato($allegato) && $deleteForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($allegato);
                $em->flush();
                $this->get('session')->getFlashBag()
                     ->add('info', $this->get('translator')->trans('allegato.cancellato'));
                $this->get('logger')->info(LogConstants::ALLEGATO_CANCELLAZIONE_PERMESSA, [
                    'allegato' => $allegato,
                    'user' => $this->getUser(),
                ]);
            }
        }

        return new RedirectResponse($this->get('router')->generate('allegati_list_cpsuser'));
    }

    private function canDeleteAllegato(Allegato $allegato)
    {
        return $allegato->getOwner() === $this->getUser()
               && $allegato->getPratiche()->count() == 0
               && !is_subclass_of($allegato, Allegato::class);
    }

    /**
     * @param Allegato $allegato
     * @return BinaryFileResponse
     */
    private function createBinaryResponseForAllegato(Allegato $allegato)
    {
        $filename = $allegato->getFilename();
        $directoryNamer = $this->get('ocsdc.allegati.directory_namer');
        /** @var PropertyMapping $mapping */
        $mapping = $this->get('vich_uploader.property_mapping_factory')->fromObject($allegato)[0];
        $destDir = $mapping->getUploadDestination().'/'.$directoryNamer->directoryName($allegato, $mapping);
        $filePath = $destDir.DIRECTORY_SEPARATOR.$filename;

        return new BinaryFileResponse(
            $filePath,
            200,
            [
                'Content-type' => 'application/octet-stream',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $allegato->getOriginalFilename() . '.' . $allegato->getFile()->getExtension()),
            ]
        );
    }

    /**
     * @param Allegato $allegato
     * @param LoggerInterface $logger
     */
    private function logUnauthorizedAccessAttempt(Allegato $allegato, $logger)
    {
        $logger->info(
            LogConstants::ALLEGATO_DOWNLOAD_NEGATO,
            [
                'originalFileName' => $allegato->getOriginalFilename(),
                'allegato' => $allegato->getId(),
            ]
        );
    }

    /**
     * @param Allegato $allegato
     * @return \Symfony\Component\Form\Form|null
     */
    private function createDeleteFormForAllegato($allegato)
    {
        if ($this->canDeleteAllegato($allegato)) {
            return $this->createFormBuilder(array('id' => $allegato->getId()))
                        ->add('id', HiddenType::class)
                        ->add('elimina', SubmitType::class, ['attr' => ['class' => 'btn btn-xs btn-danger']])
                        ->setAction($this->get('router')->generate('allegati_delete_cpsuser',
                            ['allegato' => $allegato->getId()]))
                        ->setMethod('DELETE')
                        ->getForm();
        }
        return null;
    }
}
