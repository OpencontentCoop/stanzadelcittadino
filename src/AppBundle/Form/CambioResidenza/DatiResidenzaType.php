<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class DatiResidenzaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.cambio_residenza.dati_residenza.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.cambio_residenza.dati_residenza.title', true);

        $builder
            ->add('residenzaProvincia', TextType::class, [
                'required' => true,
                'label' => 'steps.cambio_residenza.dati_residenza.provincia',
            ])
            ->add('residenzaComune', TextType::class, [
                'required' => true,
                'label' => 'steps.cambio_residenza.dati_residenza.comune',
            ])
            ->add('residenzaIndirizzo', TextType::class, [
                'required' => true,
                'label' => 'steps.cambio_residenza.dati_residenza.indirizzo',
            ])
            ->add('residenzaNumeroCivico', TextType::class, [
                'required' => true,
                'label' => 'steps.cambio_residenza.dati_residenza.numero_civico',
            ])
            ->add('residenzaScala', TextType::class, [
                'required' => false,
                'label' => 'steps.cambio_residenza.dati_residenza.scala',
            ])
            ->add('residenzaPiano', TextType::class, [
                'required' => false,
                'label' => 'steps.cambio_residenza.dati_residenza.piano',
            ])
            ->add('residenzaInterno', TextType::class, [
                'required' => false,
                'label' => 'steps.cambio_residenza.dati_residenza.interno',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'cambio_residenza_dati_residenza';
    }
}
