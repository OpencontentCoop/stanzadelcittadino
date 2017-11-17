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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="pratica")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"default" = "Pratica",
 *     "iscrizione_asilo_nido" = "IscrizioneAsiloNido",
 *     "autolettura_acqua" = "AutoletturaAcqua",
 *     "contributo_pannolini" = "ContributoPannolini",
 *     "cambio_residenza" = "CambioResidenza",
 *     "allacciamento_acquedotto" = "AllacciamentoAcquedotto",
 *     "certificato_nascita" = "CertificatoNascita",
 *     "attestazione_anagrafica" = "AttestazioneAnagrafica",
 *     "liste_elettorali" = "ListeElettorali",
 *     "stato_famiglia" = "StatoFamiglia",
 *     "occupazione_suolo_pubblico" = "OccupazioneSuoloPubblico",
 *     "contributo_associazioni" = "ContributoAssociazioni"
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Pratica
{
    const STATUS_DRAFT = 1;
    const STATUS_SUBMITTED = 2;
    const STATUS_REGISTERED = 3;
    const STATUS_PENDING = 4;
    const STATUS_COMPLETE_WAITALLEGATIOPERATORE = 5;
    const STATUS_COMPLETE = 10;
    const STATUS_CANCELLED_WAITALLEGATIOPERATORE = 90;
    const STATUS_CANCELLED = 100;

    const ACCEPTED = true;
    const REJECTED = false;

    const TYPE_DEFAULT = "default";
    const TYPE_ISCRIZIONE_ASILO_NIDO = "iscrizione_asilo_nido";
    const TYPE_AUTOLETTURA_ACQUA = "autolettura_acqua";
    const TYPE_CONTRIBUTO_PANNOLINI = "contributo_pannolini";
    const TYPE_CAMBIO_RESIDENZA = "cambio_residenza";
    const TYPE_ALLACCIAMENTO_AQUEDOTTO = "allacciamento_aquedotto";
    const TYPE_CERTIFICATO_NASCITA = "certificato_nascita";
    const TYPE_ATTESTAZIONE_ANAGRAFICA = "attestazione_anagrafica";
    const TYPE_LISTE_ELETTORALI = "liste_elettorali";
    const TYPE_STATO_FAMIGLIA = "stato_famiglia";
    const TYPE_OCCUPAZIONE_SUOLO_PUBBLICO = "occupazione_suolo_pubblico";
    const TYPE_CONTRIBUTO_ASSOCIAZIONI = "contributo_associazioni";

    /**
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CPSUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Servizio")
     * @ORM\JoinColumn(name="servizio_id", referencedColumnName="id", nullable=false)
     */
    private $servizio;

    /**
     * @ORM\ManyToOne(targetEntity="Erogatore")
     * @ORM\JoinColumn(name="erogatore_id", referencedColumnName="id", nullable=true)
     */
    private $erogatore;

    /**
     * @ORM\ManyToOne(targetEntity="Ente")
     * @ORM\JoinColumn(name="ente_id", referencedColumnName="id", nullable=true)
     */
    private $ente;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OperatoreUser")
     * @ORM\JoinColumn(name="operatore_id", referencedColumnName="id", nullable=true)
     */
    private $operatore;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Allegato", inversedBy="pratiche", orphanRemoval=false)
     * @var ArrayCollection
     * @Assert\Valid(traverse=true)
     */
    private $allegati;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ModuloCompilato", inversedBy="pratiche2", orphanRemoval=false)
     * @var ArrayCollection
     * @Assert\Valid(traverse=true)
     */
    private $moduliCompilati;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\AllegatoOperatore", inversedBy="pratiche3", orphanRemoval=false)
     * @var ArrayCollection
     * @Assert\Valid(traverse=true)
     */
    private $allegatiOperatore;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\RispostaOperatore", orphanRemoval=false)
     * @ORM\JoinColumn(nullable=true)
     * @var RispostaOperatore
     */
    private $rispostaOperatore;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ComponenteNucleoFamiliare", mappedBy="pratica", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     * @var ArrayCollection
     */
    private $nucleoFamiliare;

    /**
     * @ORM\Column(type="integer", name="creation_time")
     */
    private $creationTime;

    /**
     * @ORM\Column(type="integer", name="submission_time", nullable=true)
     */
    private $submissionTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $numeroFascicolo;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $numeroProtocollo;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $idDocumentoProtocollo;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @var ArrayCollection
     */
    private $numeriProtocollo;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $data;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $commenti;

    /**
     * @var string
     */
    private $statusName;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $latestStatusChangeTimestamp;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $latestCPSCommunicationTimestamp;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $latestOperatoreCommunicationTimestamp;

    /**
     * @var Collection
     * @ORM\Column(type="text", nullable=true)
     */
    private $storicoStati;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteNome;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteCognome;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteLuogoNascita;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $richiedenteDataNascita;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteIndirizzoResidenza;

    /**
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $richiedenteCapResidenza;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteCittaResidenza;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteTelefono;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $richiedenteEmail;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accettoIstruzioni;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $iban;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $intestatarioConto;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $lastCompiledStep;

    /**
     * @var string
     * @ORM\Column(type="string",nullable=true)
     */
    private $instanceId;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $esito;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $motivazioneEsito;

    /**
     * Pratica constructor.
     */
    public function __construct()
    {
        if (!$this->id) {
            $this->id = Uuid::uuid4();
        }
        $this->creationTime = time();
        $this->type = self::TYPE_DEFAULT;
        $this->numeroFascicolo = null;
        $this->numeriProtocollo = new ArrayCollection();
        $this->allegati = new ArrayCollection();
        $this->moduliCompilati = new ArrayCollection();
        $this->allegatiOperatore = new ArrayCollection();
        $this->nucleoFamiliare = new ArrayCollection();
        $this->latestStatusChangeTimestamp = $this->latestCPSCommunicationTimestamp = $this->latestOperatoreCommunicationTimestamp = -10000000;
        $this->storicoStati = new ArrayCollection();
        $this->lastCompiledStep = 0;
    }

    /**
     * @return CPSUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param CPSUser $user
     *
     * @return $this
     */
    public function setUser(CPSUser $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Servizio
     */
    public function getServizio()
    {
        return $this->servizio;
    }

    /**
     * @param Servizio $servizio
     *
     * @return $this
     */
    public function setServizio(Servizio $servizio)
    {
        $this->servizio = $servizio;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @param integer $time
     *
     * @return $this
     */
    public function setCreationTime($time)
    {
        $this->creationTime = $time;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $class = new \ReflectionClass(__CLASS__);
        $constants = $class->getConstants();
        foreach ($constants as $name => $value) {
            if ($value == $this->status) {
                return $name;
            }
        }

        return null;
    }

    public static function getStatuses()
    {
        $statuses = [];
        $class = new \ReflectionClass(__CLASS__);
        $constants = $class->getConstants();
        foreach ($constants as $name => $value) {
            if (strpos($name, 'STATUS_') === 0){
                $statuses[$value] = [
                    'id' => $value,
                    'identifier' => $name,
                ];
            }
        }
        return $statuses;
    }


    /**
     * @param $status
     * @param StatusChange|null $statusChange
     *
     * @return $this
     */
    public function setStatus($status, StatusChange $statusChange = null)
    {
        $this->status = $status;
        $this->latestStatusChangeTimestamp = time();
        $timestamp = $this->latestStatusChangeTimestamp;

        if ($statusChange != null) {
            $timestamp = $statusChange->getTimestamp();
        }
        $updated = null;

        $newStatus = [$status, $statusChange ? $statusChange->toArray() : null];

        if ($this->getStoricoStati()->containsKey($timestamp)) {
            $updated = $this->getStoricoStati()->get($timestamp);
            $updated[] = $newStatus;
        } else {
            $updated = [$newStatus];
        }
        $this->storicoStati->set($timestamp, $updated);

        return $this;
    }

    /**
     * @return Erogatore
     */
    public function getErogatore()
    {
        return $this->erogatore;
    }

    /**
     * @param Erogatore $erogatore
     *
     * @return $this
     */
    public function setErogatore(Erogatore $erogatore)
    {
        $this->erogatore = $erogatore;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return OperatoreUser|null
     */
    public function getOperatore()
    {
        return $this->operatore;
    }

    /**
     * @param OperatoreUser $operatore
     *
     * @return Pratica
     */
    public function setOperatore(OperatoreUser $operatore)
    {
        $this->operatore = $operatore;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     *
     * @return Pratica
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNumeroFascicolo()
    {
        return $this->numeroFascicolo;
    }

    /**
     * @param string $numeroFascicolo
     *
     * @return $this
     */
    public function setNumeroFascicolo($numeroFascicolo)
    {
        $this->numeroFascicolo = $numeroFascicolo;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdDocumentoProtocollo()
    {
        return $this->idDocumentoProtocollo;
    }

    /**
     * @param string $idDocumentoProtocollo
     *
     * @return Pratica
     */
    public function setIdDocumentoProtocollo($idDocumentoProtocollo)
    {
        $this->idDocumentoProtocollo = $idDocumentoProtocollo;

        return $this;
    }

    /**
     * @param array $numeroDiProtocollo
     *
     * @return Pratica
     */
    public function addNumeroDiProtocollo($numeroDiProtocollo)
    {
        if (!$this->numeriProtocollo->contains($numeroDiProtocollo)) {
            $this->numeriProtocollo->add($numeroDiProtocollo);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAllegati()
    {
        return $this->allegati;
    }

    /**
     * @param Allegato $allegato
     *
     * @return $this
     */
    public function addAllegato(Allegato $allegato)
    {
        if (!$this->allegati->contains($allegato)) {
            $this->allegati->add($allegato);
            $allegato->addPratica($this);
        }

        return $this;
    }

    /**
     * @param Allegato $allegato
     *
     * @return $this
     */
    public function removeAllegato(Allegato $allegato)
    {
        //TODO: testare e sentire con Nardelli come gestire i nueri di protocollo per gli allegati
        if ($this->allegati->contains($allegato)) {
            $this->allegati->removeElement($allegato);
            $allegato->removePratica($this);
        }

        return $this;
    }

    /**
     * @param ModuloCompilato $modulo
     * @return $this
     */
    public function removeModuloCompilato(ModuloCompilato $modulo)
    {
        if ($this->moduliCompilati->contains($modulo)) {
            $this->moduliCompilati->removeElement($modulo);
            $modulo->removePratica($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getModuliCompilati(): Collection
    {
        return $this->moduliCompilati;
    }

    /**
     * @param ModuloCompilato $modulo
     * @return $this
     */
    public function addModuloCompilato(ModuloCompilato $modulo)
    {
        if (!$this->moduliCompilati->contains($modulo)) {
            $this->moduliCompilati->add($modulo);
            $modulo->addPratica($this);
        }

        return $this;
    }

    /**
     * @param AllegatoOperatore $modulo
     * @return $this
     */
    public function removeAllegatoOperatore( AllegatoOperatore $allegato )
    {
        if ($this->allegatiOperatore->contains($allegato)) {
            $this->allegatiOperatore->removeElement($allegato);
            $allegato->removePratica($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAllegatiOperatore(): Collection
    {
        return $this->allegatiOperatore;
    }

    /**
     * @param AllegatoOperatore $allegato
     * @return $this
     */
    public function addAllegatoOperatore( AllegatoOperatore $allegato )
    {
        if (!$this->allegatiOperatore->contains( $allegato )) {
            $this->allegatiOperatore->add( $allegato );
            $allegato->addPratica( $this );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getNumeroProtocollo()
    {
        return $this->numeroProtocollo;
    }

    /**
     * @param string $numeroProtocollo
     *
     * @return $this
     */
    public function setNumeroProtocollo($numeroProtocollo)
    {
        $this->numeroProtocollo = $numeroProtocollo;

        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function arrayToJson()
    {
        $this->numeriProtocollo = json_encode($this->getNumeriProtocollo()->toArray());
    }

    /**
     * @return mixed
     */
    public function getNumeriProtocollo()
    {
        if (!$this->numeriProtocollo instanceof ArrayCollection) {
            $this->jsonToArray();
        }

        return $this->numeriProtocollo;
    }

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     */
    public function jsonToArray()
    {
        $this->numeriProtocollo = new ArrayCollection(json_decode($this->numeriProtocollo));
    }

    /**
     * @return Collection
     */
    public function getNucleoFamiliare()
    {
        return $this->nucleoFamiliare;
    }

    /**
     * @param Collection $nucleoFamiliare
     *
     * @return $this
     */
    public function setNucleoFamiliare(Collection $nucleoFamiliare)
    {
        $this->nucleoFamiliare = $nucleoFamiliare;

        return $this;
    }

    /**
     * @param ComponenteNucleoFamiliare $componente
     *
     * @return $this
     */
    public function addNucleoFamiliare(ComponenteNucleoFamiliare $componente)
    {
        if (!$this->nucleoFamiliare->contains($componente)) {
            $componente->setPratica($this);
            $this->nucleoFamiliare->add($componente);
        }

        return $this;
    }

    /**
     * @param ComponenteNucleoFamiliare $componente
     *
     * @return $this
     */
    public function removeNucleoFamiliare(ComponenteNucleoFamiliare $componente)
    {
        if ($this->nucleoFamiliare->contains($componente)) {
            $this->nucleoFamiliare->removeElement($componente);
            $componente->setPratica(null);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommenti()
    {
        if (!$this->commenti instanceof ArrayCollection) {
            $this->parseCommenti();
        }

        return $this->commenti;
    }

    /**
     * @param string $commenti
     *
     * @return Pratica
     */
    public function setCommenti($commenti)
    {
        $this->commenti = $commenti;

        return $this;
    }

    /**
     * @param array $commento
     *
     * @return Pratica
     */
    public function addCommento(array $commento)
    {
        if (!$this->getCommenti()->exists(function ($key, $value) use ($commento) {
            return $value['text'] == $commento['text'];
        })
        ) {
            $this->getCommenti()->add($commento);
        }

        return $this;
    }


    /**
     * @ORM\PreFlush()
     */
    public function convertCommentiToString()
    {
        $data = [];
        foreach ($this->getCommenti() as $commento) {
            $data[] = serialize($commento);
        }
        $this->commenti = implode('##', $data);
    }

    /**
     * @ORM\PreFlush()
     */
    public function serializeStatuses()
    {
        if ($this->storicoStati instanceof Collection) {
            $this->storicoStati = serialize($this->storicoStati->toArray());
        }
    }

    /**
     * @return int
     */
    public function getLatestStatusChangeTimestamp(): int
    {
        return $this->latestStatusChangeTimestamp;
    }

    /**
     * @param int $latestStatusChangeTimestamp
     * @return Pratica
     */
    public function setLatestStatusChangeTimestamp($latestStatusChangeTimestamp)
    {
        $this->latestStatusChangeTimestamp = $latestStatusChangeTimestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getLatestCPSCommunicationTimestamp(): int
    {
        return $this->latestCPSCommunicationTimestamp;
    }

    /**
     * @param int $latestCPSCommunicationTimestamp
     * @return Pratica
     */
    public function setLatestCPSCommunicationTimestamp($latestCPSCommunicationTimestamp)
    {
        $this->latestCPSCommunicationTimestamp = $latestCPSCommunicationTimestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getLatestOperatoreCommunicationTimestamp(): int
    {
        return $this->latestOperatoreCommunicationTimestamp;
    }

    /**
     * @param int $latestOperatoreCommunicationTimestamp
     * @return Pratica
     */
    public function setLatestOperatoreCommunicationTimestamp($latestOperatoreCommunicationTimestamp)
    {
        $this->latestOperatoreCommunicationTimestamp = $latestOperatoreCommunicationTimestamp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubmissionTime()
    {
        return $this->submissionTime;
    }

    /**
     * @param $submissionTime
     *
     * @return $this
     */
    public function setSubmissionTime($submissionTime)
    {
        $this->submissionTime = $submissionTime;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getStoricoStati(): Collection
    {
        if (!$this->storicoStati instanceof Collection) {
            $this->storicoStati = new ArrayCollection(unserialize($this->storicoStati));
        }

        return $this->storicoStati;
    }

    /**
     * @param int $status
     * @return null|int
     */
    public function getLatestTimestampForStatus($status)
    {
        $latestTimestamp = null;
        $array = $this->storicoStati->toArray();
        ksort($array);
        foreach ($array as $timestamp => $stati) {
            foreach ($stati as $stato) {
                if ($stato[0] == $status) {
                    $latestTimestamp = $timestamp;
                }
            }
        }

        return $latestTimestamp;
    }

    /**
     * @param $commentoSerialized
     */
    private function parseCommentStringIntoArrayCollection($commentoSerialized)
    {
        $commento = unserialize($commentoSerialized);
        if (is_array($commento) && isset($commento['text']) && !empty($commento['text'])) {
            if (!$this->commenti->exists(function ($key, $value) use ($commento) {
                return $value['text'] == $commento['text'];
            })
            ) {
                $this->commenti->add($commento);
            }
        }
    }

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     */
    private function parseCommenti()
    {
        $data = [];
        if ($this->commenti !== null) {
            $data = explode('##', $this->commenti);
        }
        $this->commenti = new ArrayCollection();
        foreach ($data as $commentoSeriliazed) {
            $this->parseCommentStringIntoArrayCollection($commentoSeriliazed);
        }
    }

    /**
     * @return string
     */
    public function getRichiedenteNome()
    {
        return $this->richiedenteNome;
    }

    /**
     * @param string $richiedenteNome
     *
     * @return Pratica
     */
    public function setRichiedenteNome($richiedenteNome)
    {
        $this->richiedenteNome = $richiedenteNome;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteCognome()
    {
        return $this->richiedenteCognome;
    }

    /**
     * @param string $richiedenteCognome
     *
     * @return Pratica
     */
    public function setRichiedenteCognome($richiedenteCognome)
    {
        $this->richiedenteCognome = $richiedenteCognome;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteLuogoNascita()
    {
        return $this->richiedenteLuogoNascita;
    }

    /**
     * @param string $richiedenteLuogoNascita
     *
     * @return Pratica
     */
    public function setRichiedenteLuogoNascita($richiedenteLuogoNascita)
    {
        $this->richiedenteLuogoNascita = $richiedenteLuogoNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteDataNascita()
    {
        return $this->richiedenteDataNascita;
    }

    /**
     * @param string $richiedenteDataNascita
     *
     * @return Pratica
     */
    public function setRichiedenteDataNascita($richiedenteDataNascita)
    {
        $this->richiedenteDataNascita = $richiedenteDataNascita;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteIndirizzoResidenza()
    {
        return $this->richiedenteIndirizzoResidenza;
    }

    /**
     * @param string $richiedenteIndirizzoResidenza
     *
     * @return Pratica
     */
    public function setRichiedenteIndirizzoResidenza($richiedenteIndirizzoResidenza)
    {
        $this->richiedenteIndirizzoResidenza = $richiedenteIndirizzoResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteCapResidenza()
    {
        return $this->richiedenteCapResidenza;
    }

    /**
     * @param string $richiedenteCapResidenza
     *
     * @return Pratica
     */
    public function setRichiedenteCapResidenza($richiedenteCapResidenza)
    {
        $this->richiedenteCapResidenza = $richiedenteCapResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteCittaResidenza()
    {
        return $this->richiedenteCittaResidenza;
    }

    /**
     * @param string $richiedenteCittaResidenza
     *
     * @return Pratica
     */
    public function setRichiedenteCittaResidenza($richiedenteCittaResidenza)
    {
        $this->richiedenteCittaResidenza = $richiedenteCittaResidenza;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteTelefono()
    {
        return $this->richiedenteTelefono;
    }

    /**
     * @param string $richiedenteTelefono
     *
     * @return Pratica
     */
    public function setRichiedenteTelefono($richiedenteTelefono)
    {
        $this->richiedenteTelefono = $richiedenteTelefono;

        return $this;
    }

    /**
     * @return string
     */
    public function getRichiedenteEmail()
    {
        return $this->richiedenteEmail;
    }

    /**
     * @param string $richiedenteEmail
     *
     * @return Pratica
     */
    public function setRichiedenteEmail($richiedenteEmail)
    {
        $this->richiedenteEmail = $richiedenteEmail;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAccettoIstruzioni()
    {
        return $this->accettoIstruzioni;
    }

    /**
     * @param boolean $accettoIstruzioni
     *
     * @return IscrizioneAsiloNido
     */
    public function setAccettoIstruzioni($accettoIstruzioni)
    {
        $this->accettoIstruzioni = $accettoIstruzioni;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param $iban
     *
     * @return $this
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
        return $this;
    }

    /**
     * @return string
     */
    public function getIntestatarioConto()
    {
        return $this->intestatarioConto;
    }

    /**
     * @param string $intestatarioConto
     */
    public function setIntestatarioConto($intestatarioConto)
    {
        $this->intestatarioConto = $intestatarioConto;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastCompiledStep(): int
    {
        return $this->lastCompiledStep;
    }

    /**
     * @param int $lastCompiledStep
     *
     * @return $this
     */
    public function setLastCompiledStep($lastCompiledStep)
    {
        $this->lastCompiledStep = $lastCompiledStep;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     * @param $instanceId
     *
     * @return $this
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEsito()
    {
        return $this->esito;
    }

    /**
     * @param bool $esito
     */
    public function setEsito(bool $esito)
    {
        $this->esito = $esito;
    }

    /**
     * @return string|null
     */
    public function getMotivazioneEsito()
    {
        return $this->motivazioneEsito;
    }

    /**
     * @param string $motivazioneEsito
     */
    public function setMotivazioneEsito(string $motivazioneEsito)
    {
        $this->motivazioneEsito = $motivazioneEsito;
    }



    /**
     * @return RispostaOperatore
     */
    public function getRispostaOperatore()
    {
        return $this->rispostaOperatore;
    }

    /**
     * @param RispostaOperatore $rispostaOperatore
     * @return $this
     */
    public function addRispostaOperatore($rispostaOperatore)
    {
        $this->rispostaOperatore = $rispostaOperatore;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnte()
    {
        return $this->ente;
    }

    /**
     * @param mixed $ente
     * @return $this
     */
    public function setEnte($ente)
    {
        $this->ente = $ente;

        return $this;
    }
}
