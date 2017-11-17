<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class AllacciamentoAcquedotto
 * @ORM\Entity
 */
class AllacciamentoAcquedotto extends Pratica
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileProvincia;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileComune;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileIndirizzo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileNumeroCivico;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCap;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileScala;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobilePiano;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileInterno;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCatastoCategoria;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCatastoCodiceComune;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCatastoFoglio;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCatastoSezione;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCatastoMappale;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileCatastoSubalterno;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoImmobileQualifica;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoTipoIntervento;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoTipoAllaccio;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoTipoUso;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $allacciamentoAcquedottoDiametroReteInterna;

    /**
     * @var bool
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $allacciamentoAcquedottoUseAlternateContact;

    /**
     * @var string
     * @ORM\Column(type="string",nullable=true)
     */
    private $allacciamentoAcquedottoAlternateContactVia;

    /**
     * @var string
     * @ORM\Column(type="string",nullable=true)
     */

    private $allacciamentoAcquedottoAlternateContactCivico;

    /**
     * @var string
     * @ORM\Column(type="string",nullable=true)
     */
    private $allacciamentoAcquedottoAlternateContactCAP;

    /**
     * @var string
     * @ORM\Column(type="string",nullable=true)
     */
    private $allacciamentoAcquedottoAlternateContactComune;

    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_ALLACCIAMENTO_AQUEDOTTO;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileProvincia()
    {
        return $this->allacciamentoAcquedottoImmobileProvincia;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileProvincia
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileProvincia($allacciamentoAcquedottoImmobileProvincia)
    {
        $this->allacciamentoAcquedottoImmobileProvincia = $allacciamentoAcquedottoImmobileProvincia;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileComune()
    {
        return $this->allacciamentoAcquedottoImmobileComune;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileComune
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileComune($allacciamentoAcquedottoImmobileComune)
    {
        $this->allacciamentoAcquedottoImmobileComune = $allacciamentoAcquedottoImmobileComune;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileIndirizzo()
    {
        return $this->allacciamentoAcquedottoImmobileIndirizzo;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileIndirizzo
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileIndirizzo($allacciamentoAcquedottoImmobileIndirizzo)
    {
        $this->allacciamentoAcquedottoImmobileIndirizzo = $allacciamentoAcquedottoImmobileIndirizzo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileNumeroCivico()
    {
        return $this->allacciamentoAcquedottoImmobileNumeroCivico;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileNumeroCivico
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileNumeroCivico($allacciamentoAcquedottoImmobileNumeroCivico)
    {
        $this->allacciamentoAcquedottoImmobileNumeroCivico = $allacciamentoAcquedottoImmobileNumeroCivico;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCap()
    {
        return $this->allacciamentoAcquedottoImmobileCap;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCap
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCap($allacciamentoAcquedottoImmobileCap)
    {
        $this->allacciamentoAcquedottoImmobileCap = $allacciamentoAcquedottoImmobileCap;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileScala()
    {
        return $this->allacciamentoAcquedottoImmobileScala;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileScala
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileScala($allacciamentoAcquedottoImmobileScala)
    {
        $this->allacciamentoAcquedottoImmobileScala = $allacciamentoAcquedottoImmobileScala;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobilePiano()
    {
        return $this->allacciamentoAcquedottoImmobilePiano;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobilePiano
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobilePiano($allacciamentoAcquedottoImmobilePiano)
    {
        $this->allacciamentoAcquedottoImmobilePiano = $allacciamentoAcquedottoImmobilePiano;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileInterno()
    {
        return $this->allacciamentoAcquedottoImmobileInterno;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileInterno
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileInterno($allacciamentoAcquedottoImmobileInterno)
    {
        $this->allacciamentoAcquedottoImmobileInterno = $allacciamentoAcquedottoImmobileInterno;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCatastoCategoria()
    {
        return $this->allacciamentoAcquedottoImmobileCatastoCategoria;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCatastoCategoria
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCatastoCategoria($allacciamentoAcquedottoImmobileCatastoCategoria)
    {
        $this->allacciamentoAcquedottoImmobileCatastoCategoria = $allacciamentoAcquedottoImmobileCatastoCategoria;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCatastoCodiceComune()
    {
        return $this->allacciamentoAcquedottoImmobileCatastoCodiceComune;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCatastoCodiceComune
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCatastoCodiceComune(
        $allacciamentoAcquedottoImmobileCatastoCodiceComune
    ) {
        $this->allacciamentoAcquedottoImmobileCatastoCodiceComune = $allacciamentoAcquedottoImmobileCatastoCodiceComune;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCatastoFoglio()
    {
        return $this->allacciamentoAcquedottoImmobileCatastoFoglio;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCatastoFoglio
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCatastoFoglio($allacciamentoAcquedottoImmobileCatastoFoglio)
    {
        $this->allacciamentoAcquedottoImmobileCatastoFoglio = $allacciamentoAcquedottoImmobileCatastoFoglio;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCatastoSezione()
    {
        return $this->allacciamentoAcquedottoImmobileCatastoSezione;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCatastoSezione
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCatastoSezione($allacciamentoAcquedottoImmobileCatastoSezione)
    {
        $this->allacciamentoAcquedottoImmobileCatastoSezione = $allacciamentoAcquedottoImmobileCatastoSezione;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCatastoMappale()
    {
        return $this->allacciamentoAcquedottoImmobileCatastoMappale;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCatastoMappale
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCatastoMappale($allacciamentoAcquedottoImmobileCatastoMappale)
    {
        $this->allacciamentoAcquedottoImmobileCatastoMappale = $allacciamentoAcquedottoImmobileCatastoMappale;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileCatastoSubalterno()
    {
        return $this->allacciamentoAcquedottoImmobileCatastoSubalterno;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileCatastoSubalterno
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileCatastoSubalterno(
        $allacciamentoAcquedottoImmobileCatastoSubalterno
    ) {
        $this->allacciamentoAcquedottoImmobileCatastoSubalterno = $allacciamentoAcquedottoImmobileCatastoSubalterno;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoImmobileQualifica()
    {
        return $this->allacciamentoAcquedottoImmobileQualifica;
    }

    /**
     * @param mixed $allacciamentoAcquedottoImmobileQualifica
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoImmobileQualifica($allacciamentoAcquedottoImmobileQualifica)
    {
        $this->allacciamentoAcquedottoImmobileQualifica = $allacciamentoAcquedottoImmobileQualifica;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoTipoIntervento()
    {
        return $this->allacciamentoAcquedottoTipoIntervento;
    }

    /**
     * @param mixed $allacciamentoAcquedottoTipoIntervento
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoTipoIntervento($allacciamentoAcquedottoTipoIntervento)
    {
        $this->allacciamentoAcquedottoTipoIntervento = $allacciamentoAcquedottoTipoIntervento;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoTipoAllaccio()
    {
        return $this->allacciamentoAcquedottoTipoAllaccio;
    }

    /**
     * @param mixed $allacciamentoAcquedottoTipoAllaccio
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoTipoAllaccio($allacciamentoAcquedottoTipoAllaccio)
    {
        $this->allacciamentoAcquedottoTipoAllaccio = $allacciamentoAcquedottoTipoAllaccio;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoTipoUso()
    {
        return $this->allacciamentoAcquedottoTipoUso;
    }

    /**
     * @param mixed $allacciamentoAcquedottoTipoUso
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoTipoUso($allacciamentoAcquedottoTipoUso)
    {
        $this->allacciamentoAcquedottoTipoUso = $allacciamentoAcquedottoTipoUso;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllacciamentoAcquedottoDiametroReteInterna()
    {
        return $this->allacciamentoAcquedottoDiametroReteInterna;
    }

    /**
     * @param mixed $allacciamentoAcquedottoDiametroReteInterna
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoDiametroReteInterna($allacciamentoAcquedottoDiametroReteInterna)
    {
        $this->allacciamentoAcquedottoDiametroReteInterna = $allacciamentoAcquedottoDiametroReteInterna;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAllacciamentoAcquedottoUseAlternateContact()
    {
        return $this->allacciamentoAcquedottoUseAlternateContact;
    }

    /**
     * @param boolean $allacciamentoAcquedottoUseAlternateContact
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoUseAlternateContact($allacciamentoAcquedottoUseAlternateContact)
    {
        $this->allacciamentoAcquedottoUseAlternateContact = $allacciamentoAcquedottoUseAlternateContact;

        return $this;
    }

    /**
     * @return string
     */
    public function getAllacciamentoAcquedottoAlternateContactVia()
    {
        return $this->allacciamentoAcquedottoAlternateContactVia;
    }

    /**
     * @param string $allacciamentoAcquedottoAlternateContactVia
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoAlternateContactVia($allacciamentoAcquedottoAlternateContactVia)
    {
        $this->allacciamentoAcquedottoAlternateContactVia = $allacciamentoAcquedottoAlternateContactVia;

        return $this;
    }

    /**
     * @return string
     */
    public function getAllacciamentoAcquedottoAlternateContactCivico()
    {
        return $this->allacciamentoAcquedottoAlternateContactCivico;
    }

    /**
     * @param string $allacciamentoAcquedottoAlternateContactCivico
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoAlternateContactCivico($allacciamentoAcquedottoAlternateContactCivico)
    {
        $this->allacciamentoAcquedottoAlternateContactCivico = $allacciamentoAcquedottoAlternateContactCivico;

        return $this;
    }

    /**
     * @return string
     */
    public function getAllacciamentoAcquedottoAlternateContactCAP()
    {
        return $this->allacciamentoAcquedottoAlternateContactCAP;
    }

    /**
     * @param string $allacciamentoAcquedottoAlternateContactCAP
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoAlternateContactCAP($allacciamentoAcquedottoAlternateContactCAP)
    {
        $this->allacciamentoAcquedottoAlternateContactCAP = $allacciamentoAcquedottoAlternateContactCAP;

        return $this;
    }

    /**
     * @return string
     */
    public function getAllacciamentoAcquedottoAlternateContactComune()
    {
        return $this->allacciamentoAcquedottoAlternateContactComune;
    }

    /**
     * @param string $allacciamentoAcquedottoAlternateContactComune
     *
     * @return AllacciamentoAcquedotto
     */
    public function setAllacciamentoAcquedottoAlternateContactComune($allacciamentoAcquedottoAlternateContactComune)
    {
        $this->allacciamentoAcquedottoAlternateContactComune = $allacciamentoAcquedottoAlternateContactComune;

        return $this;
    }

}
