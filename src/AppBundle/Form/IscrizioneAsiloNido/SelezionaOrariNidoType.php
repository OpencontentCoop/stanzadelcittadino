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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SelezionaOrariNidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.iscrizione_asilo_nido.seleziona_orari.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.iscrizione_asilo_nido.seleziona_orari.title', true);

        /** @var AsiloNido $asilo */
        $asilo = $pratica = $builder->getData()->getStruttura();
        $orari = array_combine($asilo->getOrari(), $asilo->getOrari());

        $builder
            ->add('struttura_orario', ChoiceType::class, [
                "required" => true,
                "label" => 'steps.iscrizione_asilo_nido.seleziona_orari.seleziona_orario',
                'expanded' => true,
                'choices' => $orari,
            ])
            ->add('periodo_iscrizione_da', DateType::class, [
                'required' => true,
                'label' => 'steps.iscrizione_asilo_nido.seleziona_orari.periodo_iscrizione_da',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker-range-from',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])
            ->add('periodo_iscrizione_a', DateType::class, [
                'required' => true,
                'label' => 'steps.iscrizione_asilo_nido.seleziona_orari.periodo_iscrizione_a',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker-range-to',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
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

        if ( strtotime($data['periodo_iscrizione_da']) >= strtotime($data['periodo_iscrizione_a']))
        {
            $event->getForm()->addError(
                new FormError($helper->translate('steps.iscrizione_asilo_nido.seleziona_orari.error'))
            );
        }
    }

    public function getBlockPrefix()
    {
        return 'iscrizione_asilo_nido_orari';
    }
}
