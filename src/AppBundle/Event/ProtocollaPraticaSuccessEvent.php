<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Event;

use AppBundle\Entity\Pratica;
use Symfony\Component\EventDispatcher\Event;

class ProtocollaPraticaSuccessEvent extends Event
{
    /**
     * @var Pratica
     */
    private $pratica;

    public function __construct(Pratica $pratica)
    {
        $this->pratica = $pratica;
    }

    /**
     * @return Pratica
     */
    public function getPratica()
    {
        return $this->pratica;
    }

}
