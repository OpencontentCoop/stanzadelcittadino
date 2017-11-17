<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Protocollo;

use AppBundle\Entity\AllegatoInterface;
use AppBundle\Entity\Pratica;


class DummyProtocolloHandler implements ProtocolloHandlerInterface
{

    /**
     * @param Pratica $pratica
     *
     */
    public function sendPraticaToProtocollo(Pratica $pratica)
    {
        $pratica->setIdDocumentoProtocollo(rand(0,100));
        $pratica->setNumeroProtocollo(rand(100,200));
        $pratica->setNumeroFascicolo(rand(200,300));
    }

    /**
     * @param Pratica $pratica
     * @param AllegatoInterface $allegato
     */
    public function sendAllegatoToProtocollo(Pratica $pratica, AllegatoInterface $allegato)
    {
        $pratica->addNumeroDiProtocollo([
            'id' => $allegato->getId(),
            'protocollo' => rand(0,100),
        ]);
    }

    public function sendRispostaToProtocollo(Pratica $pratica)
    {
        $risposta = $pratica->getRispostaOperatore();
        $risposta->setNumeroProtocollo(rand(0,100));
        $risposta->setIdDocumentoProtocollo(rand(100,200));
    }

    public function sendAllegatoRispostaToProtocollo(Pratica $pratica, AllegatoInterface $allegato)
    {
        $risposta = $pratica->getRispostaOperatore();
        $risposta->addNumeroDiProtocollo([
            'id' => $allegato->getId(),
            'protocollo' => rand(0,100),
        ]);
    }

}
