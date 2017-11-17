<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CambioResidenza
 *
 * @ORM\Entity
 */
class CambioResidenza extends Pratica
{
    const PROVENIENZA_COMUNE = 'comune';
    const PROVENIENZA_ALTRO_COMUNE = 'altro_comune';
    const PROVENIENZA_ESTERO = 'estero';
    const PROVENIENZA_AIRE = 'aire';
    const PROVENIENZA_ALTRO = 'altro';

    const OCCUPAZIONE_PROPRIETARIO = 'proprietario';
    const OCCUPAZIONE_LOCAZIONE = 'locazione';
    const OCCUPAZIONE_LOCAZIONE_ERP = 'locazione_erp';
    const OCCUPAZIONE_COMODATO = 'comodato';
    const OCCUPAZIONE_USUFRUTTO = 'usufruttuario';
    const OCCUPAZIONE_AUTOCERTIFICAZIONE = 'autocertificazione';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $provenienza;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $comuneDiProvenienza;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $statoEsteroDiProvenienza;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $comuneEsteroDiProvenienza;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $altraProvenienza;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaProvincia;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaComune;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaIndirizzo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaNumeroCivico;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaScala;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaPiano;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $residenzaInterno;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @var ArrayCollection
     */
    private $personeResidenti;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tipoOccupazione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $proprietarioCatastoSezione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $proprietarioCatastoFoglio;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $proprietarioCatastoParticella;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $proprietarioCatastoSubalterno;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $contrattoAgenzia;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $contrattoNumero;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $contrattoData;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $usufruttuarioInfo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infoAccertamento;

    private $tipiProvenienza;

