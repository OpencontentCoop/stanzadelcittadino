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
 * @ORM\Table(name="componente_nucleo_familiare")
 */
class ComponenteNucleoFamiliare
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string" , nullable=true)
     */
    private $nome;

    /**
     * @var string
     * @ORM\Column(type="string" , nullable=true)
     */
    private $cognome;

    /**
     * @var string
     * @ORM\Column(type="string" , nullable=true)
     */
    private $rapportoParentela;

    /**
     * @var string
     * @ORM\Column(type="string" , nullable=true)
     */
    private $codiceFiscale;

    /**
     * @ORM\ManyToOne(inversedBy="nucleoFamiliare", targetEntity="AppBundle\Entity\Pratica")
     * @var Pratica $pratica
     */
    private $pratica;

    /**
     * ComponenteNucleoFamiliare constructor.
     */
    public function __construct()
    {
        if (!$this->id) {
            $this->id = Uuid::uuid4();
        }
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     *
     * @return ComponenteNucleoFamiliare
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return string
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * @param string $cognome
     *
     * @return ComponenteNucleoFamiliare
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;

        return $this;
    }

    /**
     * @return string
     */
    public function getRapportoParentela()
    {
        return $this->rapportoParentela;
    }

    /**
     * @param string $rapportoParentela
     *
     * @return ComponenteNucleoFamiliare
     */
    public function setRapportoParentela($rapportoParentela)
    {
        $this->rapportoParentela = $rapportoParentela;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodiceFiscale()
    {
        return $this->codiceFiscale;
    }

    /**
     * @param string $codiceFiscale
     *
     * @return ComponenteNucleoFamiliare
     */
    public function setCodiceFiscale($codiceFiscale)
    {
        $this->codiceFiscale = $codiceFiscale;

        return $this;
    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Pratica
     */
    public function getPratica()
    {
        return $this->pratica;
    }

    /**
     * @param mixed $pratica
     * @return ComponenteNucleoFamiliare
     */
    public function setPratica($pratica)
    {
        $this->pratica = $pratica;

        return $this;
    }

}
