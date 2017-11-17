<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Services;

use AppBundle\Entity\Pratica;
use AppBundle\Services\PraticaStatusService;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Tests\AppBundle\Base\AbstractAppTestCase;

class PraticaStatusServiceTest extends AbstractAppTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testPraticaStatusServiceChangeStatus()
    {
        $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);
        $service = $this->getMockPraticaStatusService();
        $service->setNewStatus($pratica, Pratica::STATUS_SUBMITTED);

        $this->assertEquals(Pratica::STATUS_SUBMITTED, $pratica->getStatus());
    }

    private function getMockPraticaStatusService()
    {
        // FIXME: testare tutti i casi
        $dispatcher = $this->getMockBuilder(TraceableEventDispatcher::class)->disableOriginalConstructor()->getMock();
        $dispatcher->expects($this->exactly(1))->method('dispatch');
        return
            new PraticaStatusService(
                $this->em,
                $this->getMockLogger(),
                $dispatcher
            );
    }
}
