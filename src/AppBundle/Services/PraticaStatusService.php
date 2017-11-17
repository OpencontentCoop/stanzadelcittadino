<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\Pratica;
use AppBundle\Entity\StatusChange;
use AppBundle\Event\PraticaOnChangeStatusEvent;
use AppBundle\Logging\LogConstants;
use AppBundle\PraticaEvents;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PraticaStatusService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    private $validChangeStatusList;

    /**
     * PraticaStatusService constructor.
     *
     * @param EntityManager $entityManager
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, LoggerInterface $logger, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;

        $this->validChangeStatusList = [
            [Pratica::STATUS_DRAFT => Pratica::STATUS_SUBMITTED],
            [Pratica::STATUS_SUBMITTED => Pratica::STATUS_REGISTERED],
            [Pratica::STATUS_REGISTERED => Pratica::STATUS_PENDING],
            [Pratica::STATUS_PENDING => Pratica::STATUS_COMPLETE],
            [Pratica::STATUS_PENDING => Pratica::STATUS_CANCELLED],
            [Pratica::STATUS_PENDING => Pratica::STATUS_COMPLETE_WAITALLEGATIOPERATORE],
            [Pratica::STATUS_PENDING => Pratica::STATUS_CANCELLED_WAITALLEGATIOPERATORE],
            [Pratica::STATUS_COMPLETE_WAITALLEGATIOPERATORE => Pratica::STATUS_COMPLETE],
            [Pratica::STATUS_CANCELLED_WAITALLEGATIOPERATORE => Pratica::STATUS_CANCELLED],
        ];
    }

    public function setNewStatus(Pratica $pratica, $status, StatusChange $statusChange = null)
    {
        $beforeStatus = $pratica->getStatus();
        $beforeStatusIdentifier = $pratica->getStatusName();

        $states = Pratica::getStatuses();
        if (isset($states[$status]['id'])) {
            $afterStatus = $states[$status]['id'];
            $afterStatusIdentifier = $states[$status]['identifier'];
        }else{
            throw new \Exception("Pratica status $status not found");
        }

        $this->validateChangeStatus($pratica, $afterStatus);

        $this->entityManager->beginTransaction();

        try{
            $pratica->setStatus($afterStatus, $statusChange);

            $this->entityManager->persist($pratica);
            $this->entityManager->flush();

            $this->entityManager->refresh($pratica);

            $this->dispatcher->dispatch(
                PraticaEvents::ON_STATUS_CHANGE,
                new PraticaOnChangeStatusEvent($pratica, $afterStatus)
            );

            $this->entityManager->commit();

            $this->logger->info(
                LogConstants::PRATICA_CHANGED_STATUS,
                [
                    'pratica' => $pratica->getId(),
                    'before_status' => $beforeStatusIdentifier,
                    'after_status' => $afterStatusIdentifier,

                ]
            );
        }catch(\Exception $e){
            $this->entityManager->rollback();

            $this->logger->info(
                LogConstants::PRATICA_CHANGED_STATUS_FAILED,
                [
                    'pratica' => $pratica->getId(),
                    'before_status' => $beforeStatusIdentifier,
                    'after_status' => $afterStatusIdentifier,
                    'error' => $e->getMessage(),
                ]
            );
        }


    }

    /**
     * @return array
     */
    public function getValidChangeStatusList()
    {
        return $this->validChangeStatusList;
    }

    private function validateChangeStatus(Pratica $pratica, $afterStatus)
    {
        $beforeStatus = $pratica->getStatus();
        foreach($this->validChangeStatusList as $change) {
            foreach ($change as $before => $after) {
                if ($before == $beforeStatus && $after == $afterStatus) {
                    return true;
                }
            }
        }
        throw new \Exception("Invalid pratica status change form $beforeStatus to $afterStatus");
    }

}
