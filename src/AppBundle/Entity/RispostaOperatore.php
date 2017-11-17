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
 * Class RispostaOperatore
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RispostaOperatore extends Allegato
{

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
     * ModuloCompilato constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = 'risposta_operatore';
        $this->numeriProtocollo = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getIdDocumentoProtocollo()
    {
        return $this->idDocumentoProtocollo;
    }

    /**
     * @param string $idDocumentoProtocollo
     * @return RispostaOperatore
     */
    public function setIdDocumentoProtocollo(string $idDocumentoProtocollo)
    {
        $this->idDocumentoProtocollo = $idDocumentoProtocollo;
        return $this;
    }

    /**
     * @param array $numeroDiProtocollo
     *
     * @return RispostaOperatore
     */
    public function addNumeroDiProtocollo($numeroDiProtocollo)
    {
        if (!$this->numeriProtocollo->contains($numeroDiProtocollo)) {
            $this->numeriProtocollo->add($numeroDiProtocollo);
        }

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



}
