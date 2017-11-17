<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Command;

use AppBundle\Entity\OperatoreUser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Cache\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Class OperatoreCreateCommand
 */
class OperatoreCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ocsdc:crea-operatore')
            ->setDescription('Crea un record nella tabella utente di tipo operatore');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('Inserisci il nome ', 'Mario');
        $nome = $helper->ask($input, $output, $question);

        $question = new Question('Inserisci il cognome ', 'Rossi');
        $cognome = $helper->ask($input, $output, $question);

        $question = new Question('Inserisci l\'indirizzo email ', 'gabriele@opencontent.it');
        $email = $helper->ask($input, $output, $question);

        $question = new Question('Inserisci lo username ', 'mariorossi');
        $username = $helper->ask($input, $output, $question);

        $question = new Question('Inserisci la password ', 'mariorossi');
        $password = $helper->ask($input, $output, $question);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Ente');
        $entiEntites = $repo->findAll();
        $enti = [];
        foreach($entiEntites as $entiEntity){
            $enti[] = $entiEntity->getName();
        }
        $question = new ChoiceQuestion('Seleziona ente di riferimento', $enti, 0);
        $enteName = $helper->ask($input, $output, $question);
        $ente = $repo->findOneByName($enteName);
        if (!$ente){
            throw new InvalidArgumentException("Ente $enteName non trovato");
        }

        $um = $this->getContainer()->get('fos_user.user_manager');

        $user = (new OperatoreUser())
            ->setUsername($username)
            ->setPlainPassword($password)
            ->setEmail($email)
            ->setNome($nome)
            ->setEnte($ente)
            ->setCognome($cognome)
            ->setEnabled(true);

        try {
            $um->updateUser($user);
            $output->writeln('Ok: generato nuovo operatore');
        } catch (\Exception $e) {
            $output->writeln('Errore: ' . $e->getMessage());
        }
    }

}
