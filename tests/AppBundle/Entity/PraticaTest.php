<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\Pratica;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * Class PraticaTest
 */
class PraticaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
//        ClockMock::register(Pratica::class);
//        ClockMock::register(self::class);
//        ClockMock::withClockMock(true);
    }

    /**
     * @test
     */
    public function testCanSetAndGetData()
    {
        $pratica = new Pratica();
        $this->assertNull($pratica->getData());
        $data = ['a'];
        $this->assertEquals($data,$pratica->setData($data)->getData());
    }

    /**
     * @test
     */
    public function testAddingAllegatoAlsoAddsPraticaToAllegato()
    {
        $pratica = new Pratica();
        $allegato = new Allegato();
        $this->assertEquals(0, $pratica->getAllegati()->count());
        $this->assertEquals(0, $allegato->getPratiche()->count());
        $pratica->addAllegato($allegato);
        $this->assertTrue($pratica->getAllegati()->contains($allegato));
        $this->assertTrue($allegato->getPratiche()->contains($pratica));
    }

    /**
     * @test
     */
    public function testRemovingAllegatoAlsoRemovesPraticaFromAllegato()
    {
        $pratica = new Pratica();
        $allegato = new Allegato();
        $this->assertEquals(0, $pratica->getAllegati()->count());
        $this->assertEquals(0, $allegato->getPratiche()->count());
        $pratica->addAllegato($allegato);

        $pratica->removeAllegato($allegato);
        $this->assertTrue(!$pratica->getAllegati()->contains($allegato));
        $this->assertTrue(!$allegato->getPratiche()->contains($pratica));
    }

    /**
     * @test
     */
    public function testEveryStatusChangeIsStored()
    {
        $pratica = new Pratica();
        $time = time();
        $pratica->setStatus(Pratica::STATUS_DRAFT);
        $this->assertArrayHasKey($time, $pratica->getStoricoStati());
        $this->assertEquals(1, count($pratica->getStoricoStati()[$time]));
        $this->assertEquals(Pratica::STATUS_DRAFT, $pratica->getStoricoStati()[$time][0][0]);

        $pratica->setStatus(Pratica::STATUS_REGISTERED);
        $this->assertArrayHasKey($time, $pratica->getStoricoStati());
        $this->assertEquals(2, count($pratica->getStoricoStati()[$time]));
        $this->assertEquals(Pratica::STATUS_REGISTERED, $pratica->getStoricoStati()[$time][1][0]);

        sleep(1);
        $time = time();
        $pratica->setStatus(Pratica::STATUS_COMPLETE);
        $this->assertArrayHasKey($time, $pratica->getStoricoStati()->toArray());
        $this->assertEquals(1, count($pratica->getStoricoStati()->toArray()[$time]));
        $this->assertEquals(Pratica::STATUS_COMPLETE, $pratica->getStoricoStati()->toArray()[$time][0][0]);
    }

    /**
     * @test
     */
    public function testLatestStatusIsCorrectlyStored()
    {
        $pratica = new Pratica();
        $time = time();
        $pratica->setStatus(Pratica::STATUS_DRAFT);
        $this->assertEquals($time, $pratica->getLatestTimestampForStatus(Pratica::STATUS_DRAFT));
        $this->assertNull($pratica->getLatestTimestampForStatus(Pratica::STATUS_CANCELLED));
        $pratica->setStatus(Pratica::STATUS_CANCELLED);
        $this->assertEquals($time, $pratica->getLatestTimestampForStatus(Pratica::STATUS_CANCELLED));

        sleep(1);
        $time = time();
        $pratica->setStatus(Pratica::STATUS_DRAFT);
        $pratica->setStatus(Pratica::STATUS_COMPLETE);
        $this->assertEquals($time, $pratica->getLatestTimestampForStatus(Pratica::STATUS_DRAFT));
        $this->assertEquals($time, $pratica->getLatestTimestampForStatus(Pratica::STATUS_COMPLETE));
    }
}
