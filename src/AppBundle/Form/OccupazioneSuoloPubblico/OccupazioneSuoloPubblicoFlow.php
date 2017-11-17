<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\OccupazioneSuoloPubblico;

use AppBundle\Entity\OccupazioneSuoloPubblico;
use AppBundle\Form\Base\AccettazioneIstruzioniType;
use AppBundle\Form\Base\DatiRichiedenteType;
use AppBundle\Form\Base\PraticaFlow;
use AppBundle\Form\Base\SelezionaEnteType;
use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Class OccupazioneSuoloPubblicoFlow
 */
class OccupazioneSuoloPubblicoFlow extends PraticaFlow
{
    const STEP_SELEZIONA_ENTE = 1;
    const STEP_ACCETTAZIONE_ISTRUZIONI = 2;
    const STEP_DATI_RICHIEDENTE = 3;
    const STEP_DATI_ORG_RICHIEDENTE = 4;
    const STEP_OCCUPAZIONE = 5;
    const STEP_TIPO_OCCUPAZIONE = 6;
    const STEP_TEMPO_OCCUPAZIONE = 7;
    const STEP_CONFERMA = 8;

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
            self::STEP_DATI_ORG_RICHIEDENTE => array(
                'label' => 'steps.occupazione_suolo_pubblico.org_richiedente.label',
                'form_type' => OrgRichiedenteType::class
            ),
            self::STEP_OCCUPAZIONE => array(
                'label' => 'steps.occupazione_suolo_pubblico.occupazione.label',
                'form_type' => OccupazioneType::class
            ),
            self::STEP_TIPO_OCCUPAZIONE => array(
                'label' => 'steps.occupazione_suolo_pubblico.tipologia_occupazione.label',
                'form_type' => TipologiaOccupazioneType::class
            ),
            self::STEP_TEMPO_OCCUPAZIONE => array(
                'label' => 'steps.occupazione_suolo_pubblico.tempo_occupazione.label',
                'form_type' => TempoOccupazioneType::class,
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    return $flow->getFormData()->getTipologiaOccupazione() == OccupazioneSuoloPubblico::TIPOLOGIA_PERMANENTE;
                }
            ),
            self::STEP_CONFERMA => array(
                'label' => 'steps.common.conferma.label',
            )
        );
    }
}
