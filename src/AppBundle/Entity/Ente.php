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
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="ente")
 * @ORM\HasLifecycleCallbacks
 */
class Ente
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $codiceMeccanografico;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AsiloNido", cascade={"remove"})
     * @ORM\JoinTable(
     *     name="ente_asili",
     *     joinColumns={@ORM\JoinColumn(name="ente_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="asilo_id", referencedColumnName="id")}
     * )
     */
    private $asili;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OperatoreUser", mappedBy="ente", fetch="EAGER")
     */
    private $operatori;

    /**
     * @var ArrayCollection
     * @ORM\Column(type="text")
     */
    private $protocolloParameters;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $siteUrl;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Erogatore", mappedBy="enti")
     * @var Collection;
     */
    private $erogatori;

    /**
     * Ente constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->asili = new ArrayCollection();
        $this->protocolloParameters = new ArrayCollection();
        $this->operatori = new ArrayCollection();
        $this->erogatori = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function getNameForEmail()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return Collection
     */
    public function getAsili()
    {
        return $this->asili;
    }

    /**
     * @param AsiloNido[] $asili
     *
     * @return $this
     */
    public function setAsili($asili)
    {
        $this->asili = $asili;

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
     * @return string
     */
    public function getCodiceMeccanografico()
    {
        return $this->codiceMeccanografico;
    }

    /**
     * @param string $codiceMeccanografico
     * @return Ente
     */
    public function setCodiceMeccanografico($codiceMeccanografico)
    {
        $this->codiceMeccanografico = $codiceMeccanografico;

        return $this;
    }

    /**
     * @param AsiloNido $asilo
     * @return $this
     */
    public function addAsilo(AsiloNido $asilo)
    {
        if (!$this->asili->contains($asilo)) {
            $this->asili->add($asilo);
        }

        return $this;
    }

    /**
     * @param Servizio $servizio
     * @return mixed
     */
    public function getProtocolloParametersPerServizio(Servizio $servizio)
    {
        $this->parseProtocolloParameters();
        if ($this->protocolloParameters->containsKey($servizio->getSlug())) {
            return $this->protocolloParameters->get($servizio->getSlug());
        }

        return  null;
    }

    /**
     * @param mixed    $protocolloParameters
     * @param Servizio $servizio
     * @return Ente
     */
    public function setProtocolloParametersPerServizio($protocolloParameters, Servizio $servizio)
    {
        $this->parseProtocolloParameters();
        $this->protocolloParameters->set($servizio->getSlug(), $protocolloParameters);

        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function serializeProtocolloParameters()
    {
        if ($this->protocolloParameters instanceof Collection) {
            $this->protocolloParameters = serialize($this->protocolloParameters->toArray());
        }
    }

    /**
     * @ORM\PostLoad()
     * @ORM\PostUpdate()
     */
    public function parseProtocolloParameters()
    {
        if (!$this->protocolloParameters instanceof ArrayCollection) {
            $this->protocolloParameters = new ArrayCollection(unserialize($this->protocolloParameters));
        }
    }

    /**
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * @param string $siteUrl
     *
     * @return Ente
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getOperatori(): Collection
    {
        return $this->operatori;
    }

    /**
     * @return Collection
     */
    public function getErogatori(): Collection
    {
        return $this->erogatori;
    }
}
