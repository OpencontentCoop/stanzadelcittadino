<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\AllegatoInterface;
use AppBundle\Entity\Pratica;
use AppBundle\Event\ProtocollaAllegatiOperatoreSuccessEvent;
use AppBundle\Event\ProtocollaPraticaSuccessEvent;
use AppBundle\Protocollo\Exception\AlreadyUploadException;
use AppBundle\Protocollo\ProtocolloEvents;
use AppBundle\Protocollo\ProtocolloHandlerInterface;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProtocolloService extends AbstractProtocolloService implements ProtocolloServiceInterface
{
    /**
     * @var ProtocolloHandlerInterface
     */
    protected $handler;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(
        ProtocolloHandlerInterface $handler,
        EntityManager $entityManager,
        LoggerInterface $logger,
        EventDispatcherInterface $dispatcher
    ) {
        $this->handler = $handler;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function protocollaPratica(Pratica $pratica)
    {
        $this->validatePratica($pratica);

        $this->handler->sendPraticaToProtocollo($pratica);

        $allegati = $pratica->getAllegati();
        foreach ($allegati as $allegato) {
            try {
                $this->validateUploadFile($pratica, $allegato);
                $this->handler->sendAllegatoToProtocollo($pratica, $allegato);
            }catch(AlreadyUploadException $e){}
        }

        $this->entityManager->persist($pratica);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(
            ProtocolloEvents::ON_PROTOCOLLA_PRATICA_SUCCESS,
            new ProtocollaPraticaSuccessEvent($pratica)
        );
    }

    public function protocollaRisposta(Pratica $pratica)
    {
        $this->validateRisposta($pratica);

        $this->handler->sendRispostaToProtocollo($pratica);

        $allegati = $pratica->getAllegatiOperatore();
        foreach ($allegati as $allegato) {
            try {
                $this->validateUploadFile($pratica, $allegato);
                $this->handler->sendAllegatoRispostaToProtocollo($pratica, $allegato);
            }catch(AlreadyUploadException $e){}
        }

        $this->entityManager->persist($pratica);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(
            ProtocolloEvents::ON_PROTOCOLLA_ALLEGATI_OPERATORE_SUCCESS,
            new ProtocollaAllegatiOperatoreSuccessEvent($pratica)
        );
    }

    public function protocollaAllegato(Pratica $pratica, AllegatoInterface $allegato)
    {
        $this->validatePraticaForUploadFile($pratica);
        $this->validateUploadFile($pratica, $allegato);

        $this->handler->sendAllegatoToProtocollo($pratica, $allegato);

        $this->entityManager->persist($pratica);
        $this->entityManager->flush();
    }

    public function getHandler()
    {
        return $this->handler;
    }

}
