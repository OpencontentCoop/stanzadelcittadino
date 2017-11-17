<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\ContributoAssociazioni;

use AppBundle\Form\Base\AccettazioneIstruzioniType;
use AppBundle\Form\Base\DatiContoCorrenteType;
use AppBundle\Form\Base\DatiRichiedenteType;
use AppBundle\Form\Base\PraticaFlow;
use AppBundle\Form\Base\SelezionaEnteType;

/**
 * Class ContributoAssociazioniFlow
 */
class ContributoAssociazioniFlow extends PraticaFlow
{
    const STEP_SELEZIONA_ENTE = 1;
    const STEP_ACCETTAZIONE_ISTRUZIONI = 2;
    const STEP_DATI_RICHIEDENTE = 3;
    const STEP_DATI_ORG_RICHIEDENTE = 4;
    const STEP_TIPO_ATTIVITA = 5;
    const STEP_CONTRIBUTO = 6;
    const STEP_DATI_CONTO_CORRENTE = 7;
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
                'label' => 'steps.common.org_richiedente.label',
                'form_type' => OrgRichiedenteType::class
            ),
            self::STEP_TIPO_ATTIVITA => array(
                'label' => 'steps.contributo_associazioni.tipologia_attivita.label',
                'form_type' => TipologiaAttivitaType::class
            ),
            self::STEP_CONTRIBUTO => array(
                'label' => 'steps.contributo_associazioni.contributo.label',
                'form_type' => ContributoType::class
            ),
            self::STEP_DATI_CONTO_CORRENTE => array(
                'label' => 'steps.common.dati_conto_corrente.label',
                'form_type' => DatiContoCorrenteType::class,
            ),
            self::STEP_CONFERMA => array(
                'label' => 'steps.common.conferma.label',
            )
        );
    }
}
