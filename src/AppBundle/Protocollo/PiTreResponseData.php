<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Protocollo;


class PiTreResponseData
{
    private $rawData;
    private $data;

    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
        $this->data = $this->rawData['data'] ?? array();
    }

    public function getStatus()
    {
        return $this->rawData['status'] ?? null;
    }

    public function getMessage()
    {
        return $this->rawData['message'] ?? null;
    }

    public function getIdDoc()
    {
        return $this->data['id_doc'] ?? null;
    }

    public function getNProt()
    {
        return $this->data['n_prot'] ?? null;
    }

    public function getIdProj()
    {
        return $this->data['id_proj'] ?? null;
    }

    public function __toString()
    {
        return json_encode($this->rawData);
    }
}
