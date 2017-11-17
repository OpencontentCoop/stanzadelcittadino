<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Pratica;

class ComponenteNucleoFamiliareTest extends \PHPUnit_Framework_TestCase
{
    public function testComponenteCanBeBoundToPratica()
    {
        $componente = new ComponenteNucleoFamiliare();
        $this->assertNotNull($componente->getId());
        $this->assertNull($componente->getPratica());
        $pratica = new Pratica();
        $this->assertEquals($pratica, $componente->setPratica($pratica)->getPratica());
    }
}
