<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Controller;

use AppBundle\Entity\CPSUser;
use AppBundle\Form\Base\MessageType;
use AppBundle\Logging\LogConstants;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package AppBundle\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_dashboard")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $serviziRepository = $this->getDoctrine()->getRepository('AppBundle:Servizio');
        $servizi = $serviziRepository->findBy([], [], 4);

        $praticheRepo = $this->getDoctrine()->getRepository('AppBundle:Pratica');
        $pratiche = $praticheRepo->findBy(
            ['user' => $user],
            ['creationTime' => 'ASC'],
            3
        );

        $messagesAdapterService = $this->get('ocsdc.messages_adapter');
        $userThreads = (array) $messagesAdapterService->getDecoratedThreadsForUser($user);
        $threads = [];
        foreach ($userThreads as $thread) {

            $form = $this->createForm(
                MessageType::class,
                ['thread_id' => $thread->threadId, 'sender_id' => $user->getId()],
                [
                    'action' => $this->get('router')->generate('messages_controller_enqueue_for_user', ['threadId' => $thread->threadId]),
                    'method' => 'PUT',
                ]
            );

            $threads[] = [
                'threadId' => $thread->threadId,
                'title'    => $thread->title,
                'messages' => $messagesAdapterService->getDecoratedMessagesForThread($thread->threadId, $user),
                'form'     => $form->createView()
            ];
        }

        return array(
            'user'     => $user,
            'servizi' => $servizi,
            'pratiche' => $pratiche,
            'threads' => $threads,
        );
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Template()
     * @param Request $request
     *
     * @return array
     */
    public function profileAction(Request $request)
    {
        /** @var CPSUser $user */
        $user = $this->getUser();
        $form = $this->setupSdcUserForm($user)->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $this->storeSdcUserData($user, $data, $this->get('logger'));

            $redirectRoute = $request->query->has('r') ? $request->query->get('r') : 'user_profile';
            $redirectRouteParams = $request->query->has('p') ? unserialize($request->query->get('p')) : array();
            $redirectRouteQuery = $request->query->has('p') ? unserialize($request->query->get('q')) : array();

            return $this->redirectToRoute($redirectRoute, array_merge($redirectRouteParams, $redirectRouteQuery) );
        }else{
            if ($request->query->has('r')){
                $this->addFlash(
                    'warning',
                    $this->get('translator')->trans('completa_profilo')
                );
            }
        }

        return [
            'form' => $form->createView(),
            'user' => $user,
        ];
    }

    private function storeSdcUserData(CPSUser $user, array $data, LoggerInterface $logger)
    {
        $manager = $this->getDoctrine()->getManager();
        $user
            ->setEmailContatto($data['email_contatto'])
            ->setCellulareContatto($data['cellulare_contatto'])

            ->setSdcIndirizzoResidenza($data['sdc_indirizzo_residenza'])
            ->setSdcCapResidenza($data['sdc_cap_residenza'])
            ->setSdcCittaResidenza($data['sdc_citta_residenza'])
            ->setSdcProvinciaResidenza($data['sdc_provincia_residenza'])
            ->setSdcStatoResidenza($data['sdc_stato_residenza'])

            ->setSdcIndirizzoDomicilio($data['sdc_indirizzo_domicilio'])
            ->setSdcCapDomicilio($data['sdc_cap_domicilio'])
            ->setSdcCittaDomicilio($data['sdc_citta_domicilio'])
            ->setSdcProvinciaDomicilio($data['sdc_provincia_domicilio'])
            ->setSdcStatoDomicilio($data['sdc_stato_domicilio']);

        $manager->persist($user);
        try {
            $manager->flush();
            $logger->info(LogConstants::USER_HAS_CHANGED_CONTACTS_INFO, ['userid' => $user->getId()]);
        } catch (\Exception $e) {
            $logger->error($e->getMessage());
        }
    }

    private function setupSdcUserForm(CPSUser $user)
    {
        $compiledCellulareData = $user->getCellulare();
        $compiledEmailData = $user->getEmail();

        $compiledIndirizzoResidenza = $user->getIndirizzoResidenza();
        $compiledCapResidenza = $user->getCapResidenza();
        $compiledCittaResidenza = $user->getCittaResidenza();
        $compiledProvinciaResidenza = $user->getProvinciaResidenza();
        $compiledStatoResidenza = $user->getStatoResidenza();

        $compiledIndirizzoDomicilio = $user->getIndirizzoDomicilio();
        $compiledCapDomicilio = $user->getCapDomicilio();
        $compiledCittaDomicilio = $user->getCittaDomicilio();
        $compiledProvinciaDomicilio = $user->getProvinciaDomicilio();
        $compiledStatoDomicilio = $user->getStatoDomicilio();

        $formBuilder = $this->createFormBuilder(null, ['attr' => ['id' => 'edit_user_profile']])
            ->add('email_contatto', EmailType::class,
                ['label' => false, 'data' => $compiledEmailData, 'required' => false]
            )
            ->add('cellulare_contatto', TextType::class,
                ['label' => false, 'data' => $compiledCellulareData, 'required' => false]
            )
            ->add('cellulare_contatto', TextType::class,
                ['label' => false, 'data' => $compiledCellulareData, 'required' => false]
            )

            ->add('sdc_indirizzo_residenza', TextType::class,
                ['label' => false, 'data' => $compiledIndirizzoResidenza, 'required' => true]
            )
            ->add('sdc_cap_residenza', TextType::class,
                ['label' => false, 'data' => $compiledCapResidenza, 'required' => true]
            )
            ->add('sdc_citta_residenza', TextType::class,
                ['label' => false, 'data' => $compiledCittaResidenza, 'required' => true]
            )
            ->add('sdc_provincia_residenza', TextType::class,
                ['label' => false, 'data' => $compiledProvinciaResidenza, 'required' => true]
            )
            ->add('sdc_stato_residenza', TextType::class,
                ['label' => false, 'data' => $compiledStatoResidenza, 'required' => true]
            )

            ->add('sdc_indirizzo_domicilio', TextType::class,
                ['label' => false, 'data' => $compiledIndirizzoDomicilio, 'required' => false]
            )
            ->add('sdc_cap_domicilio', TextType::class,
                ['label' => false, 'data' => $compiledCapDomicilio, 'required' => false]
            )
            ->add('sdc_citta_domicilio', TextType::class,
                ['label' => false, 'data' => $compiledCittaDomicilio, 'required' => false]
            )
            ->add('sdc_provincia_domicilio', TextType::class,
                ['label' => false, 'data' => $compiledProvinciaDomicilio, 'required' => false]
            )
            ->add('sdc_stato_domicilio', TextType::class,
                ['label' => false, 'data' => $compiledStatoDomicilio, 'required' => false]
            )

            ->add('save', SubmitType::class,
                ['label' => 'user.profile.salva_informazioni_profilo']
            );
        $form = $formBuilder->getForm();

        return $form;
    }

    /**
     * @Route("/latest_news", name="user_latest_news")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function latestNewsAction(Request $request)
    {
        $newsProvider = $this->get('ocsdc.remote_content_provider');
        $enti = $this->getEntiFromCurrentUser();
        $data = $newsProvider->getLatestNews($enti);
        $response = new JsonResponse($data);
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/latest_deadlines", name="user_latest_deadlines")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function latestDeadlinesAction(Request $request)
    {
        $newsProvider = $this->get('ocsdc.remote_content_provider');
        $enti = $this->getEntiFromCurrentUser();
        $data = $newsProvider->getLatestDeadlines($enti);
        $response = new JsonResponse($data);
        $response->setMaxAge(3600);
        $response->setSharedMaxAge(3600);
        return $response;
    }

    private function getEntiFromCurrentUser()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entiPerUser = $entityManager->createQueryBuilder()
                                           ->select('IDENTITY(p.ente)')->distinct()
                                           ->from('AppBundle:Pratica', 'p')
                                           ->where('p.user = :user')
                                           ->setParameter('user', $this->getUser())
                                           ->getQuery()
                                           ->getResult();

        $repository = $entityManager->getRepository('AppBundle:Ente');
        if (count($entiPerUser) > 0) {
            $entiPerUser = array_reduce($entiPerUser, 'array_merge', array());
            $enti = $repository->findBy(['id' => $entiPerUser]);
        } else {
            $enti = $repository->findAll();
        }

        return $enti;
    }

}
