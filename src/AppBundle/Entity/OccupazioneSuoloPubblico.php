<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OccupazioneSuoloPubblico
 *
 * @ORM\Entity
 */
class OccupazioneSuoloPubblico extends Pratica
{

    const TIPOLOGIA_PERMANENTE = 'permanente';
    const TIPOLOGIA_TEMPORANEA = 'temporanea';


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ruoloUtenteOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ragioneSocialeOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $comuneOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $indirizzoOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $capOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $provinciaOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $civicoOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $indirizzoOccupazione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $civicoOccupazione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lunghezzaOccupazione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $larghezzaOccupazione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $metriQuadriOccupazione;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $motivazioneOccupazione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tipologiaOccupazione;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $inizioOccupazioneGiorno;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $inizioOccupazioneOra;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fineOccupazioneGiorno;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $fineOccupazioneOra;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $limitazioneTraffico;

    private $tipologieOccupazione;

    private $orari;

    /**
     * OccupazioneSuoloPubblico constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_OCCUPAZIONE_SUOLO_PUBBLICO;
    }

    public function getTipologieOccupazione()
    {
        if ($this->tipologieOccupazione === null) {
            $this->tipologieOccupazione = array();
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            foreach ($constants as $name => $value) {
                if (strpos($name, 'TIPOLOGIA_') !== false) {
                    $this->tipologieOccupazione[] = $value;
                }
            }
        }
        return $this->tipologieOccupazione;
    }

    public function getOrari( $start = 0, $end = 86400, $step = 3600 ) {
        if ($this->orari === null) {
            $this->orari = array();
            foreach ( range( $start, $end, $step ) as $timestamp ) {
                $hour_mins = gmdate( 'H:i', $timestamp );
                $this->orari[$hour_mins] = $hour_mins;
            }
        }
        return $this->orari;
    }

    /**
     * @return mixed
     */
    public function getRuoloUtenteOrgRichiedente()
    {
        return $this->ruoloUtenteOrgRichiedente;
    }

