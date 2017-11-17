<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;


class AccettazioneIstruzioniType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setDescriptionText(
            '<div class="well well-sm service-disclaimer">' .
            $builder->getData()->getServizio()->getTestoIstruzioni() .
            '</div>'
        );
        $helper->setStepTitle('steps.common.accettazione_istruzioni.title', true);

        $helper->setGuideText('steps.common.accettazione_istruzioni.guida_alla_compilazione', true);

        $builder->add(
            'accetto_istruzioni',
            CheckboxType::class,
            [
                "required" => true,
                "label" => 'steps.common.accettazione_istruzioni.accetto_istruzioni',
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'pratica_accettazione_istruzioni';
    }

}
