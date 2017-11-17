<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Controller;

use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Form\Base\MessageType;
use AppBundle\Form\Base\PraticaFlow;
use AppBundle\Logging\LogConstants;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class PraticheController
 *
 * @package AppBundle\Controller
 * @Route("/pratiche")
 */
class PraticheController extends Controller
{

    const ENTE_SLUG_QUERY_PARAMETER = 'ente';

    /**
     * @Route("/", name="pratiche")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Pratica');
        $pratiche = $repo->findBy(
            array('user' => $user),
            array('status' => 'DESC')
        );

        $praticheDraft = $repo->findBy(
            [
                'user' => $user,
                'status' => Pratica::STATUS_DRAFT,
            ],
            [
                'creationTime' => 'DESC',
            ]
        );

        $pratichePending = $repo->findBy(
            [
                'user' => $user,
                'status' => [
                    Pratica::STATUS_SUBMITTED,
                    Pratica::STATUS_REGISTERED,
                    Pratica::STATUS_PENDING,
                    Pratica::STATUS_COMPLETE_WAITALLEGATIOPERATORE,
                ],
            ],
            [
                'creationTime' => 'DESC',
            ]
        );

        $praticheCompleted = $repo->findBy(
            [
                'user' => $user,
                'status' => Pratica::STATUS_COMPLETE,
            ],
            [
                'creationTime' => 'DESC',
            ]
        );

        $praticheCancelled = $repo->findBy(
            [
                'user' => $user,
                'status' => Pratica::STATUS_CANCELLED,
            ],
            [
                'creationTime' => 'DESC',
            ]
        );


        return [
            'user' => $user,
            'pratiche' => $pratiche,
            'title' => 'lista_pratiche',
            'tab_pratiche' => array(
                'draft' => $praticheDraft,
                'pending' => $pratichePending,
                'completed' => $praticheCompleted,
                'cancelled' => $praticheCancelled,
            ),
        ];
    }

    /**
     * @Route("/{servizio}/new", name="pratiche_new")
     * @ParamConverter("servizio", class="AppBundle:Servizio", options={"mapping": {"servizio": "slug"}})
     *
     * @param Request $request
     * @param Servizio $servizio
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, Servizio $servizio)
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Pratica');
        $pratiche = $repo->findBy(
            array(
                'user' => $user,
                'servizio' => $servizio,
                'status' => Pratica::STATUS_DRAFT,
            ),
            array('creationTime' => 'ASC')
        );

        if (!empty( $pratiche )) {
            return $this->redirectToRoute(
                'pratiche_list_draft',
                ['servizio' => $servizio->getSlug()]
            );
        }

        $pratica = $this->createNewPratica($servizio, $user);

        $enteSlug = $request->query->get(self::ENTE_SLUG_QUERY_PARAMETER, null);
        if ($enteSlug != null) {
            $ente = $this->getDoctrine()
                         ->getRepository('AppBundle:Ente')
                         ->findOneBySlug($enteSlug);
            if ($ente != null) {
                $pratica->setEnte($ente);
            } else {
                $this->get('logger')->info(
                    LogConstants::PRATICA_WRONG_ENTE_REQUESTED,
                    [
                        'pratica' => $pratica,
                        'headers' => $request->headers,
                    ]
                );
            }
        }

        return $this->redirectToRoute(
            'pratiche_compila',
            ['pratica' => $pratica->getId()]
        );
    }

    /**
     * @Route("/{servizio}/draft", name="pratiche_list_draft")
     * @ParamConverter("servizio", class="AppBundle:Servizio", options={"mapping": {"servizio": "slug"}})
     * @Template()
     * @param Servizio $servizio
     *
     * @return array
     */
    public function listDraftByServiceAction(Servizio $servizio)
    {
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Pratica');
        $pratiche = $repo->findBy(
            array(
                'user' => $user,
                'servizio' => $servizio,
                'status' => Pratica::STATUS_DRAFT,
            ),
            array('creationTime' => 'ASC')
        );

        return [
            'user' => $user,
            'pratiche' => $pratiche,
            'title' => 'bozze_servizio',
            'msg' => array(
                'type' => 'warning',
                'text' => 'msg_bozze_servizio',
            ),
        ];
    }

