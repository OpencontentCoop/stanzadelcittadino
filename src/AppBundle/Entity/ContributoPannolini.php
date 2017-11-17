<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContributoPannolini
 * @ORM\Entity
 */
class ContributoPannolini extends Pratica
{
//bin/console o:crea-s contributo_pannolini "Contributo Acquisto Pannolini" "AppBundle\Entity\ContributoPannolini" ocsdc.form.flow.contributopannolini
    const PANNOLINO_LAVABILE = 1;
    const PANNOLINO_BIOPANNOLINO = 2;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $bambinoNome;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $bambinoCognome;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $bambinoLuogoNascita;

    /**
     * @var string
     * @ORM\Column(type="date", nullable=true)
     */
    private $bambinoDataNascita;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $tipoPannolini;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $nomePuntoVendita;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataAcquisto;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $totaleSpesa;

    public function __construct()
    {
        parent::__construct();
        $this->tipoPannolini = self::PANNOLINO_LAVABILE;
    }

    /**
     * @return string
     */
    public function getBambinoNome()
    {
        return $this->bambinoNome;
    }

    /**
     * @param string $bambinoNome
     */
    public function setBambinoNome($bambinoNome)
    {
        $this->bambinoNome = $bambinoNome;
        return $this;
    }

    /**
     * @return string
     */
    public function getBambinoCognome()
    {
        return $this->bambinoCognome;
    }

    /**
     * @param string $bambinoCognome
     */
    public function setBambinoCognome($bambinoCognome)
    {
        $this->bambinoCognome = $bambinoCognome;
        return $this;
    }

    /**
     * @return string
     */
    public function getBambinoLuogoNascita()
    {
        return $this->bambinoLuogoNascita;
    }

    /**
     * @param string $bambinoLuogoNascita
     */
    public function setBambinoLuogoNascita($bambinoLuogoNascita)
    {
        $this->bambinoLuogoNascita = $bambinoLuogoNascita;
        return $this;
    }

    /**
     * @return string
     */
    public function getBambinoDataNascita()
    {
        return $this->bambinoDataNascita;
    }

    /**
     * @param string $bambinoDataNascita
     */
    public function setBambinoDataNascita($bambinoDataNascita)
    {
        $this->bambinoDataNascita = $bambinoDataNascita;
        return $this;
    }

    /**
     * @return int
     */
    public function getTipoPannolini()
    {
        return $this->tipoPannolini;
    }

    /**
     * @param int $tipoPannolini
     */
    public function setTipoPannolini($tipoPannolini)
    {
        $this->tipoPannolini = $tipoPannolini;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomePuntoVendita()
    {
        return $this->nomePuntoVendita;
    }

    /**
     * @param string $nomePuntoVendita
     */
    public function setNomePuntoVendita($nomePuntoVendita)
    {
        $this->nomePuntoVendita = $nomePuntoVendita;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataAcquisto()
    {
        return $this->dataAcquisto;
    }

    /**
     * @param \DateTime $dataAcquisto
     */
    public function setDataAcquisto($dataAcquisto)
    {
        $this->dataAcquisto = $dataAcquisto;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotaleSpesa()
    {
        return $this->totaleSpesa / 100;
    }

    /**
     * @param int $totaleSpesa
     */
    public function setTotaleSpesa($totaleSpesa)
    {
        $this->totaleSpesa = $totaleSpesa * 100;
        return $this;
    }
}
