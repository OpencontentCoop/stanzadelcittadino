<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContributoPannolini
 * @ORM\Entity
 */
class CertificatoNascita extends Pratica
{
    /**
     * @var boolean
     * @ORM\Column(name="allegato_operatore_richiesto", type="boolean")
     */
    private $allegatoOperatoreRichiesto;

    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_CERTIFICATO_NASCITA;
        $this->allegatoOperatoreRichiesto = true;
    }

    /**
     * @return boolean
     */
    public function isAllegatoOperatoreRichiesto(): bool
    {
        return $this->allegatoOperatoreRichiesto;
    }

}
