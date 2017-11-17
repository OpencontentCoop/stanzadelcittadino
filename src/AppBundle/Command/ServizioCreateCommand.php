<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Command;

use AppBundle\Entity\Servizio;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ServizioCreateCommand
 */
class ServizioCreateCommand extends ContainerAwareCommand
{

    const HELP = <<<HELP
Crea un record nella tabella servizi, lo marca come disabilitato, va abilitato a mano una volta che si è creato il flow corrispondente.
I valori necessari sono
Lo slug del servizio (sarà usato anche per le rotte)
Il nome del servizio (testo libero)
L'FQCN della classe persistita che rappresenta il servizio (quella in /Entity/...)
Il nome del servizio che rappresenta il flow

FQCN e flow possono essere creati successivamente, ma finché non ci sono il sistema darà una serie di errori e sarà inutilizzabile (nota utile di fatto solo per lo sviluppo)
HELP;

    protected function configure()
    {
        $this
            ->setName('ocsdc:crea-servizio')
            ->setDescription('Crea un record nella tabella servizi')
            ->setHelp(self::HELP)
            ->addArgument('slug', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('fcqn', InputArgument::REQUIRED)
            ->addArgument('flow', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Crea Servizi',
            '============',
            '',
        ]);

        $output->writeln('Slug: '.$input->getArgument('slug'));

        $output->writeln('Retrieving the db connection');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $newServizio = new Servizio();
        $newServizio->setSlug($input->getArgument('slug'));
        $newServizio->setName($input->getArgument('name'));
        $newServizio->setPraticaFCQN($input->getArgument('fcqn'));
        $newServizio->setPraticaFlowServiceName($input->getArgument('flow'));

        $repo = $em->getRepository('AppBundle:Servizio');
        if ($repo->findByPraticaFCQN($newServizio->getPraticaFCQN())){
            $output->writeln(sprintf('Il servizio: %s esiste già', $input->getArgument('slug')));
        }else{
            $em->persist($newServizio);
            $em->flush();
            $output->writeln(sprintf('Servizio: %s creato, manca il flusso', $input->getArgument('slug')));
        }

        $fcqn = $newServizio->getPraticaFCQN();
        $parts = explode("\\", $fcqn);
        $className = array_pop($parts);

        $slug = $newServizio->getSlug();
        $slugNormalized = str_replace('-', '_', $slug);
        $slugConstant = strtoupper($slugNormalized);

        $flow = $newServizio->getPraticaFlowServiceName();
        $flowClassName = "AppBundle\\Form\\{$className}\\{$className}Flow";

        $output->writeln("\n1 - Modifica l'annotazione DiscriminatorMap di AppBundle\\Entity\\Pratica: \n\n @ORM\\DiscriminatorMap({ ..., \"{$slugNormalized}\" = \"{$className}\"}) \n");
        $output->writeln("\n2 - Aggiungi in AppBundle\\Entity\\Pratica la costante \n\n const TYPE_{$slugConstant} = \"{$slugNormalized}\" \n");
        $output->writeln("\n3 - Crea la classe {$fcqn} come estensione di AppBundle\\Entity\\Pratica e aggiungi i campi che servono");
        $output->writeln("\n4 - Crea la {$flowClassName} classe estensione di AppBundle\\Form\\Base\\PraticaFlow e configura il flow");
        $output->writeln("\n5 - Registra il servizio in services.yml \n\n {$flow}: \n class: {$flowClassName} \n parent: craue.form.flow \n arguments: [\"@logger\",\"@translator\"] \n");
        $output->writeln("\n6 - Crea il template Resources/views/Pratiche/summary/{$className}.html.twig per la visualizzazione dei sommari");
        $output->writeln("\n7 - Crea il template Resources/views/Pratiche/pdf/{$className}.html.twig per il render dei pdf");
        $output->writeln("\n8 - Crea la classe di test Tests\\Flows\\{$className}Test");
    }
}
