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
 * Class CPSUser
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @package AppBundle\Entity
 */
class CPSUser extends User
{
    /**
     * @var string
     *
     * @ORM\Column(name="codice_fiscale", type="string", unique=true)
     */
    private $codiceFiscale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_nascita", type="datetime", nullable=true)
     */
    private $dataNascita;

    /**
     * @var string
     *
     * @ORM\Column(name="luogo_nascita", type="text", nullable=true)
     */
    private $luogoNascita;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia_nascita", type="text", nullable=true)
     */
    private $provinciaNascita;

    /**
     * @var string
     *
     * @ORM\Column(name="stato_nascita", type="text", nullable=true)
     */
    private $statoNascita;

    /**
     * @var string
     *
     * @ORM\Column(name="sesso", type="string", nullable=true)
     */
    private $sesso;

    /**
     * @var string
     *
     * @ORM\Column(name="x509certificate_issuerdn", type="text", nullable=true)
     */
    private $x509certificate_issuerdn;

    /**
     * @var string
     *
     * @ORM\Column(name="x509certificate_subjectdn", type="text", nullable=true)
     */
    private $x509certificate_subjectdn;

    /**
     * @var string
     *
     * @ORM\Column(name="x509certificate_base64", type="text", nullable=true)
     */
    private $x509certificate_base64;


    /**
     * @var string
     *
     * @ORM\Column(name="cps_telefono", type="text", nullable=true)
     */
    private $cpsTelefono;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_cellulare", type="text", nullable=true)
     */
    private $cpsCellulare;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_email", type="text", nullable=true)
     */
    private $cpsEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_email_personale", type="text", nullable=true)
     */
    private $cpsEmailPersonale;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_titolo", type="text", nullable=true)
     */
    private $cpsTitolo;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_indirizzo_domicilio", type="text", nullable=true)
     */
    private $cpsIndirizzoDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_cap_domicilio", type="text", nullable=true)
     */
    private $cpsCapDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_citta_domicilio", type="text", nullable=true)
     */
    private $cpsCittaDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_provincia_domicilio", type="text", nullable=true)
     */
    private $cpsProvinciaDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_stato_domicilio", type="text", nullable=true)
     */
    private $cpsStatoDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_indirizzo_residenza", type="text", nullable=true)
     */
    private $cpsIndirizzoResidenza;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_cap_residenza", type="text", nullable=true)
     */
    private $cpsCapResidenza;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_citta_residenza", type="text", nullable=true)
     */
    private $cpsCittaResidenza;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_provincia_residenza", type="text", nullable=true)
     */
    private $cpsProvinciaResidenza;

    /**
     * @var string
     *
     * @ORM\Column(name="cps_stato_residenza", type="text", nullable=true)
     */
    private $cpsStatoResidenza;


    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcIndirizzoDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcCapDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcCittaDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcProvinciaDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcStatoDomicilio;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcIndirizzoResidenza;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcCapResidenza;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcCittaResidenza;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcProvinciaResidenza;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $sdcStatoResidenza;

    /**
     * @var Collection
     *
     * @ORM\Column(name="accepted_terms", type="text")
     */
    private $acceptedTerms;

