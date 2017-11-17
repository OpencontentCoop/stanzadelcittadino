<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\OccupazioneSuoloPubblico;

use AppBundle\Entity\OccupazioneSuoloPubblico;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TempoOccupazioneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var OccupazioneSuoloPubblico $pratica */
        $pratica = $builder->getData();
        $orari = $pratica->getOrari();

        $builder
            ->add('inizioOccupazioneGiorno', DateType::class, [
                'required' => true,
                'label' => 'steps.occupazione_suolo_pubblico.tempo_occupazione.inizio_giorno',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker-range-from',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])
            ->add('inizioOccupazioneOra', ChoiceType::class, [
                "label" => 'steps.occupazione_suolo_pubblico.tempo_occupazione.inizio_ora',
                'expanded' => false,
                'choices' => $orari,
            ])
            ->add('fineOccupazioneGiorno', DateType::class, [
                'required' => true,
                'label' => 'steps.occupazione_suolo_pubblico.tempo_occupazione.fine_giorno',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker-range-to',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])
            ->add('fineOccupazioneOra', ChoiceType::class, [
                "label" => 'steps.occupazione_suolo_pubblico.tempo_occupazione.fine_ora',
                'expanded' => false,
                'choices' => $orari,
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    /**
     * FormEvents::PRE_SUBMIT $listener
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $options = $event->getForm()->getConfig()->getOptions();

        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];

        if ( strtotime($data['inizioOccupazioneGiorno']) >= strtotime($data['fineOccupazioneGiorno']))
        {
            $event->getForm()->addError(
                new FormError($helper->translate('steps.occupazione_suolo_pubblico.tempo_occupazione.error'))
            );
        }
    }

    public function getBlockPrefix()
    {
        return 'occupazione_suolo_pubblico_tempo_occupazione';
    }


}
