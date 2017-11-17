<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class OperatoreUserTest
 */
class OperatoreUserTest extends AbstractAppTestCase
{

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->cleanDb(Pratica::class);
        $this->cleanDb(User::class);
    }

    /**
     * @test
     */
    public function testCanStoreServicesIds()
    {
        $operatore = new OperatoreUser();
        $operatore->setServiziAbilitati(new ArrayCollection([
            Uuid::uuid4().'',
            Uuid::uuid4().'',
        ]));

        $operatore->setUsername('pippo')
            ->setPlainPassword('pippo')
            ->setEmail(md5(rand(0, 1000).microtime()).'some@fake.email')
            ->setNome('a')
            ->setCognome('b')
            ->setEnabled(true);

        $this->em->persist($operatore);
        $this->em->flush();
        $this->em->refresh($operatore);
        $this->assertInstanceOf(Collection::class, $operatore->getServiziAbilitati());
    }
}
