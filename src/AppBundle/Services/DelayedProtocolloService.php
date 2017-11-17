<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\AllegatoInterface;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\ScheduledAction;
use AppBundle\Protocollo\Exception\AlreadyScheduledException;
use AppBundle\Protocollo\Exception\AlreadySentException;
use AppBundle\ScheduledAction\ScheduledActionHandlerInterface;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class DelayedProtocolloService extends AbstractProtocolloService implements ProtocolloServiceInterface, ScheduledActionHandlerInterface
{
    const SCHEDULED_ITEM_TYPE_SEND = 'protocollo.sendPratica';

    const SCHEDULED_ITEM_TYPE_UPDATE = 'protocollo.refreshPratica';

    const SCHEDULED_ITEM_TYPE_UPLOAD = 'protocollo.uploadFile';

    /**
     * @var ProtocolloServiceInterface
     */
    protected $protocolloService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $entityManager;


    public function __construct(
        ProtocolloServiceInterface $protocolloService,
        EntityManager $entityManager,
        LoggerInterface $logger
    ) {
        $this->protocolloService = $protocolloService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function protocollaPratica(Pratica $pratica)
    {
        $this->validatePratica($pratica);

        $params = serialize([
            'pratica' => $pratica->getId(),
        ]);

        $repository = $this->entityManager->getRepository('AppBundle:ScheduledAction');
        if ($repository->findBy([
            'type' => self::SCHEDULED_ITEM_TYPE_SEND,
            'params' => $params,
        ])
        ) {
            throw new AlreadyScheduledException();
        }

        $scheduled = (new ScheduledAction())
            ->setService('ocsdc.protocollo')
            ->setType(self::SCHEDULED_ITEM_TYPE_SEND)
            ->setParams($params);
        $this->entityManager->persist($scheduled);
        $this->entityManager->flush();
    }

    public function protocollaRisposta(Pratica $pratica)
    {
        $this->validatePraticaForUploadFile($pratica);
        $params = serialize([
            'pratica' => $pratica->getId(),
        ]);

        $repository = $this->entityManager->getRepository('AppBundle:ScheduledAction');
        if ($repository->findBy([
            'type' => self::SCHEDULED_ITEM_TYPE_UPDATE,
            'params' => $params,
        ])
        ) {
            throw new AlreadyScheduledException();
        }

        $scheduled = (new ScheduledAction())
            ->setService('ocsdc.protocollo')
            ->setType(self::SCHEDULED_ITEM_TYPE_UPDATE)
            ->setParams($params);
        $this->entityManager->persist($scheduled);
        $this->entityManager->flush();
    }

    public function protocollaAllegato(Pratica $pratica, AllegatoInterface $allegato)
    {
        $this->validateUploadFile($pratica, $allegato);

        $params = serialize([
            'pratica' => $pratica->getId(),
            'allegato' => $allegato->getId()
        ]);

        $repository = $this->entityManager->getRepository('AppBundle:ScheduledAction');
        if ($repository->findBy([
            'type' => self::SCHEDULED_ITEM_TYPE_UPLOAD,
            'params' => $params,
        ])
        ) {
            throw new AlreadyScheduledException();
        }

        $scheduled = (new ScheduledAction())
            ->setService('ocsdc.protocollo')
            ->setType(self::SCHEDULED_ITEM_TYPE_UPLOAD)
            ->setParams($params);

        $this->entityManager->persist($scheduled);
        $this->entityManager->flush();
    }

    public function getHandler()
    {
        return $this->protocolloService->getHandler();
    }

    /**
     * @param ScheduledAction $action
     *
     * @see ScheduledActionCommand
     */
    public function executeScheduledAction(ScheduledAction $action)
    {
        $params = unserialize($action->getParams());
        try {
            if ($action->getType() == self::SCHEDULED_ITEM_TYPE_SEND) {

                $pratica = $this->entityManager->getRepository('AppBundle:Pratica')->find($params['pratica']);

                if ($pratica instanceof Pratica) {
                    $this->protocolloService->protocollaPratica($pratica);
                }

            } elseif ($action->getType() == self::SCHEDULED_ITEM_TYPE_UPDATE) {

                $pratica = $this->entityManager->getRepository('AppBundle:Pratica')->find($params['pratica']);

                if ($pratica instanceof Pratica) {
                    $this->protocolloService->protocollaRisposta($pratica);
                }

            } elseif ($action->getType() == self::SCHEDULED_ITEM_TYPE_UPLOAD) {

                $allegato = $this->entityManager->getRepository('AppBundle:Allegato')->find($params['allegato']);
                $pratica = $this->entityManager->getRepository('AppBundle:Pratica')->find($params['pratica']);

                if ($pratica instanceof Pratica && $allegato instanceof AllegatoInterface) {
                    $this->protocolloService->protocollaAllegato($pratica, $allegato);
                }

            }
        }catch(AlreadySentException $e){
            $this->logger->warning($e->getMessage());
        }
    }

}
