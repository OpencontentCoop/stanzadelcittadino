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

/**
 * Class OperatoreUser
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @package AppBundle\Entity
 */
class OperatoreUser extends User
{

    /**
     * @ORM\ManyToOne(targetEntity="Ente", inversedBy="operatori")
     * @ORM\JoinColumn(name="ente_id", referencedColumnName="id", nullable=true)
     */
    private $ente;

    /**
     * @var string
     *
     * @ORM\Column(name="ambito", type="string")
     */
    private $ambito;

    /**
     * @var Collection
     *
     * @ORM\Column(name="servizi_abilitati", type="text")
     */
    private $serviziAbilitati;

    /**
     * OperatoreUser constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::USER_TYPE_OPERATORE;
        $this->addRole(User::ROLE_OPERATORE);
        $this->serviziAbilitati = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getEnte()
    {
        return $this->ente;
    }

    /**
     * @param Ente $ente
     * @return OperatoreUser
     */
    public function setEnte(Ente $ente)
    {
        $this->ente = $ente;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmbito()
    {
        return $this->ambito;
    }

    /**
     * @param string $ambito
     */
    public function setAmbito($ambito)
    {
        $this->ambito = $ambito;
    }

    /**
     * @return Collection
     */
    public function getServiziAbilitati(): Collection
    {
        if (!($this->serviziAbilitati instanceof Collection)) {
            $this->serviziAbilitati = new ArrayCollection(json_decode($this->serviziAbilitati));
        }

        return $this->serviziAbilitati;
    }

    /**
     * @param Collection $servizi
     * @return $this
     */
    public function setServiziAbilitati(Collection $servizi)
    {
        $this->serviziAbilitati = $servizi;

        return $this;
    }

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     */
    public function parseServizi()
    {
        if (!($this->serviziAbilitati instanceof Collection)) {
            $this->serviziAbilitati = new ArrayCollection(json_decode($this->serviziAbilitati));
        }
    }

    /**
     * @ORM\PreFlush()
     */
    public function serializeServizi()
    {
        if ($this->serviziAbilitati instanceof Collection) {
            $this->serviziAbilitati = json_encode($this->getServiziAbilitati()->toArray());
        }
    }
}
