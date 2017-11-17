<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\IscrizioneAsiloNido;

use AppBundle\Form\Base\AccettazioneIstruzioniType;
use AppBundle\Form\Base\DatiRichiedenteType;
use AppBundle\Form\Base\NucleoFamiliareType;
use AppBundle\Form\Base\PraticaFlow;
use AppBundle\Form\Base\SelezionaEnteType;

class IscrizioneAsiloNidoFlow extends PraticaFlow
{

    const STEP_SELEZIONA_ENTE = 1;
    const STEP_ACCETTAZIONE_ISTRUZIONI = 2;
    const STEP_SELEZIONA_NIDO = 3;
    const STEP_ACCETTAZIONE_UTILIZZO_NIDO = 4;
    const STEP_SELEZIONA_ORARI_NIDO = 5;
    const STEP_DATI_RICHIEDENTE = 6;
    const STEP_DATI_BAMBINO = 7;
    const STEP_NUCLEO_FAMILIARE = 8;
    const STEP_ALLEGA_ATTESTAZIONE_ICEF = 9;
    const STEP_CONFERMA = 10;

    protected $allowDynamicStepNavigation = true;
    protected $handleFileUploads = false;


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
            self::STEP_SELEZIONA_NIDO => array(
                'label' => 'steps.iscrizione_asilo_nido.seleziona_nido.label',
                'form_type' => SelezionaNidoType::class,
            ),
            self::STEP_ACCETTAZIONE_UTILIZZO_NIDO => array(
                'label' => 'steps.iscrizione_asilo_nido.accettazione_utilizzo.label',
                'form_type' => AccettazioneUtilizzoNidoType::class,
            ),
            self::STEP_SELEZIONA_ORARI_NIDO => array(
                'label' => 'steps.iscrizione_asilo_nido.seleziona_orari.label',
                'form_type' => SelezionaOrariNidoType::class,
            ),
            self::STEP_DATI_RICHIEDENTE => array(
                'label' => 'steps.common.dati_richiedente.label',
                'form_type' => DatiRichiedenteType::class,
            ),
            self::STEP_DATI_BAMBINO => array(
                'label' => 'steps.iscrizione_asilo_nido.dati_bambino.label',
                'form_type' => DatiBambinoType::class,
            ),
            self::STEP_NUCLEO_FAMILIARE => array(
                'label' => 'steps.common.nucleo_familiare.label',
                'form_type' => NucleoFamiliareType::class,
            ),
            self::STEP_ALLEGA_ATTESTAZIONE_ICEF => array(
                'label' => 'steps.iscrizione_asilo_nido.allega_attestazione_icef.label',
                'form_type' => AttestazioneIcefType::class,
            ),
            self::STEP_CONFERMA => array(
                'label' => 'steps.common.conferma.label',
            ),
        );
    }
}
