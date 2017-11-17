<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\AllacciamentoAcquedotto;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DatiImmobileType extends AbstractType
{

    const TIPI_QUALIFICA = [
        'proprietario',
        'locatario',
        'erede/familiare/convivente',
        'assegnatario dell\'immobile',
        'altro',
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.allacciamento_acquedotto.dati_immobile.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.allacciamento_acquedotto.dati_immobile.title', true);

        $builder
            ->add('allacciamentoAcquedottoImmobileQualifica', ChoiceType::class, [
                'required' => true,
                'choices' => array_combine(self::TIPI_QUALIFICA, self::TIPI_QUALIFICA),
                'expanded' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.qualifica',
            ])
            ->add('allacciamentoAcquedottoImmobileProvincia', TextType::class, [
                'required' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.provincia',
            ])
            ->add('allacciamentoAcquedottoImmobileComune', TextType::class, [
                'required' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.comune',
            ])
            ->add('allacciamentoAcquedottoImmobileIndirizzo', TextType::class, [
                'required' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.indirizzo',
            ])
            ->add('allacciamentoAcquedottoImmobileNumeroCivico', TextType::class, [
                'required' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.numero_civico',
            ])
            ->add('allacciamentoAcquedottoImmobileCap', IntegerType::class, [
                'required' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.cap',
            ])
            ->add('allacciamentoAcquedottoImmobileScala', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.scala',
            ])
            ->add('allacciamentoAcquedottoImmobilePiano', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.piano',
            ])
            ->add('allacciamentoAcquedottoImmobileInterno', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.interno',
            ])
            ->add('allacciamentoAcquedottoImmobileCatastoCategoria', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.catasto_categoria',
            ])
            ->add('allacciamentoAcquedottoImmobileCatastoCodiceComune', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.catasto_codice_comune',
            ])
            ->add('allacciamentoAcquedottoImmobileCatastoFoglio', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.catasto_foglio',
            ])
            ->add('allacciamentoAcquedottoImmobileCatastoSezione', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.catasto_sezione',
            ])
            ->add('allacciamentoAcquedottoImmobileCatastoMappale', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_immobile.catasto_mappale',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'allacciamento_acquedotto_dati_immobile';
    }
}
