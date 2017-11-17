<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Ente;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\StatusChange;
use AppBundle\Logging\LogConstants;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class APIController
 * @package AppBundle\Controller
 * @Route("/api/v1.0")
 */
class APIController extends Controller
{

    const CURRENT_API_VERSION = 'v1.0';
    const SCHEDA_INFORMATIVA_REMOTE_PARAMETER = 'remote';

    /**
     * @Route("/status",name="api_status")
     * @return JsonResponse
     */
    public function statusAction()
    {
        return new JsonResponse([
            'version' => self::CURRENT_API_VERSION,
            'status' => 'ok',
        ]);
    }

    /**
     * @Route("/services",name="api_services")
     * @return JsonResponse
     */
    public function servicesAction()
    {
        $servizi = $this->getDoctrine()->getRepository('AppBundle:Servizio')->findAll();
        $out = [];
        foreach ($servizi as $servizio) {
            $out[] = [
                'name' => $servizio->getName(),
                'slug' => $servizio->getSlug(),
            ];
        }

        return new JsonResponse($out);
    }

    /**
     * @Route("/pratica/{pratica}/status", name="gpa_api_pratica_update_status")
     * @Method({"POST"})
     * @Security("has_role('ROLE_GPA')")
     * @return Response
     */
    public function addStatusChangeToPraticaAction(Request $request, Pratica $pratica)
    {
        $logger = $this->get('logger');
        $content = $request->getContent();
        if (empty($content)) {
            $logger->info(LogConstants::PRATICA_ERROR_IN_UPDATED_STATUS_FROM_GPA, [ 'statusChange' => null , 'error' => 'missing body altogether' ]);

            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        try {
            $statusChange = new StatusChange(json_decode($content, true));
        } catch (\Exception $e) {
            $logger->info(LogConstants::PRATICA_ERROR_IN_UPDATED_STATUS_FROM_GPA, [ 'statusChange' => $content , 'error' => $e ]);

            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $this->get('ocsdc.pratica_status_service')->setNewStatus($pratica, $statusChange->getEvento(), $statusChange);
        $logger->info(LogConstants::PRATICA_UPDATED_STATUS_FROM_GPA, [ 'statusChange' => $statusChange ]);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    //$route = '/api/'.APIController::CURRENT_API_VERSION.'/schedaInformativa/'.$servizio->getSlug().'/'.$ente->getSlug()

    /**
     * @Route("/schedaInformativa/{servizio}/{ente}", name="ez_api_scheda_informativa_servizio_ente")
     * @ParamConverter("servizio", options={"mapping": {"servizio": "slug"}})
     * @ParamConverter("ente", options={"mapping": {"ente": "codiceMeccanografico"}})
     *
     * @param Request  $request
     * @param Servizio $servizio
     * @param Ente     $ente
     *
     * @return Response
     */
    public function putSchedaInformativaForServizioAndEnteAction(Request $request, Servizio $servizio, Ente $ente)
    {
        if (!$request->query->has(self::SCHEDA_INFORMATIVA_REMOTE_PARAMETER)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $schedaInformativa = json_decode(file_get_contents($request->query->get(self::SCHEDA_INFORMATIVA_REMOTE_PARAMETER)), true);

        if (!array_key_exists('data', $schedaInformativa) || !array_key_exists('metadata', $schedaInformativa)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $servizio->setSchedaInformativaPerEnte($schedaInformativa, $ente);
        $this->getDoctrine()->getManager()->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
