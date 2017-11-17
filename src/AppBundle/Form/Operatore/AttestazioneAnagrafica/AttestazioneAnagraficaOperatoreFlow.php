<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Operatore\AttestazioneAnagrafica;

use AppBundle\Form\Operatore\Base\ApprovaORigettaType;
use AppBundle\Form\Operatore\Base\PraticaOperatoreFlow;
use AppBundle\Form\Operatore\Base\UploadRispostaOperatoreType;
use Craue\FormFlowBundle\Form\FormFlowInterface;

/**
 * Class AttestazioneAnagraficaOperatoreFlow
 */
class AttestazioneAnagraficaOperatoreFlow extends PraticaOperatoreFlow
{
    const STEP_APPROVA_O_RIGETTA = 1;
    const STEP_ALLEGA = 2;
    const STEP_ALLEGA_RISPOSTA_FIRMATA = 3;

    protected $allowDynamicStepNavigation = true;

    protected function loadStepsConfig()
    {
        return array(
            self::STEP_APPROVA_O_RIGETTA => array(
                'label' => 'operatori.approva',
                'form_type' => ApprovaORigettaType::class,
            ),
            self::STEP_ALLEGA => array(
                'label' => 'operatori.allega',
                'form_type' => UploadAttestazioneAnagraficaType::class,
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    return $flow->getFormData()->getEsito() === false;
                }
            ),
            self::STEP_ALLEGA_RISPOSTA_FIRMATA => array(
                'label' => 'operatori.allega_risposta_firmata',
                'form_type' => UploadRispostaOperatoreType::class,
            )
        );
    }
}
