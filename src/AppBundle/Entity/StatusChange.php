<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

/**
 * Class StatusChange
 */
class StatusChange
{
    private $timestamp;
    private $evento;
    private $operatore;
    private $responsabile;
    private $struttura;

    /**
     * StatusChange constructor.
     * @param object $remoteRequest
     */
    public function __construct($remoteRequest)
    {
        $this->evento = $remoteRequest['evento'];
        $this->operatore = $remoteRequest['operatore'];
        $this->responsabile = $remoteRequest['responsabile'];
        $this->struttura = $remoteRequest['struttura'];
        $this->timestamp = $remoteRequest['timestamp'];
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getEvento(): string
    {
        return $this->evento;
    }

    /**
     * @return string
     */
    public function getOperatore(): string
    {
        return $this->operatore;
    }

    /**
     * @return string
     */
    public function getResponsabile(): string
    {
        return $this->responsabile;
    }

    /**
     * @return string
     */
    public function getStruttura(): string
    {
        return $this->struttura;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode(
            $this->toArray()
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'evento' => $this->evento,
            'operatore' => $this->operatore,
            'responsabile' => $this->responsabile,
            'struttura' => $this->struttura,
            'timestamp' => $this->timestamp,
        ];
    }
}
