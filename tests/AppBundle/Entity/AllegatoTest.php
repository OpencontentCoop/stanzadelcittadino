<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\Allegato;
use AppBundle\Entity\Pratica;
use Symfony\Component\HttpFoundation\File\File;
use Tests\AppBundle\Base\AbstractAppTestCase;

class AllegatoTest extends AbstractAppTestCase{
    public function setUp()
    {
        parent::setUp();
    }

    public function testDateFieldsGetUpdatedMagically()
    {
        $allegato = new Allegato();
        $originalDate = new \DateTime('last year');
        $allegato->setUpdatedAt($originalDate);
        $mockedFile = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();
        $allegato->setFile($mockedFile);
        $newUpdatedAt = $allegato->getUpdatedAt();
        $this->assertGreaterThan($originalDate, $newUpdatedAt);
    }

    public function testCanAddAndRemovePratica()
    {
        $allegato = new Allegato();
        $pratica = new Pratica();
        $this->assertEquals(0, $allegato->getPratiche()->count());
        $allegato->addPratica($pratica);
        $this->assertEquals(1, $allegato->getPratiche()->count());
        $allegato->addPratica($pratica);
        $this->assertEquals(1, $allegato->getPratiche()->count());
        $allegato->removePratica($pratica);
        $this->assertEquals(0, $allegato->getPratiche()->count());

    }
}
