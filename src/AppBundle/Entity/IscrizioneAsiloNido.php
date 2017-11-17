<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class IscrizioneAsiloNido
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class IscrizioneAsiloNido extends Pratica
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accettoUtilizzo;

    /**
     * @var AsiloNido
     *
     * @ORM\ManyToOne(targetEntity="AsiloNido")
     * @ORM\JoinColumn(name="asilo_id", referencedColumnName="id")
     */
    private $struttura;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $strutturaOrario;

    /**
     * @var DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $periodoIscrizioneDa;

    /**
     * @var DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $periodoIscrizioneA;

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

    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_ISCRIZIONE_ASILO_NIDO;
    }

    /**
     * @return AsiloNido
     */
    public function getStruttura()
    {
        return $this->struttura;
    }

    /**
     * @param AsiloNido $struttura
     *
     * @return IscrizioneAsiloNido
     */
    public function setStruttura($struttura)
    {
        $this->struttura = $struttura;

        return $this;
    }

    public function jsonSerialize()
    {
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAccettoUtilizzo()
    {
        return $this->accettoUtilizzo;
    }

    /**
     * @param boolean $accettoUtilizzo
     *
     * @return IscrizioneAsiloNido
     */
    public function setAccettoUtilizzo($accettoUtilizzo)
    {
        $this->accettoUtilizzo = $accettoUtilizzo;

        return $this;
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
     *
     * @return IscrizioneAsiloNido
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
     *
     * @return IscrizioneAsiloNido
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
     *
     * @return IscrizioneAsiloNido
     */
    public function setBambinoLuogoNascita($bambinoLuogoNascita)
    {
        $this->bambinoLuogoNascita = $bambinoLuogoNascita;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBambinoDataNascita()
    {
        return $this->bambinoDataNascita;
    }

    /**
     * @param DateTime $bambinoDataNascita
     *
     * @return IscrizioneAsiloNido
     */
    public function setBambinoDataNascita($bambinoDataNascita)
    {
        $this->bambinoDataNascita = $bambinoDataNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getStrutturaOrario()
    {
        return $this->strutturaOrario;
    }

    /**
     * @param string $strutturaOrario
     *
     * @return IscrizioneAsiloNido
     */
    public function setStrutturaOrario($strutturaOrario)
    {
        $this->strutturaOrario = $strutturaOrario;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPeriodoIscrizioneDa()
    {
        return $this->periodoIscrizioneDa;
    }

    /**
     * @param DateTime $periodoIscrizioneDa
     *
     * @return IscrizioneAsiloNido
     */
    public function setPeriodoIscrizioneDa($periodoIscrizioneDa)
    {
        $this->periodoIscrizioneDa = $periodoIscrizioneDa;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPeriodoIscrizioneA()
    {
        return $this->periodoIscrizioneA;
    }

    /**
     * @param DateTime $periodoIscrizioneA
     *
     * @return IscrizioneAsiloNido
     */
    public function setPeriodoIscrizioneA($periodoIscrizioneA)
    {
        $this->periodoIscrizioneA = $periodoIscrizioneA;

        return $this;
    }

}