    /**
     * @param mixed $ruoloUtenteOrgRichiedente
     */
    public function setRuoloUtenteOrgRichiedente($ruoloUtenteOrgRichiedente)
    {
        $this->ruoloUtenteOrgRichiedente = $ruoloUtenteOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getRagioneSocialeOrgRichiedente()
    {
        return $this->ragioneSocialeOrgRichiedente;
    }

    /**
     * @param mixed $ragioneSocialeOrgRichiedente
     */
    public function setRagioneSocialeOrgRichiedente($ragioneSocialeOrgRichiedente)
    {
        $this->ragioneSocialeOrgRichiedente = $ragioneSocialeOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getComuneOrgRichiedente()
    {
        return $this->comuneOrgRichiedente;
    }

    /**
     * @param mixed $ComuneOrgRichiedente
     */
    public function setComuneOrgRichiedente($ComuneOrgRichiedente)
    {
        $this->comuneOrgRichiedente = $ComuneOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getIndirizzoOrgRichiedente()
    {
        return $this->indirizzoOrgRichiedente;
    }

    /**
     * @param mixed $IndirizzoOrgRichiedente
     */
    public function setIndirizzoOrgRichiedente($IndirizzoOrgRichiedente)
    {
        $this->indirizzoOrgRichiedente = $IndirizzoOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getCapOrgRichiedente()
    {
        return $this->capOrgRichiedente;
    }

    /**
     * @param mixed $capOrgRichiedente
     */
    public function setCapOrgRichiedente($capOrgRichiedente)
    {
        $this->capOrgRichiedente = $capOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getProvinciaOrgRichiedente()
    {
        return $this->provinciaOrgRichiedente;
    }

    /**
     * @param mixed $provinciaOrgRichiedente
     */
    public function setProvinciaOrgRichiedente($provinciaOrgRichiedente)
    {
        $this->provinciaOrgRichiedente = $provinciaOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getCivicoOrgRichiedente()
    {
        return $this->civicoOrgRichiedente;
    }

    /**
     * @param mixed $civicoOrgRichiedente
     */
    public function setCivicoOrgRichiedente($civicoOrgRichiedente)
    {
        $this->civicoOrgRichiedente = $civicoOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getIndirizzoOccupazione()
    {
        return $this->indirizzoOccupazione;
    }

    /**
     * @param mixed $indirizzoOccupazione
     */
    public function setIndirizzoOccupazione($indirizzoOccupazione)
    {
        $this->indirizzoOccupazione = $indirizzoOccupazione;
    }

    /**
     * @return mixed
     */
    public function getCivicoOccupazione()
    {
        return $this->civicoOccupazione;
    }

    /**
     * @param mixed $civicoOccupazione
     */
    public function setCivicoOccupazione($civicoOccupazione)
    {
        $this->civicoOccupazione = $civicoOccupazione;
    }

    /**
     * @return mixed
     */
    public function getLunghezzaOccupazione()
    {
        return $this->lunghezzaOccupazione;
    }

    /**
     * @param mixed $lunghezzaOccupazione
     */
    public function setLunghezzaOccupazione($lunghezzaOccupazione)
    {
        $this->lunghezzaOccupazione = $lunghezzaOccupazione;
    }

    /**
     * @return mixed
     */
    public function getLarghezzaOccupazione()
    {
        return $this->larghezzaOccupazione;
    }

    /**
     * @param mixed $larghezzaOccupazione
     */
    public function setLarghezzaOccupazione($larghezzaOccupazione)
    {
        $this->larghezzaOccupazione = $larghezzaOccupazione;
    }

    /**
     * @return mixed
     */
    public function getMetriQuadriOccupazione()
    {
        return $this->metriQuadriOccupazione;
    }

    /**
     * @param mixed $metriQuadriOccupazione
     */
    public function setMetriQuadriOccupazione($metriQuadriOccupazione)
    {
        $this->metriQuadriOccupazione = $metriQuadriOccupazione;
    }

    /**
     * @return mixed
     */
    public function getMotivazioneOccupazione()
    {
        return $this->motivazioneOccupazione;
    }

    /**
     * @param mixed $motivazioneOccupazione
     */
    public function setMotivazioneOccupazione($motivazioneOccupazione)
    {
        $this->motivazioneOccupazione = $motivazioneOccupazione;
    }

    /**
     * @return mixed
     */
    public function getTipologiaOccupazione()
    {
        return $this->tipologiaOccupazione;
    }

    /**
     * @param mixed $tipologiaOccupazione
     */
    public function setTipologiaOccupazione($tipologiaOccupazione)
    {
        $this->tipologiaOccupazione = $tipologiaOccupazione;
    }

    /**
     * @return mixed
     */
    public function getInizioOccupazioneGiorno()
    {
        return $this->inizioOccupazioneGiorno;
    }

    /**
     * @param mixed $inizioOccupazioneGiorno
     */
    public function setInizioOccupazioneGiorno($inizioOccupazioneGiorno)
    {
        $this->inizioOccupazioneGiorno = $inizioOccupazioneGiorno;
    }

    /**
     * @return mixed
     */
    public function getInizioOccupazioneOra()
    {
        return $this->inizioOccupazioneOra;
    }

    /**
     * @param mixed $inizioOccupazioneOra
     */
    public function setInizioOccupazioneOra($inizioOccupazioneOra)
    {
        $this->inizioOccupazioneOra = $inizioOccupazioneOra;
    }

    /**
     * @return mixed
     */
    public function getFineOccupazioneGiorno()
    {
        return $this->fineOccupazioneGiorno;
    }

    /**
     * @param mixed $fineOccupazioneGiorno
     */
    public function setFineOccupazioneGiorno($fineOccupazioneGiorno)
    {
        $this->fineOccupazioneGiorno = $fineOccupazioneGiorno;
    }

    /**
     * @return mixed
     */
    public function getFineOccupazioneOra()
    {
        return $this->fineOccupazioneOra;
    }

    /**
     * @param mixed $fineOccupazioneOra
     */
    public function setFineOccupazioneOra($fineOccupazioneOra)
    {
        $this->fineOccupazioneOra = $fineOccupazioneOra;
    }

    /**
     * @param mixed $fineOccupazione
     */
    public function setFineOccupazione($fineOccupazione)
    {
        $this->fineOccupazione = $fineOccupazione;
    }

    /**
     * @return mixed
     */
    public function getLimitazioneTraffico()
    {
        return $this->limitazioneTraffico;
    }

    /**
     * @param mixed $limitazioneTraffico
     */
    public function setLimitazioneTraffico($limitazioneTraffico)
    {
        $this->limitazioneTraffico = $limitazioneTraffico;
    }




}
