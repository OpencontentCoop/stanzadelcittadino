<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AutoletturaAcqua
 *
 * @ORM\Entity
 */
class AutoletturaAcqua extends Pratica
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioCodiceUtente;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioNome;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioCognome;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioIndirizzo;

    /**
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $intestatarioCap;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioCitta;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioTelefono;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioEmail;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $contatoreNumero;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $contatoreUso;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $contatoreUnitaImmobiliari;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $letturaMetriCubi;

    /**
     * @var string
     * @ORM\Column(type="date", nullable=true)
     */
    private $letturaData;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_AUTOLETTURA_ACQUA;
    }

    /**
     * @return string
     */
    public function getIntestatarioCodiceUtente()
    {
        return $this->intestatarioCodiceUtente;
    }

    /**
     * @param string $intestatarioCodiceUtente
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioCodiceUtente($intestatarioCodiceUtente)
    {
        $this->intestatarioCodiceUtente = $intestatarioCodiceUtente;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioNome()
    {
        return $this->intestatarioNome;
    }

    /**
     * @param string $intestatarioNome
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioNome($intestatarioNome)
    {
        $this->intestatarioNome = $intestatarioNome;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioCognome()
    {
        return $this->intestatarioCognome;
    }

    /**
     * @param string $intestatarioCognome
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioCognome($intestatarioCognome)
    {
        $this->intestatarioCognome = $intestatarioCognome;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioIndirizzo()
    {
        return $this->intestatarioIndirizzo;
    }

    /**
     * @param string $intestatarioIndirizzo
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioIndirizzo($intestatarioIndirizzo)
    {
        $this->intestatarioIndirizzo = $intestatarioIndirizzo;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioCap()
    {
        return $this->intestatarioCap;
    }

    /**
     * @param string $intestatarioCap
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioCap($intestatarioCap)
    {
        $this->intestatarioCap = $intestatarioCap;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioCitta()
    {
        return $this->intestatarioCitta;
    }

    /**
     * @param string $intestatarioCitta
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioCitta($intestatarioCitta)
    {
        $this->intestatarioCitta = $intestatarioCitta;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioTelefono()
    {
        return $this->intestatarioTelefono;
    }

    /**
     * @param string $intestatarioTelefono
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioTelefono($intestatarioTelefono)
    {
        $this->intestatarioTelefono = $intestatarioTelefono;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioEmail()
    {
        return $this->intestatarioEmail;
    }

    /**
     * @param string $intestatarioEmail
     *
     * @return AutoletturaAcqua
     */
    public function setIntestatarioEmail($intestatarioEmail)
    {
        $this->intestatarioEmail = $intestatarioEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getContatoreNumero()
    {
        return $this->contatoreNumero;
    }

    /**
     * @param string $contatoreNumero
     *
     * @return AutoletturaAcqua
     */
    public function setContatoreNumero($contatoreNumero)
    {
        $this->contatoreNumero = $contatoreNumero;

        return $this;
    }

    /**
     * @return string
     */
    public function getContatoreUso()
    {
        return $this->contatoreUso;
    }

    /**
     * @param string $contatoreUso
     *
     * @return AutoletturaAcqua
     */
    public function setContatoreUso($contatoreUso)
    {
        $this->contatoreUso = $contatoreUso;

        return $this;
    }

    /**
     * @return string
     */
    public function getContatoreUnitaImmobiliari()
    {
        return $this->contatoreUnitaImmobiliari;
    }

    /**
     * @param string $contatoreUnitaImmobiliari
     *
     * @return AutoletturaAcqua
     */
    public function setContatoreUnitaImmobiliari($contatoreUnitaImmobiliari)
    {
        $this->contatoreUnitaImmobiliari = $contatoreUnitaImmobiliari;

        return $this;
    }

    /**
     * @return string
     */
    public function getLetturaMetriCubi()
    {
        return $this->letturaMetriCubi;
    }

    /**
     * @param string $letturaMetriCubi
     *
     * @return AutoletturaAcqua
     */
    public function setLetturaMetriCubi($letturaMetriCubi)
    {
        $this->letturaMetriCubi = $letturaMetriCubi;

        return $this;
    }

    /**
     * @return string
     */
    public function getLetturaData()
    {
        return $this->letturaData;
    }

    /**
     * @param string $letturaData
     *
     * @return AutoletturaAcqua
     */
    public function setLetturaData($letturaData)
    {
        $this->letturaData = $letturaData;

        return $this;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return AutoletturaAcqua
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

}
