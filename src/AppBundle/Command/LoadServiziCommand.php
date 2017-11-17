<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Command;

use AppBundle\DataFixtures\ORM\LoadData;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadServiziCommand extends ContainerAwareCommand{
    protected function configure()
    {
        $this
            ->setName('ocsdc:carica-servizi')
            ->setDescription('Carica Servizi, enti e associazioni fra i deu, dal foglio excel');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $loader = new LoadData();
        $loader->loadEnti($manager);
        $loader->loadServizi($manager);
        $counters = $loader->getCounters();
        $output->writeln('Servizi caricati: '.$counters['servizi']['new']);
        $output->writeln('Servizi aggiornati: '.$counters['servizi']['updated']);

    }
}
