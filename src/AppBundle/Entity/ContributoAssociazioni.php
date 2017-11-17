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
class ContributoAssociazioni extends Pratica
{

    const TIPOLOGIA_ATTIVITA_COMMERCIALE = 'commerciale';
    const TIPOLOGIA_ATTIVITA_NON_COMMERCIALE = 'non_commerciale';

    const TIPOLOGIA_CONTRIBUTO_BENI_STRUMENTALI = 'beni_strumentali';
    const TIPOLOGIA_CONTRIBUTO_FINI_ISTITUZIONALI_COMMERCIALI = 'fini_istituzionali_commerciali';
    const TIPOLOGIA_CONTRIBUTO_FINI_ISTITUZIONALI_NON_COMMERCIALI = 'fini_istituzionali_non_commerciali';
    const TIPOLOGIA_CONTRIBUTO_MANIFESTAZIONE_ISTITUZIONALE_NON_COMMERCIALE = 'manifestazione_istituzionale_non_commerciale';
    const TIPOLOGIA_CONTRIBUTO_COMMERCIALE = 'commerciale';

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
    private $cfOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pivaOrgRichiedente;

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
    private $emailOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $telOrgRichiedente;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $tipologiaAttivita;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $usoContributo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descrizioneContributo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $annoAttivita;

    private $tipologieAttivita;

    private $tipologieUsoContributo;

    /**
     * OccupazioneSuoloPubblico constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_CONTRIBUTO_ASSOCIAZIONI;
    }

    public function getTipologieAttivita()
    {
        if ($this->tipologieAttivita === null) {
            $this->tipologieAttivita = array();
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            foreach ($constants as $name => $value) {
                if (strpos($name, 'TIPOLOGIA_ATTIVITA_') !== false) {
                    $this->tipologieAttivita[] = $value;
                }
            }
        }
        return $this->tipologieAttivita;
    }

    public function getTipologieUsoContributo()
    {
        if ($this->tipologieUsoContributo === null) {
            $this->tipologieUsoContributo = array();
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            foreach ($constants as $name => $value) {
                if (strpos($name, 'TIPOLOGIA_CONTRIBUTO_') !== false) {
                    $this->tipologieUsoContributo[] = $value;
                }
            }
        }
        return $this->tipologieUsoContributo;
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
    public function getCfOrgRichiedente()
    {
        return $this->cfOrgRichiedente;
    }

    /**
     * @param mixed $cfOrgRichiedente
     */
    public function setCfOrgRichiedente($cfOrgRichiedente)
    {
        $this->cfOrgRichiedente = $cfOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getPivaOrgRichiedente()
    {
        return $this->pivaOrgRichiedente;
    }

    /**
     * @param mixed $pivaOrgRichiedente
     */
    public function setPivaOrgRichiedente($pivaOrgRichiedente)
    {
        $this->pivaOrgRichiedente = $pivaOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getComuneOrgRichiedente()
    {
        return $this->comuneOrgRichiedente;
    }

    /**
     * @param mixed $comuneOrgRichiedente
     */
    public function setComuneOrgRichiedente($comuneOrgRichiedente)
    {
        $this->comuneOrgRichiedente = $comuneOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getIndirizzoOrgRichiedente()
    {
        return $this->indirizzoOrgRichiedente;
    }

    /**
     * @param mixed $indirizzoOrgRichiedente
     */
    public function setIndirizzoOrgRichiedente($indirizzoOrgRichiedente)
    {
        $this->indirizzoOrgRichiedente = $indirizzoOrgRichiedente;
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
    public function getEmailOrgRichiedente()
    {
        return $this->emailOrgRichiedente;
    }

    /**
     * @param mixed $emailOrgRichiedente
     */
    public function setEmailOrgRichiedente($emailOrgRichiedente)
    {
        $this->emailOrgRichiedente = $emailOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getTelOrgRichiedente()
    {
        return $this->telOrgRichiedente;
    }

    /**
     * @param mixed $telOrgRichiedente
     */
    public function setTelOrgRichiedente($telOrgRichiedente)
    {
        $this->telOrgRichiedente = $telOrgRichiedente;
    }

    /**
     * @return mixed
     */
    public function getTipologiaAttivita()
    {
        return $this->tipologiaAttivita;
    }

    /**
     * @param mixed $tipologiaAttivita
     */
    public function setTipologiaAttivita($tipologiaAttivita)
    {
        $this->tipologiaAttivita = $tipologiaAttivita;
    }

    /**
     * @return mixed
     */
    public function getUsoContributo()
    {
        return $this->usoContributo;
    }

    /**
     * @param mixed $usoContributo
     */
    public function setUsoContributo($usoContributo)
    {
        $this->usoContributo = $usoContributo;
    }

    /**
     * @return mixed
     */
    public function getDescrizioneContributo()
    {
        return $this->descrizioneContributo;
    }

    /**
     * @param mixed $descrizioneContributo
     */
    public function setDescrizioneContributo($descrizioneContributo)
    {
        $this->descrizioneContributo = $descrizioneContributo;
    }

    /**
     * @return mixed
     */
    public function getAnnoAttivita()
    {
        return $this->annoAttivita;
    }

    /**
     * @param mixed $annoAttivita
     */
    public function setAnnoAttivita($annoAttivita)
    {
        $this->annoAttivita = $annoAttivita;
    }
}
