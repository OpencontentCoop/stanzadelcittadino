<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Categoria;
use AppBundle\Entity\Servizio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServiziController
 * @package AppBundle\Controller
 * @Route("/servizi")
 */
class ServiziController extends Controller
{
    /**
     * @Route("/", name="servizi_list")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function serviziAction(Request $request)
    {
        $serviziRepository = $this->getDoctrine()->getRepository('AppBundle:Servizio');
        $servizi = $serviziRepository->findAll();


        return [
            'servizi' => $servizi,
            'user' => $this->getUser(),
        ];
    }

    /**
     * @Route("/miller/{topic}/{subtopic}", name="servizi_miller", defaults={"topic":false, "subtopic":false})
     * @Template()
     * @param string $topic
     * @param string $subtopic
     * @param Request $request
     * @return array
     */
    public function serviziMillerAction($topic, $subtopic, Request $request)
    {
        $topics = $subTopics = $subSubTopics = $servizi = array();
        $serviziRepository = $this->getDoctrine()->getRepository('AppBundle:Servizio');
        $categoriesRepo = $this->getDoctrine()->getRepository('AppBundle:Categoria');
        $area = $request->get('area', false);

        $areas = $categoriesRepo->findBy(
            ['parentId' => null],
            ['name' => 'ASC']
        );

        /** @var Categoria $parent */
        if (!$area)
        {
            if ($topic)
            {
                /** @var Categoria $temp */
                $temp = $categoriesRepo->findOneBySlug($topic);
                $parent = $categoriesRepo->findOneByTreeId($temp->getTreeParentId());
            }
            else
            {
                $parent = $areas[0];
            }
        }
        else
        {
            $parent = $categoriesRepo->findOneBySlug($area);
        }

        $topics = $categoriesRepo->findBy(
            ['treeParentId' => $parent->getTreeId()],
            ['name' => 'ASC']
        );

        if ( !$topic )
        {
            $topic = $topics[0];
        }
        else
        {
            $topic = $categoriesRepo->findOneBySlug($topic);
        }

        $subTopics = $categoriesRepo->findBy(
            ['parentId' => $topic->getId()],
            ['name' => 'ASC']
        );

        if ($subtopic)
        {
            $subtopic = $categoriesRepo->findOneBySlug($subtopic);
            $subSubTopics = $categoriesRepo->findBy(
                ['parentId' => $subtopic->getId()],
                ['name' => 'ASC']
            );

            // Recupero servizi subtopic
            $temp = $serviziRepository->findBy(
                array('area' => $subtopic->getId()),
                array('name' => 'ASC')
            );
            $servizi[$subtopic->getId()] = $temp;

            foreach ($subSubTopics as $sub)
            {
                $temp = $serviziRepository->findBy(
                    array('area' => $sub->getId()),
                    array('name' => 'ASC')
                );
                $servizi[$sub->getId()] = $temp;
            }
        }

        return [
            'areas'            => $areas,
            'current_area'     => $parent,
            'current_topic'    => $topic,
            'topics'           => $topics,
            'current_subtopic' => $subtopic,
            'sub_topics'       => $subTopics,
            'sub_sub_topics'   => $subSubTopics,
            'servizi'          => $servizi,
            'user'             => $this->getUser()
        ];
    }

    /**
     * @Route("/miller_ajax/{topic}/{subtopic}", name="servizi_miller_ajax", defaults={"subtopic":false})
     * @param string $topic
     * @param string $subtopic
     * @param Request $request
     * @return array
     */
    public function serviziMillerAjaxAction($topic, $subtopic, Request $request)
    {

        if (!$request->isXMLHttpRequest()) {
            return $this->redirectToRoute(
                'servizi_miller',
                ['topic' => $topic, 'subtopic' => $subtopic]
            );
        }
        $serviziRepository = $this->getDoctrine()->getRepository('AppBundle:Servizio');
        $categoriesRepo = $this->getDoctrine()->getRepository('AppBundle:Categoria');

        $subTopics = $subSubTopics = $servizi =  $params = array();
        $templateName =  '@App/Servizi/parts/miller/section.html.twig';

        /** @var Categoria $topic */
        $topic = $categoriesRepo->findOneBySlug($topic);
        $parent = $categoriesRepo->findOneByTreeId($topic->getTreeParentId());
        $params['current_area']= $parent;

        $params['current_topic']= $topic;

        $subTopics = $categoriesRepo->findBy(
            ['parentId' => $topic->getId()],
            ['name' => 'ASC']
        );
        $params['sub_topics']= $subTopics;
        $params['current_subtopic']= $subtopic;

        if ($subtopic)
        {

            $templateName =  '@App/Servizi/parts/miller/subsection.html.twig';
            $subtopic = $categoriesRepo->findOneBySlug($subtopic);
            $params['current_subtopic']= $subtopic;
            $subSubTopics = $categoriesRepo->findBy(
                ['parentId' => $subtopic->getId()],
                ['name' => 'ASC']
            );

            $params['sub_sub_topics']= $subSubTopics;

            // Recupero servizi subtopic
            $temp = $serviziRepository->findBy(
                array('area' => $subtopic->getId()),
                array('name' => 'ASC')
            );
            $servizi[$subtopic->getId()] = $temp;

            foreach ($subSubTopics as $sub)
            {
                $temp = $serviziRepository->findBy(
                    array('area' => $sub->getId()),
                    array('name' => 'ASC')
                );
                $servizi[$sub->getId()] = $temp;
            }
            $params['servizi']= $servizi;
        }

        $template = $this->render(
            $templateName,
            $params
        )->getContent();

        $response = new JsonResponse(
            ['html' => $template]
        );
        $response->setVary("X-Requested-With");
        return $response;

    }

    /**
     * @Route("/{slug}", name="servizi_show")
     * @Template()
     * @param string $slug
     * @param Request $request
     *
     * @return array
     */
    public function serviziDetailAction($slug, Request $request)
    {
        $user = $this->getUser();
        $serviziRepository = $this->getDoctrine()->getRepository('AppBundle:Servizio');
        /** @var Servizio $servizio */
        $servizio = $serviziRepository->findOneBySlug($slug);
        if (!$servizio instanceof Servizio){
            throw new NotFoundHttpException("Servizio $slug not found");
        }
        $servizi = $serviziRepository->findAll();
        $serviziArea = $serviziRepository->findBy(['area' => $servizio->getArea()]);

        return [
            'user' => $user,
            'servizio' => $servizio,
            'servizi' => $servizi,
            'servizi_area' => $serviziArea,
            'user' => $this->getUser(),
        ];

    }

}
