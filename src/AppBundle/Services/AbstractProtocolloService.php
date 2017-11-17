<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\AllegatoInterface;
use AppBundle\Entity\Pratica;
use AppBundle\Protocollo\Exception\AlreadySentException;
use AppBundle\Protocollo\Exception\AlreadyUploadException;
use AppBundle\Protocollo\Exception\ParentNotRegisteredException;

class AbstractProtocolloService
{
    protected function validatePratica(Pratica $pratica)
    {
        if ($pratica->getNumeroProtocollo() !== null) {
            throw new AlreadySentException();
        }

        foreach ($pratica->getAllegati() as $allegato) {
            $this->validateUploadFile($pratica, $allegato);
        }
    }

    protected function validatePraticaForUploadFile(Pratica $pratica)
    {
        if ($pratica->getNumeroProtocollo() === null) {
            throw new ParentNotRegisteredException();
        }
    }

    protected function validateUploadFile(Pratica $pratica, AllegatoInterface $allegato)
    {
        $alreadySent = false;
        foreach ($pratica->getNumeriProtocollo() as $item) {
            $item = (array)$item;
            if ($item['id'] == $allegato->getId()) {
                $alreadySent = true;
            }
        }

        if ($alreadySent) {
            throw new AlreadyUploadException();
        }
    }

    protected function validateRisposta(Pratica $pratica)
    {
        $risposta = $pratica->getRispostaOperatore();
        if ($risposta->getNumeroProtocollo() !== null) {
            throw new AlreadySentException();
        }

        foreach ($pratica->getAllegatiOperatore() as $allegato) {
            $this->validateRispostaUploadFile($pratica, $allegato);
        }
    }

    protected function validateRispostaUploadFile(Pratica $pratica, AllegatoInterface $allegato)
    {
        $risposta = $pratica->getRispostaOperatore();
        $alreadySent = false;
        foreach ($risposta->getNumeriProtocollo() as $item) {
            $item = (array)$item;
            if ($item['id'] == $allegato->getId()) {
                $alreadySent = true;
            }
        }

        if ($alreadySent) {
            throw new AlreadyUploadException();
        }
    }
}
