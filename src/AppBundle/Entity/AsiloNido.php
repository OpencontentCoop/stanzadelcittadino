<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="asilo_nido")
 * @ORM\HasLifecycleCallbacks
 */
class AsiloNido
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text" , nullable=true)
     */
    private $schedaInformativa;

    /**
     * @var string
     * @ORM\Column(type="text" , nullable=true)
     */
    private $orari;


    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AsiloNido
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSchedaInformativa()
    {
        return $this->schedaInformativa;
    }

    /**
     * @param string $schedaInformativa
     *
     * @return AsiloNido
     */
    public function setSchedaInformativa($schedaInformativa)
    {
        $this->schedaInformativa = $schedaInformativa;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrari()
    {
        if (!is_array($this->orari)) {
            $this->parseOrariStringIntoArray();
        }

        return $this->orari;
    }

    /**
     * @param string[] $orari
     *
     * @return AsiloNido
     */
    public function setOrari($orari)
    {
        $this->orari = $orari;

        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function convertOrariToString()
    {
        $this->orari = serialize($this->getOrari());
    }

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     */
    public function parseOrariStringIntoArray()
    {
        $this->orari = unserialize($this->orari);
    }
}