    /**
     * CPSUser constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::USER_TYPE_CPS;
        $this->acceptedTerms = new ArrayCollection();
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
     * @return CPSUser
     */
    public function setCodiceFiscale($codiceFiscale)
    {
        $this->codiceFiscale = $codiceFiscale;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDataNascita()
    {
        return $this->dataNascita;
    }

    /**
     * @param \DateTime $dataNascita
     *
     * @return CPSUser
     */
    public function setDataNascita($dataNascita)
    {
        $this->dataNascita = $dataNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getLuogoNascita()
    {
        return $this->luogoNascita;
    }

    /**
     * @param string $luogoNascita
     *
     * @return CPSUser
     */
    public function setLuogoNascita($luogoNascita)
    {
        $this->luogoNascita = $luogoNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getProvinciaNascita()
    {
        return $this->provinciaNascita;
    }

    /**
     * @param string $provinciaNascita
     *
     * @return CPSUser
     */
    public function setProvinciaNascita($provinciaNascita)
    {
        $this->provinciaNascita = $provinciaNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatoNascita()
    {
        return $this->statoNascita;
    }

    /**
     * @param string $statoNascita
     *
     * @return CPSUser
     */
    public function setStatoNascita($statoNascita)
    {
        $this->statoNascita = $statoNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getSesso()
    {
        return $this->sesso;
    }

    /**
     * @param string $sesso
     *
     * @return CPSUser
     */
    public function setSesso($sesso)
    {
        $this->sesso = $sesso;

        return $this;
    }

    /**
     * @return string
     */
    public function getX509certificateIssuerdn()
    {
        return $this->x509certificate_issuerdn;
    }

    /**
     * @param string $x509certificate_issuerdn
     *
     * @return CPSUser
     */
    public function setX509certificateIssuerdn($x509certificate_issuerdn)
    {
        $this->x509certificate_issuerdn = $x509certificate_issuerdn;

        return $this;
    }

    /**
     * @return string
     */
    public function getX509certificateSubjectdn()
    {
        return $this->x509certificate_subjectdn;
    }

    /**
     * @param string $x509certificate_subjectdn
     *
     * @return CPSUser
     */
    public function setX509certificateSubjectdn($x509certificate_subjectdn)
    {
        $this->x509certificate_subjectdn = $x509certificate_subjectdn;

        return $this;
    }

    /**
     * @return string
     */
    public function getX509certificateBase64()
    {
        return $this->x509certificate_base64;
    }

    /**
     * @param string $x509certificate_base64
     *
     * @return CPSUser
     */
    public function setX509certificateBase64($x509certificate_base64)
    {
        $this->x509certificate_base64 = $x509certificate_base64;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsTelefono()
    {
        return $this->cpsTelefono;
    }

    /**
     * @param string $cpsTelefono
     *
     * @return CPSUser
     */
    public function setCpsTelefono($cpsTelefono)
    {
        $this->cpsTelefono = $cpsTelefono;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsCellulare()
    {
        return $this->cpsCellulare;
    }

    /**
     * @param string $cpsCellulare
     *
     * @return CPSUser
     */
    public function setCpsCellulare($cpsCellulare)
    {
        $this->cpsCellulare = $cpsCellulare;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsEmail()
    {
        return $this->cpsEmail;
    }

    /**
     * @param string $cpsEmail
     *
     * @return CPSUser
     */
    public function setCpsEmail($cpsEmail)
    {
        $this->cpsEmail = $cpsEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsEmailPersonale()
    {
        return $this->cpsEmailPersonale;
    }

    /**
     * @param string $cpsEmailPersonale
     *
     * @return CPSUser
     */
    public function setCpsEmailPersonale($cpsEmailPersonale)
    {
        $this->cpsEmailPersonale = $cpsEmailPersonale;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsTitolo()
    {
        return $this->cpsTitolo;
    }

    /**
     * @param string $cpsTitolo
     *
     * @return CPSUser
     */
    public function setCpsTitolo($cpsTitolo)
    {
        $this->cpsTitolo = $cpsTitolo;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsIndirizzoDomicilio()
    {
        return $this->cpsIndirizzoDomicilio;
    }

    /**
     * @param string $cpsIndirizzoDomicilio
     *
     * @return CPSUser
     */
    public function setCpsIndirizzoDomicilio($cpsIndirizzoDomicilio)
    {
        $this->cpsIndirizzoDomicilio = $cpsIndirizzoDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsCapDomicilio()
    {
        return $this->cpsCapDomicilio;
    }

    /**
     * @param string $cpsCapDomicilio
     *
     * @return CPSUser
     */
    public function setCpsCapDomicilio($cpsCapDomicilio)
    {
        $this->cpsCapDomicilio = $cpsCapDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsCittaDomicilio()
    {
        return $this->cpsCittaDomicilio;
    }

    /**
     * @param string $cpsCittaDomicilio
     *
     * @return CPSUser
     */
    public function setCpsCittaDomicilio($cpsCittaDomicilio)
    {
        $this->cpsCittaDomicilio = $cpsCittaDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsProvinciaDomicilio()
    {
        return $this->cpsProvinciaDomicilio;
    }

    /**
     * @param string $cpsProvinciaDomicilio
     *
     * @return CPSUser
     */
    public function setCpsProvinciaDomicilio($cpsProvinciaDomicilio)
    {
        $this->cpsProvinciaDomicilio = $cpsProvinciaDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsStatoDomicilio()
    {
        return $this->cpsStatoDomicilio;
    }

    /**
     * @param string $cpsStatoDomicilio
     *
     * @return CPSUser
     */
    public function setCpsStatoDomicilio($cpsStatoDomicilio)
    {
        $this->cpsStatoDomicilio = $cpsStatoDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsIndirizzoResidenza()
    {
        return $this->cpsIndirizzoResidenza;
    }

    /**
     * @param string $cpsIndirizzoResidenza
     *
     * @return CPSUser
     */
    public function setCpsIndirizzoResidenza($cpsIndirizzoResidenza)
    {
        $this->cpsIndirizzoResidenza = $cpsIndirizzoResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsCapResidenza()
    {
        return $this->cpsCapResidenza;
    }

    /**
     * @param string $cpsCapResidenza
     *
     * @return CPSUser
     */
    public function setCpsCapResidenza($cpsCapResidenza)
    {
        $this->cpsCapResidenza = $cpsCapResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsCittaResidenza()
    {
        return $this->cpsCittaResidenza;
    }

    /**
     * @param string $cpsCittaResidenza
     *
     * @return CPSUser
     */
    public function setCpsCittaResidenza($cpsCittaResidenza)
    {
        $this->cpsCittaResidenza = $cpsCittaResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsProvinciaResidenza()
    {
        return $this->cpsProvinciaResidenza;
    }

    /**
     * @param string $cpsProvinciaResidenza
     *
     * @return CPSUser
     */
    public function setCpsProvinciaResidenza($cpsProvinciaResidenza)
    {
        $this->cpsProvinciaResidenza = $cpsProvinciaResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getCpsStatoResidenza()
    {
        return $this->cpsStatoResidenza;
    }

    /**
     * @param string $cpsStatoResidenza
     *
     * @return CPSUser
     */
    public function setCpsStatoResidenza($cpsStatoResidenza)
    {
        $this->cpsStatoResidenza = $cpsStatoResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcIndirizzoDomicilio()
    {
        return $this->sdcIndirizzoDomicilio;
    }

    /**
     * @param string $sdcIndirizzoDomicilio
     *
     * @return CPSUser
     */
    public function setSdcIndirizzoDomicilio($sdcIndirizzoDomicilio)
    {
        $this->sdcIndirizzoDomicilio = $sdcIndirizzoDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcCapDomicilio()
    {
        return $this->sdcCapDomicilio;
    }

    /**
     * @param string $sdcCapDomicilio
     *
     * @return CPSUser
     */
    public function setSdcCapDomicilio($sdcCapDomicilio)
    {
        $this->sdcCapDomicilio = $sdcCapDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcCittaDomicilio()
    {
        return $this->sdcCittaDomicilio;
    }

    /**
     * @param string $sdcCittaDomicilio
     *
     * @return CPSUser
     */
    public function setSdcCittaDomicilio($sdcCittaDomicilio)
    {
        $this->sdcCittaDomicilio = $sdcCittaDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcProvinciaDomicilio()
    {
        return $this->sdcProvinciaDomicilio;
    }

    /**
     * @param string $sdcProvinciaDomicilio
     *
     * @return CPSUser
     */
    public function setSdcProvinciaDomicilio($sdcProvinciaDomicilio)
    {
        $this->sdcProvinciaDomicilio = $sdcProvinciaDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcStatoDomicilio()
    {
        return $this->sdcStatoDomicilio;
    }

    /**
     * @param string $sdcStatoDomicilio
     *
     * @return CPSUser
     */
    public function setSdcStatoDomicilio($sdcStatoDomicilio)
    {
        $this->sdcStatoDomicilio = $sdcStatoDomicilio;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcIndirizzoResidenza()
    {
        return $this->sdcIndirizzoResidenza;
    }

    /**
     * @param string $sdcIndirizzoResidenza
     *
     * @return CPSUser
     */
    public function setSdcIndirizzoResidenza($sdcIndirizzoResidenza)
    {
        $this->sdcIndirizzoResidenza = $sdcIndirizzoResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcCapResidenza()
    {
        return $this->sdcCapResidenza;
    }

    /**
     * @param string $sdcCapResidenza
     *
     * @return CPSUser
     */
    public function setSdcCapResidenza($sdcCapResidenza)
    {
        $this->sdcCapResidenza = $sdcCapResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcCittaResidenza()
    {
        return $this->sdcCittaResidenza;
    }

    /**
     * @param string $sdcCittaResidenza
     *
     * @return CPSUser
     */
    public function setSdcCittaResidenza($sdcCittaResidenza)
    {
        $this->sdcCittaResidenza = $sdcCittaResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcProvinciaResidenza()
    {
        return $this->sdcProvinciaResidenza;
    }

    /**
     * @param string $sdcProvinciaResidenza
     *
     * @return CPSUser
     */
    public function setSdcProvinciaResidenza($sdcProvinciaResidenza)
    {
        $this->sdcProvinciaResidenza = $sdcProvinciaResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getSdcStatoResidenza()
    {
        return $this->sdcStatoResidenza;
    }

    /**
     * @param string $sdcStatoResidenza
     *
     * @return CPSUser
     */
    public function setSdcStatoResidenza($sdcStatoResidenza)
    {
        $this->sdcStatoResidenza = $sdcStatoResidenza;

        return $this;
    }

    public function isTermsAccepted()
    {
        throw new \Exception('deprecated, use the TermsAcceptanceCheckerService instead');
    }

    public function setTermsAccepted($termsAccepted)
    {
        throw new \Exception('deprecated, use setAcceptedterms ');
    }

    /**
     * @return string
     */
    public function getTitolo()
    {
        return $this->cpsTitolo;
    }

    /**
     * @return string
     */
    public function getCellulare()
    {
        return $this->getCellulareContatto() ?? $this->cpsCellulare;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getEmailContatto() ?? $this->email;
    }

    /**
     * @return string
     */
    public function getTelefono()
    {
        return $this->cpsTelefono;
    }

    /**
     * @return string
     */
    public function getEmailAlt()
    {
        return $this->cpsEmailPersonale;
    }

    /**
     * @return string
     */
    public function getIndirizzoDomicilio()
    {
        return $this->sdcIndirizzoDomicilio ?? $this->cpsIndirizzoDomicilio;
    }

    /**
     * @return string
     */
    public function getCapDomicilio()
    {
        return $this->sdcCapDomicilio ?? $this->cpsCapDomicilio;
    }


    /**
     * @return string
     */
    public function getCittaDomicilio()
    {
        return $this->sdcCittaDomicilio ?? $this->cpsCittaDomicilio;
    }


    /**
     * @return string
     */
    public function getProvinciaDomicilio()
    {
        return $this->sdcProvinciaDomicilio ?? $this->cpsProvinciaDomicilio;
    }


    /**
     * @return string
     */
    public function getStatoDomicilio()
    {
        return $this->sdcStatoDomicilio ?? $this->cpsStatoDomicilio;
    }


    /**
     * @return string
     */
    public function getIndirizzoResidenza()
    {
        return $this->sdcIndirizzoResidenza ?? $this->cpsIndirizzoResidenza;
    }


    /**
     * @return string
     */
    public function getCapResidenza()
    {
        return $this->sdcCapResidenza ?? $this->cpsCapResidenza;
    }


    /**
     * @return string
     */
    public function getCittaResidenza()
    {
        return $this->sdcCittaResidenza ?? $this->cpsCittaResidenza;
    }


    /**
     * @return string
     */
    public function getProvinciaResidenza()
    {
        return $this->sdcProvinciaResidenza ?? $this->cpsProvinciaResidenza;
    }


    /**
     * @return string
     */
    public function getStatoResidenza()
    {
        return $this->sdcStatoResidenza ?? $this->cpsStatoResidenza;
    }

    /**
     * @return Collection
     */
    public function getAcceptedTerms(): Collection
    {
        $this->parseAcceptedTerms();

        return $this->acceptedTerms;
    }

    /**
     * @param TerminiUtilizzo $term
     * @return $this
     */
    public function addTermsAcceptance(TerminiUtilizzo $term)
    {
        if (!$this->getAcceptedTerms()->containsKey((string)$term->getId())) {
            $this->acceptedTerms[$term->getId().''] = [
                'text' => $term->getText(),
                'name' => $term->getName(),
                'timestamp' => time(),
            ];
        }

        return $this;
    }

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     */
    public function parseAcceptedTerms()
    {
        if (!($this->acceptedTerms instanceof Collection)) {
            $this->acceptedTerms = new ArrayCollection(json_decode($this->acceptedTerms, true));
        }
    }

    /**
     * @ORM\PreFlush()
     */
    public function serializeAcceptedTerms()
    {
        if ($this->acceptedTerms instanceof Collection) {
            $this->acceptedTerms = json_encode($this->getAcceptedterms()->toArray());
        }
    }
}
