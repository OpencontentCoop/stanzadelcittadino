<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Operatore\Base;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ApprovaORigettaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $helper = $options["helper"];
        $helper->setGuideText('operatori.flow.approva_o_rigetta.guida_alla_compilazione', true);

        $builder->add(
            "esito",
            ChoiceType::class,
            [
                "label"    => 'operatori.flow.approva_o_rigetta.esito_label',
                "required" => true,
                "expanded" => true,
                "multiple" => false,
                "choices"  => [
                    "Approva" => true,
                    "Rigetta" => false
                ]
            ]
        );
        $builder->add(
            "motivazioneEsito",
            TextareaType::class,
            [
                "label"    => 'operatori.flow.approva_o_rigetta.motivazione:esito_label',
                "required" => false,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'approva_o_rigetta';
    }
}
