<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Ente;
use AppBundle\Entity\Erogatore;
use Doctrine\Common\Collections\Collection;

/**
 * Class ErogatoriTest
 */
class ErogatoriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testErogatoreHasManyEnte()
    {
        $erogatore = new Erogatore();
        $this->assertInstanceOf(Collection::class, $erogatore->getEnti());
    }

    /**
     * @test
     */
    public function testErogatoreHasManyServizio()
    {
        $erogatore = new Erogatore();
        $this->assertInstanceOf(Collection::class, $erogatore->getServizi());
    }

    /**
     * @test
     */
    public function testEnteCanBeAddedToErogatore()
    {
        $erogatore = new Erogatore();
        $ente = new Ente();
        $this->assertEquals(0, $erogatore->getEnti()->count());
        $erogatore->addEnte($ente);
        $this->assertEquals(1, $erogatore->getEnti()->count());
    }

    /**
     * @test
     */
    public function testEnteCanBeRemovedFromErogatore()
    {
        $erogatore = new Erogatore();
        $ente = new Ente();
        $this->assertEquals(0, $erogatore->getEnti()->count());
        $erogatore->addEnte($ente);
        $this->assertEquals(1, $erogatore->getEnti()->count());
        $erogatore->removeEnte($ente);
        $this->assertEquals(0, $erogatore->getEnti()->count());
    }

    /**
     * @test
     */
    public function testErogatoreCanHaveAName()
    {
        $erogatore = new Erogatore();
        $name = "Consorzio della tristezza rodigina";
        $erogatore->setName($name);
        $this->assertEquals($name, $erogatore->getName());
    }
}