    /**
     * @Route("/compila/{pratica}", name="pratiche_compila")
     * @ParamConverter("pratica", class="AppBundle:Pratica")
     * @Template()
     * @param Pratica $pratica
     *
     * @return array
     */
    public function compilaAction(Request $request, Pratica $pratica)
    {
        //@todo da testare
        //@todo scrivere la storia
        if ($pratica->getStatus() !== Pratica::STATUS_DRAFT) {
            return $this->redirectToRoute(
                'pratiche_show',
                ['pratica' => $pratica->getId()]
            );
        }

        $this->checkUserCanAccessPratica($pratica);

        $user = $this->getUser();

        /** @var PraticaFlow $praticaFlowService */
        $praticaFlowService = $this->get($pratica->getServizio()->getPraticaFlowServiceName());

        $praticaFlowService->setInstanceKey($user->getId());

        $praticaFlowService->bind($pratica);

        if ($pratica->getInstanceId() == null) {
            $pratica->setInstanceId($praticaFlowService->getInstanceId());
        }
        $resumeURI = $request->getUri()
            .'?instance='.$praticaFlowService->getInstanceId()
            .'&step='.$praticaFlowService->getCurrentStepNumber();

        $thread = $this->createThreadElementsForUserAndPratica($pratica, $user, $resumeURI);

        $form = $praticaFlowService->createForm();
        if ($praticaFlowService->isValid($form)) {


            $currentStep = $praticaFlowService->getCurrentStepNumber();
            //Erogatore
            //FIXME: find a way to generalize the ente selection step
            if($currentStep == 1 ) {
                $this->infereErogatoreFromEnteAndServizio($pratica);
            }

            $praticaFlowService->saveCurrentStepData($form);
            $pratica->setLastCompiledStep($currentStep);



            if ($praticaFlowService->nextStep()) {
                $this->getDoctrine()->getManager()->flush();
                $form = $praticaFlowService->createForm();

                $resumeURI = $request->getUri()
                    .'?instance='.$praticaFlowService->getInstanceId()
                    .'&step='.$praticaFlowService->getCurrentStepNumber();
                $thread = $this->createThreadElementsForUserAndPratica($pratica, $user, $resumeURI);

            } else {
                $pratica->setSubmissionTime(time());

                $moduloCompilato = $this->get('ocsdc.modulo_pdf_builder')->createForPratica($pratica, $user);
                $pratica->addModuloCompilato($moduloCompilato);

                $this->get('ocsdc.pratica_status_service')->setNewStatus($pratica, Pratica::STATUS_SUBMITTED);

                $this->get('logger')->info(
                    LogConstants::PRATICA_UPDATED,
                    ['id' => $pratica->getId(), 'pratica' => $pratica]
                );

                $this->addFlash(
                    'feedback',
                    $this->get('translator')->trans('pratica_ricevuta')
                );

                $praticaFlowService->getDataManager()->drop($praticaFlowService);
                $praticaFlowService->reset();

                return $this->redirectToRoute(
                    'pratiche_show',
                    ['pratica' => $pratica->getId()]
                );
            }
        }

        return [
            'form' => $form->createView(),
            'pratica' => $praticaFlowService->getFormData(),
            'flow' => $praticaFlowService,
            'user' => $user,
            'threads' => $thread,
        ];
    }

    /**
     * @Route("/{pratica}", name="pratiche_show")
     * @ParamConverter("pratica", class="AppBundle:Pratica")
     * @Template()
     * @param Pratica $pratica
     *
     * @return array
     */
    public function showAction(Request $request, Pratica $pratica)
    {
        $this->checkUserCanAccessPratica($pratica);

        $user = $this->getUser();
        $resumeURI = $request->getUri();
        $thread = $this->createThreadElementsForUserAndPratica($pratica, $user, $resumeURI);

        return [
            'pratica' => $pratica,
            'user' => $user,
            'threads' => $thread,
        ];
    }
    
