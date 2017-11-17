<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\ContributoPannolini;

use AppBundle\Entity\ContributoPannolini;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DatiAcquistoType
 */
class DatiAcquistoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.contributo_pannolini.dati_acquisto.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.contributo_pannolini.dati_acquisto.title', true);

        $builder
            ->add('tipoPannolini', ChoiceType::class, [
                'required' => true,
                'label' => 'steps.contributo_pannolini.dati_acquisto.tipo_pannolini',
                'choices' => [ 'Lavabile' => ContributoPannolini::PANNOLINO_LAVABILE,  'Biopannolino' => ContributoPannolini::PANNOLINO_BIOPANNOLINO ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('nomePuntoVendita', TextType::class, [
                'required' => true,
                'label' => 'steps.contributo_pannolini.dati_acquisto.punto_vendita',
            ])
            ->add('dataAcquisto', DateType::class, [
                'required' => true,
                'label' => 'steps.contributo_pannolini.dati_acquisto.data_acquisto',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy'
                ]
            ])
            ->add('totaleSpesa', NumberType::class, [
                'required' => true,
                'label' => 'steps.contributo_pannolini.dati_acquisto.totale_spesa',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'contributo_pannolini_dati_acquisto';
    }

}