    private $tipiOccupazione;

    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_CAMBIO_RESIDENZA;
    }

    public function getTipiProvenienza()
    {
        if ($this->tipiProvenienza === null) {
            $this->tipiProvenienza = array();
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            foreach ($constants as $name => $value) {
                if (strpos($name, 'PROVENIENZA_') !== false) {
                    $this->tipiProvenienza[] = $value;
                }
            }
        }

        return $this->tipiProvenienza;
    }

    public function getTipiOccupazione()
    {
        if ($this->tipiOccupazione === null) {
            $this->tipiOccupazione = array();
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            foreach ($constants as $name => $value) {
                if (strpos($name, 'OCCUPAZIONE_') !== false) {
                    $this->tipiOccupazione[] = $value;
                }
            }
        }

        return $this->tipiOccupazione;
    }

    /**
     * @return mixed
     */
    public function getProvenienza()
    {
        return $this->provenienza;
    }

    /**
     * @param mixed $provenienza
     *
     * @return CambioResidenza
     */
    public function setProvenienza($provenienza)
    {
        $this->provenienza = $provenienza;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComuneDiProvenienza()
    {
        return $this->comuneDiProvenienza;
    }

    /**
     * @param mixed $comuneDiProvenienza
     *
     * @return CambioResidenza
     */
    public function setComuneDiProvenienza($comuneDiProvenienza)
    {
        $this->comuneDiProvenienza = $comuneDiProvenienza;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatoEsteroDiProvenienza()
    {
        return $this->statoEsteroDiProvenienza;
    }

    /**
     * @param mixed $statoEsteroDiProvenienza
     *
     * @return CambioResidenza
     */
    public function setStatoEsteroDiProvenienza($statoEsteroDiProvenienza)
    {
        $this->statoEsteroDiProvenienza = $statoEsteroDiProvenienza;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComuneEsteroDiProvenienza()
    {
        return $this->comuneEsteroDiProvenienza;
    }

    /**
     * @param mixed $comuneEsteroDiProvenienza
     *
     * @return CambioResidenza
     */
    public function setComuneEsteroDiProvenienza($comuneEsteroDiProvenienza)
    {
        $this->comuneEsteroDiProvenienza = $comuneEsteroDiProvenienza;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAltraProvenienza()
    {
        return $this->altraProvenienza;
    }

    /**
     * @param mixed $altraProvenienza
     *
     * @return CambioResidenza
     */
    public function setAltraProvenienza($altraProvenienza)
    {
        $this->altraProvenienza = $altraProvenienza;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaProvincia()
    {
        return $this->residenzaProvincia;
    }

    /**
     * @param mixed $residenzaProvincia
     *
     * @return CambioResidenza
     */
    public function setResidenzaProvincia($residenzaProvincia)
    {
        $this->residenzaProvincia = $residenzaProvincia;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaComune()
    {
        return $this->residenzaComune;
    }

    /**
     * @param mixed $residenzaComune
     *
     * @return CambioResidenza
     */
    public function setResidenzaComune($residenzaComune)
    {
        $this->residenzaComune = $residenzaComune;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaIndirizzo()
    {
        return $this->residenzaIndirizzo;
    }

    /**
     * @param mixed $residenzaIndirizzo
     *
     * @return CambioResidenza
     */
    public function setResidenzaIndirizzo($residenzaIndirizzo)
    {
        $this->residenzaIndirizzo = $residenzaIndirizzo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaNumeroCivico()
    {
        return $this->residenzaNumeroCivico;
    }

    /**
     * @param mixed $residenzaNumeroCivico
     *
     * @return CambioResidenza
     */
    public function setResidenzaNumeroCivico($residenzaNumeroCivico)
    {
        $this->residenzaNumeroCivico = $residenzaNumeroCivico;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaScala()
    {
        return $this->residenzaScala;
    }

    /**
     * @param mixed $residenzaScala
     *
     * @return CambioResidenza
     */
    public function setResidenzaScala($residenzaScala)
    {
        $this->residenzaScala = $residenzaScala;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaPiano()
    {
        return $this->residenzaPiano;
    }

    /**
     * @param mixed $residenzaPiano
     *
     * @return CambioResidenza
     */
    public function setResidenzaPiano($residenzaPiano)
    {
        $this->residenzaPiano = $residenzaPiano;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResidenzaInterno()
    {
        return $this->residenzaInterno;
    }

    /**
     * @param mixed $residenzaInterno
     *
     * @return CambioResidenza
     */
    public function setResidenzaInterno($residenzaInterno)
    {
        $this->residenzaInterno = $residenzaInterno;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPersoneResidenti()
    {
        return $this->personeResidenti;
    }

    /**
     * @param ArrayCollection $personeResidenti
     *
     * @return CambioResidenza
     */
    public function setPersoneResidenti($personeResidenti)
    {
        $notEmptyValuesCollection = new ArrayCollection();
        foreach($personeResidenti as $persona){
            $hasContent = true;
            foreach($persona as $key => $value){
                if (empty($value)){
                    $hasContent = false;
                }
            }
            if ($hasContent && !$notEmptyValuesCollection->contains($persona)){
                $notEmptyValuesCollection->add($persona);
            }
        }
        $this->personeResidenti = $notEmptyValuesCollection->toArray();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoOccupazione()
    {
        return $this->tipoOccupazione;
    }

    /**
     * @param mixed $tipoOccupazione
     *
     * @return CambioResidenza
     */
    public function setTipoOccupazione($tipoOccupazione)
    {
        $this->tipoOccupazione = $tipoOccupazione;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProprietarioCatastoSezione()
    {
        return $this->proprietarioCatastoSezione;
    }

    /**
     * @param mixed $proprietarioCatastoSezione
     *
     * @return CambioResidenza
     */
    public function setProprietarioCatastoSezione($proprietarioCatastoSezione)
    {
        $this->proprietarioCatastoSezione = $proprietarioCatastoSezione;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProprietarioCatastoFoglio()
    {
        return $this->proprietarioCatastoFoglio;
    }

    /**
     * @param mixed $proprietarioCatastoFoglio
     *
     * @return CambioResidenza
     */
    public function setProprietarioCatastoFoglio($proprietarioCatastoFoglio)
    {
        $this->proprietarioCatastoFoglio = $proprietarioCatastoFoglio;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProprietarioCatastoParticella()
    {
        return $this->proprietarioCatastoParticella;
    }

    /**
     * @param mixed $proprietarioCatastoParticella
     *
     * @return CambioResidenza
     */
    public function setProprietarioCatastoParticella($proprietarioCatastoParticella)
    {
        $this->proprietarioCatastoParticella = $proprietarioCatastoParticella;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProprietarioCatastoSubalterno()
    {
        return $this->proprietarioCatastoSubalterno;
    }

    /**
     * @param mixed $proprietarioCatastoSubalterno
     *
     * @return CambioResidenza
     */
    public function setProprietarioCatastoSubalterno($proprietarioCatastoSubalterno)
    {
        $this->proprietarioCatastoSubalterno = $proprietarioCatastoSubalterno;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContrattoAgenzia()
    {
        return $this->contrattoAgenzia;
    }

    /**
     * @param mixed $contrattoAgenzia
     *
     * @return CambioResidenza
     */
    public function setContrattoAgenzia($contrattoAgenzia)
    {
        $this->contrattoAgenzia = $contrattoAgenzia;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContrattoNumero()
    {
        return $this->contrattoNumero;
    }

    /**
     * @param mixed $contrattoNumero
     *
     * @return CambioResidenza
     */
    public function setContrattoNumero($contrattoNumero)
    {
        $this->contrattoNumero = $contrattoNumero;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContrattoData()
    {
        return $this->contrattoData;
    }

    /**
     * @param mixed $contrattoData
     *
     * @return CambioResidenza
     */
    public function setContrattoData($contrattoData)
    {
        $this->contrattoData = $contrattoData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsufruttuarioInfo()
    {
        return $this->usufruttuarioInfo;
    }

    /**
     * @param mixed $usufruttuarioInfo
     *
     * @return CambioResidenza
     */
    public function setUsufruttuarioInfo($usufruttuarioInfo)
    {
        $this->usufruttuarioInfo = $usufruttuarioInfo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInfoAccertamento()
    {
        return $this->infoAccertamento;
    }

    /**
     * @param mixed $infoAccertamento
     *
     * @return CambioResidenza
     */
    public function setInfoAccertamento($infoAccertamento)
    {
        $this->infoAccertamento = $infoAccertamento;

        return $this;
    }

}
