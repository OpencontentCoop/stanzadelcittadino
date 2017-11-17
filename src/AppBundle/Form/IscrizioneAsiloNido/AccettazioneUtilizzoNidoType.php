<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\IscrizioneAsiloNido;

use AppBundle\Entity\AsiloNido;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;


class AccettazioneUtilizzoNidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var AsiloNido $asilo */
        $asilo = $pratica = $builder->getData()->getStruttura();

        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setDescriptionText(
            '<div class="well well-sm service-disclaimer">' .
            $asilo->getSchedaInformativa() .
            '</div>'
        );

        $helper->setGuideText('steps.iscrizione_asilo_nido.accettazione_utilizzo.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.iscrizione_asilo_nido.accettazione_utilizzo.title', true);

        $builder->add(
            'accetto_utilizzo',
            CheckboxType::class,
            ["required" => true, "label" => 'steps.iscrizione_asilo_nido.accettazione_utilizzo.accetto_utilizzo']
        );
    }

    public function getBlockPrefix()
    {
        return 'iscrizione_asilo_nido_utilizzo_nido';
    }
}
