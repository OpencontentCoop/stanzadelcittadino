<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Erogatore
 * @ORM\Entity
 * @ORM\Table(name="erogatore")
 * @ORM\HasLifecycleCallbacks
 */
class Erogatore
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Ente", cascade={"persist"}, inversedBy="erogatori")
     * @var Collection
     */
    private $enti;

    /**
     * @ORM\ManyToMany(targetEntity="Servizio", mappedBy="erogatori")
     * @var Collection
     */
    private $servizi;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * Servizio constructor.
     */
    public function __construct()
    {
        if (!$this->id) {
            $this->id = Uuid::uuid4();
        }
        $this->enti = new ArrayCollection();
        $this->servizi = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection
     */
    public function getEnti(): Collection
    {
        return $this->enti;
    }

    /**
     * @param Ente $ente
     * @return $this
     */
    public function addEnte(Ente $ente)
    {
        if (!$this->enti->contains($ente)) {
            $this->enti->add($ente);
        }

        return $this;
    }

    /**
     * @param Ente $ente
     * @return $this
     */
    public function removeEnte(Ente $ente)
    {
        if ($this->enti->contains($ente)) {
            $this->enti->removeElement($ente);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getServizi(): Collection
    {
        return $this->servizi;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