    /**
     * @param Servizio $servizio
     * @param CPSUser $user
     *
     * @return Pratica
     */
    private function createNewPratica(Servizio $servizio, CPSUser $user)
    {
        $praticaClassName = $servizio->getPraticaFCQN();
        /** @var PraticaFlow $praticaFlowService */
        $praticaFlowService = $this->get($servizio->getPraticaFlowServiceName());

        $pratica = new $praticaClassName();
        if (!$pratica instanceof Pratica) {
            throw new \RuntimeException("Wrong Pratica FCQN for servizio {$servizio->getName()}");
        }
        $pratica
            ->setServizio($servizio)
            ->setType($servizio->getSlug())
            ->setUser($user)
            ->setStatus(Pratica::STATUS_DRAFT);

        $repo = $this->getDoctrine()->getRepository('AppBundle:Pratica');
        $lastPraticaList = $repo->findBy(
            array(
                'user' => $user,
                'servizio' => $servizio,
                'status' => [
                    Pratica::STATUS_COMPLETE,
                    Pratica::STATUS_SUBMITTED,
                    Pratica::STATUS_PENDING,
                    Pratica::STATUS_REGISTERED
                ],
            ),
            array('creationTime' => 'DESC'),
            1
        );
        $lastPratica = null;
        if ($lastPraticaList) {
            $lastPratica = $lastPraticaList[0];
        }
        if ($lastPratica instanceof Pratica) {
            $praticaFlowService->populatePraticaFieldsWithLastPraticaValues($lastPratica, $pratica);
        }

        $user = $this->getUser();
        $praticaFlowService->populatePraticaFieldsWithUserValues($user, $pratica);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pratica);
        $em->flush();

        $this->get('logger')->info(
            LogConstants::PRATICA_CREATED,
            ['type' => $pratica->getType(), 'pratica' => $pratica]
        );

        return $pratica;
    }

    private function checkUserCanAccessPratica(Pratica $pratica)
    {
        $praticaUser = $pratica->getUser();
        if ( $praticaUser->getId() !== $this->getUser()->getId()) {
            throw new UnauthorizedHttpException("User can not read pratica {$pratica->getId()}");
        }
    }

    /**
     * @param Pratica $pratica
     * @param $user
     * @return array
     */
    private function createThreadElementsForUserAndPratica(Pratica $pratica, User $user, $returnURL)
    {

        if ($pratica->getEnte()) {
            $messagesAdapterService = $this->get('ocsdc.messages_adapter');
            //FIXME: this should be the Capofila Ente (the first in the array of the Erogatore's ones)
            $ente = $pratica->getEnte();
            $servizio = $pratica->getServizio();
            $userThread = $messagesAdapterService->getThreadsForUserEnteAndService($user, $ente, $servizio);
            if (!$userThread) {
                return null;
            }

            $threadId = $userThread[0]->threadId;
            $threadForm = $this->createForm(
                MessageType::class,
                [
                    'thread_id' => $threadId,
                    'sender_id' => $user->getId(),
                    'return_url' => $returnURL,
                ],
                [
                    'action' => $this->get('router')->generate('messages_controller_enqueue_for_user', ['threadId' => $threadId]),
                    'method' => 'PUT',
                ]
            );

            $thread = [
                'threadId' => $threadId,
                'title'    => $userThread[0]->title,
                'messages' => $messagesAdapterService->getDecoratedMessagesForThread($threadId, $user),
                'form'     => $threadForm->createView(),
            ];

            return [$thread];
        }

        return null;
    }

    private function infereErogatoreFromEnteAndServizio(Pratica $pratica)
    {
        $ente = $pratica->getEnte();
        $servizio = $pratica->getServizio();
        $erogatori = $servizio->getErogatori();
        foreach ($erogatori as $erogatore) {
            if ($erogatore->getEnti()->contains($ente)) {
                $pratica->setErogatore($erogatore);

                return;
            }
        }
        //FIXME: testme
        throw new \Error('Missing erogatore for service ');
    }
}
