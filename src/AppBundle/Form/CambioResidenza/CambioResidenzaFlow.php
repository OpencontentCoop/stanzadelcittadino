<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use AppBundle\Entity\CambioResidenza;
use AppBundle\Form\Base\AccettazioneIstruzioniType;
use AppBundle\Form\Base\DatiRichiedenteType;
use AppBundle\Form\Base\NucleoFamiliareType;
use AppBundle\Form\Base\PraticaFlow;
use AppBundle\Form\Base\SelezionaEnteType;
use Craue\FormFlowBundle\Form\FormFlowInterface;


class CambioResidenzaFlow extends PraticaFlow
{
    const STEP_SELEZIONA_ENTE = 1;
    const STEP_ACCETTAZIONE_ISTRUZIONI = 2;
    const STEP_DATI_RICHIEDENTE = 3;
    const STEP_DICHIARAZIONE_PROVENIENZA = 4;
    const STEP_DICHIARAZIONE_PROVENIENZA_DETTAGLIO = 5;
    const STEP_DATI_RESIDENZA = 6;
    const STEP_NUCLEO_FAMILIARE = 7;
    const STEP_ATTUALMENTE_RESIDENTI = 8;
    const STEP_TIPOLOGIA_OCCUPAZIONE = 9;
    const STEP_TIPOLOGIA_OCCUPAZIONE_DETTAGLIO = 10;
    const STEP_INFORMAZIONI_ACCERTAMENTO = 11;
    const STEP_CONFERMA = 12;

    protected $allowDynamicStepNavigation = true;

    protected function loadStepsConfig()
    {
        return array(
            self::STEP_SELEZIONA_ENTE => array(
                'label' => 'steps.common.seleziona_ente.label',
                'form_type' => SelezionaEnteType::class,
            ),
            self::STEP_ACCETTAZIONE_ISTRUZIONI => array(
                'label' => 'steps.common.accettazione_istruzioni.label',
                'form_type' => AccettazioneIstruzioniType::class,
            ),
            self::STEP_DATI_RICHIEDENTE => array(
                'label' => 'steps.common.dati_richiedente.label',
                'form_type' => DatiRichiedenteType::class,
            ),
            self::STEP_DICHIARAZIONE_PROVENIENZA => array(
                'label' => 'steps.cambio_residenza.dichiarazione_provenienza.label',
                'form_type' => DichiarazioneProvenienzaType::class,
            ),
            self::STEP_DICHIARAZIONE_PROVENIENZA_DETTAGLIO => array(
                'label' => 'steps.cambio_residenza.dichiarazione_provenienza_dettaglio.label',
                'form_type' => DichiarazioneProvenienzaDettaglioType::class,
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    return $flow->getFormData()->getProvenienza() == CambioResidenza::PROVENIENZA_COMUNE;
                },
            ),
            self::STEP_DATI_RESIDENZA => array(
                'label' => 'steps.cambio_residenza.dati_residenza.label',
                'form_type' => DatiResidenzaType::class,
            ),
            self::STEP_NUCLEO_FAMILIARE => array(
                'label' => 'steps.common.nucleo_familiare.label',
                'form_type' => NucleoFamiliareType::class,
            ),
            self::STEP_ATTUALMENTE_RESIDENTI => array(
                'label' => 'steps.cambio_residenza.attualmente_residenti.label',
                'form_type' => AttualmenteResidentiType::class,
            ),
            self::STEP_TIPOLOGIA_OCCUPAZIONE => array(
                'label' => 'steps.cambio_residenza.tipologia_occupazione.label',
                'form_type' => TipologiaOccupazioneType::class,
            ),
            self::STEP_TIPOLOGIA_OCCUPAZIONE_DETTAGLIO => array(
                'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.label',
                'form_type' => TipologiaOccupazioneDettaglioType::class,
            ),
            self::STEP_INFORMAZIONI_ACCERTAMENTO => array(
                'label' => 'steps.cambio_residenza.informazioni_accertamento.label',
                'form_type' => InformazioneAccertamentoType::class,
            ),
            self::STEP_CONFERMA => array(
                'label' => 'steps.common.conferma.label',
            ),
        );
    }
}
