<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;


use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Ente;
use AppBundle\Entity\Pratica;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\Translation\TranslatorInterface;

class MailerService
{
    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface $translator
     */
    private $translator;

    /**
     * @var TemplatingExtension
     */
    private $templating;

    /**
     * MailerService constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, TwigEngine $templating)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->templating = $templating;
    }

    /**
     * @param Pratica $pratica
     * @param $fromAddress
     * @param bool $resend
     *
     * @return int
     */
    public function dispatchMailForPratica(Pratica $pratica, $fromAddress, $resend = false)
    {
        $sentAmount = 0;
        if ($this->CPSUserHasValidContactEmail($pratica->getUser()) &&
            ($resend || !$this->CPSUserHasAlreadyBeenWarned($pratica))
        ) {
            $CPSUsermessage = $this->setupCPSUserMessage($pratica, $fromAddress);
            $sentAmount += $this->mailer->send($CPSUsermessage);
            $pratica->setLatestCPSCommunicationTimestamp(time());
        }

        if ($pratica->getOperatore() != null &&
            ($resend || !$this->operatoreUserHasAlreadyBeenWarned($pratica))
        ) {
            $operatoreUserMessage = $this->setupOperatoreUserMessage($pratica, $fromAddress);
            $sentAmount += $this->mailer->send($operatoreUserMessage);
            $pratica->setLatestOperatoreCommunicationTimestamp(time());
        }

        return $sentAmount;
    }

    /**
     * @param Pratica $pratica
     * @param $fromAddress
     * @return mixed
     */
    private function setupCPSUserMessage(Pratica $pratica, $fromAddress)
    {
        $toEmail = $pratica->getUser()->getEmailContatto();
        $toName = $pratica->getUser()->getFullName();

        $ente = $pratica->getEnte();
        $fromName = $ente instanceof Ente ? $ente->getName() : null;

        $message = \Swift_Message::newInstance()
            ->setSubject($this->translator->trans('pratica.email.status_change.subject'))
            ->setFrom($fromAddress, $fromName)
            ->setTo($toEmail, $toName)
            ->setBody(
                $this->templating->render(
                    'AppBundle:Emails/User:pratica_status_change.html.twig',
                    array(
                        'pratica' => $pratica,
                        'user_name'    => $pratica->getUser()->getFullName()
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->templating->render(
                    'AppBundle:Emails/User:pratica_status_change.txt.twig',
                    array(
                        'pratica' => $pratica,
                        'user_name'    => $pratica->getUser()->getFullName()
                    )
                ),
                'text/plain'
            );
        return $message;
    }


    private function setupOperatoreUserMessage(Pratica $pratica, $fromAddress)
    {
        $toEmail = $pratica->getOperatore()->getEmail();
        $toName = $pratica->getOperatore()->getFullName();

        $ente = $pratica->getEnte();
        $fromName = $ente instanceof Ente ? $ente->getName() : null;

        $message = \Swift_Message::newInstance()
            ->setSubject($this->translator->trans('pratica.email.status_change.subject'))
            ->setFrom($fromAddress, $fromName)
            ->setTo($toEmail, $toName)
            ->setBody(
                $this->templating->render(
                    'AppBundle:Emails/Operatore:pratica_status_change.html.twig',
                    array(
                        'pratica' => $pratica,
                        'user_name'    => $pratica->getOperatore()->getFullName()
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->templating->render(
                    'AppBundle:Emails/Operatore:pratica_status_change.txt.twig',
                    array(
                        'pratica' => $pratica,
                        'user_name'    => $pratica->getOperatore()->getFullName()
                    )
                ),
                'text/plain'
            );
        return $message;
    }

    private function CPSUserHasAlreadyBeenWarned(Pratica $pratica)
    {
        return $pratica->getLatestCPSCommunicationTimestamp() >= $pratica->getLatestStatusChangeTimestamp();
    }

    private function operatoreUserHasAlreadyBeenWarned(Pratica $pratica)
    {
        return $pratica->getLatestOperatoreCommunicationTimestamp() >= $pratica->getLatestStatusChangeTimestamp();
    }

    private function CPSUserHasValidContactEmail(CPSUser $user)
    {
        $email = $user->getEmailContatto();

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

}
