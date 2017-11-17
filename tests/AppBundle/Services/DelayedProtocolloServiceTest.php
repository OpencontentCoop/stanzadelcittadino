<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Services;

use AppBundle\Command\ScheduledActionCommand;
use AppBundle\Entity\Allegato;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\ScheduledAction;
use AppBundle\Entity\User;
use AppBundle\Services\DelayedProtocolloService;
use AppBundle\Services\ProtocolloService;
use Tests\AppBundle\Base\AbstractAppTestCase;
use AppBundle\Protocollo\PiTreProtocolloHandler;

class DelayedProtocolloServiceTest extends AbstractAppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->cleanDb(ScheduledAction::class);
        $this->cleanDb(Allegato::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(User::class);
    }

    /**
     * @test
     */
    public function testDelayedProtocolloService()
    {
        $user = $this->createCPSUser();
        $pratica = $this->createSubmittedPraticaForUser($user);

        $allegati = $this->setupNeededAllegatiForAllInvolvedUsers(3, $user);
        foreach ($allegati as $allegato) {
            $pratica->addAllegato($allegato);
        }

        $this->getMockDelayedProtocollo()->protocollaPratica($pratica);

        $repo = $this->em->getRepository('AppBundle:ScheduledAction');
        $this->assertEquals(1, count($repo->findAll()));
    }

    /**
     * @test
     * @expectedException \AppBundle\Protocollo\Exception\AlreadyScheduledException
     */
    public function testDelayedProtocolloServiceCannotResend()
    {
        $user = $this->createCPSUser();
        $pratica = $this->createSubmittedPraticaForUser($user);

        $this->getMockDelayedProtocollo()->protocollaPratica($pratica);

        $this->getMockDelayedProtocollo()->protocollaPratica($pratica);

    }

    /**
     * @test
     */
    public function testDelayedProtocolloServiceSendAllegati()
    {
        $user = $this->createCPSUser();
        $pratica = $this->createSubmittedPraticaForUser($user);

        $allegati = $this->setupNeededAllegatiForAllInvolvedUsers(3, $user);
        foreach ($allegati as $allegato) {
            $pratica->addAllegato($allegato);
        }

        $this->getMockDelayedProtocollo()->protocollaPratica($pratica);

        $repo = $this->em->getRepository('AppBundle:ScheduledAction');
        $this->assertEquals(1, count($repo->findAll()));

        $this->executeCron(4); //pratica + 3 allegati

        $repo = $this->em->getRepository('AppBundle:ScheduledAction');
        $this->assertEquals(0, count($repo->findAll()));
        $this->assertEquals(Pratica::STATUS_REGISTERED, $pratica->getStatus());
        $this->assertEquals(3, count($pratica->getNumeriProtocollo()));


        $allegati = $this->setupNeededAllegatiForAllInvolvedUsers(3, $user);
        foreach ($allegati as $allegato) {
            $pratica->addAllegato($allegato);
            $this->getMockDelayedProtocollo()->protocollaAllegato($pratica, $allegato);
        }

        $repo = $this->em->getRepository('AppBundle:ScheduledAction');
        $this->assertEquals(3, count($repo->findAll()));

        $this->executeCron(3); // tre allegati
        $this->assertEquals(6, count($pratica->getNumeriProtocollo()));
    }

    private function executeCron($expectedRemoteCalls)
    {
        $responses = [];
        for($i = 1; $i <= $expectedRemoteCalls; $i++){
            $responses[] = $this->getPiTreSuccessResponse();
        }

        $service = $this->getMockDelayedProtocollo($responses);
        $repository = $this->em->getRepository('AppBundle:ScheduledAction');
        /** @var ScheduledAction[] $actions */
        $actions = $repository->findBy([], ['createdAt' => 'ASC']);
        foreach($actions as $action){
            if ($action->getService() == 'ocsdc.protocollo'){
                $service->executeScheduledAction($action);
                $this->em->remove($action);
            }
        }
        $this->em->flush();
    }

    private function getMockDelayedProtocollo($responses = array())
    {
        return
            new DelayedProtocolloService(
                $this->getMockProtocollo($responses),
                $this->em,
                $this->getMockLogger()
            );

    }

    private function getMockProtocollo($responses = array(), $dispatcher = null)
    {
        if (!$dispatcher){
            $dispatcher = $this->container->get('event_dispatcher');
        }
        return
            new ProtocolloService(
                new PiTreProtocolloHandler($this->getMockGuzzleClient($responses)),
                $this->em,
                $this->getMockLogger(),
                $dispatcher
            );

    }

}
