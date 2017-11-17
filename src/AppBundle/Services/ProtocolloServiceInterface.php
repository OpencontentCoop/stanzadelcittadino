<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\AllegatoInterface;
use AppBundle\Entity\Pratica;

interface ProtocolloServiceInterface
{
    public function protocollaPratica(Pratica $pratica);

    public function protocollaRisposta(Pratica $pratica);

    public function protocollaAllegato(Pratica $pratica, AllegatoInterface $allegato);

    public function getHandler();
}
