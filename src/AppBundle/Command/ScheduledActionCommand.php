<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Command;

use AppBundle\Entity\ScheduledAction;
use AppBundle\ScheduledAction\ScheduledActionHandlerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ScheduledActionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ocsdc:scheduled_action:execute')
            ->setDescription('Execute all scheduled actions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost($this->getContainer()->getParameter('ocsdc_host'));
        $context->setScheme($this->getContainer()->getParameter('ocsdc_scheme'));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository('AppBundle:ScheduledAction');

        /** @var ScheduledAction[] $actions */
        $actions = $repository->findBy([], ['createdAt' => 'ASC']);
        foreach($actions as $action){
            $service = $this->getContainer()->get($action->getService());
            if ($service instanceof ScheduledActionHandlerInterface){
                $output->writeln('Execute ' . $action->getType() . ' with params ' . $action->getParams());
                try {
                    $service->executeScheduledAction($action);
                    $em->remove($action);
                }catch(\Exception $e){
                    $this->getContainer()->get('logger')->error($e->getMessage());
                }
            }
        }
        $em->flush();
    }
}
