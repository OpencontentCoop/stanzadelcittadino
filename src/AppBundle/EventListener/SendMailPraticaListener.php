<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\EventListener;

use AppBundle\Event\PraticaOnChangeStatusEvent;
use AppBundle\Services\MailerService;
use Psr\Log\LoggerInterface;

class SendMailPraticaListener
{
    /**
     * @var MailerService
     */
    private $mailer;

    private $defaultSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MailerService $mailer, $defaultSender, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->defaultSender = $defaultSender;
        $this->logger = $logger;
    }

    public function onStatusChange(PraticaOnChangeStatusEvent $event)
    {
        $pratica = $event->getPratica();
        $this->mailer->dispatchMailForPratica($pratica, $this->defaultSender);
    }
}
