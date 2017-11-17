<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Pratica;
use AppBundle\Event\ProtocollaAllegatiOperatoreSuccessEvent;
use AppBundle\Event\ProtocollaPraticaSuccessEvent;
use AppBundle\Protocollo\ProtocolloEvents;
use AppBundle\Services\PraticaStatusService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProtocolloSuccessSubscriber implements EventSubscriberInterface
{
    private $praticaStatusService;

    private $logger;

    public function __construct(PraticaStatusService $praticaStatusService, LoggerInterface $logger)
    {
        $this->praticaStatusService = $praticaStatusService;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return[
            ProtocolloEvents::ON_PROTOCOLLA_PRATICA_SUCCESS => ['onProtocollaPratica'],
            ProtocolloEvents::ON_PROTOCOLLA_ALLEGATI_OPERATORE_SUCCESS => ['onProtocollaAllegatiOperatore'],
        ];
    }

    public function onProtocollaPratica(ProtocollaPraticaSuccessEvent $event)
    {
        $this->praticaStatusService->setNewStatus($event->getPratica(), Pratica::STATUS_REGISTERED);
    }

    public function onProtocollaAllegatiOperatore(ProtocollaAllegatiOperatoreSuccessEvent $event)
    {
        $pratica = $event->getPratica();
        if ($pratica->getEsito())
        {
            $this->praticaStatusService->setNewStatus($pratica, Pratica::STATUS_COMPLETE);
        }
        else
        {
            $this->praticaStatusService->setNewStatus($pratica, Pratica::STATUS_CANCELLED);
        }

    }
}
