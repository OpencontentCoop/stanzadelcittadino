<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="termini_utilizzo")
 */
class TerminiUtilizzo
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $mandatory;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $latestRevision;

    /**
     * Servizio constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->latestRevision = time();
        $this->mandatory = false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->mandatory;
    }

    /**
     * @param bool $mandatory
     * @return $this
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * @return int
     */
    public function getLatestRevisionTime(): int
    {
        return $this->latestRevision;
    }

    /**
     * @param int $latestRevision
     * @return $this
     */
    public function setLatestRevision($latestRevision)
    {
        $this->latestRevision = $latestRevision;

        return $this;
    }
}
