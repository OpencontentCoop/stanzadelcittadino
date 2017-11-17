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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class DatiInterventoType extends AbstractType
{
    const TIPI_INTERVENTO = [
        'nuovo allaccio',
        'rifacimento allaccio',
        'spostamento allaccio',
    ];

    const TIPI_ALLACCIO = [
        'rete idrica e fognaria',
        'solo rete idrica',
        'solo rete fognaria',
    ];

    const TIPI_USO = [
        "DOMESTICO",
        "NON DOMESTICO (uffici, negozi etc.)",
        "ALTRI USI (commercio, artigianale, terziario, ecc..)",
        "PRODUTTIVO O INDUSTRIALE con uso d'acqua per un ciclo produttivo",
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.allacciamento_acquedotto.dati_intervento.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.allacciamento_acquedotto.dati_intervento.title', true);

        $builder
            ->add('allacciamentoAcquedottoTipoIntervento', ChoiceType::class, [
                'required' => true,
                'choices' => array_combine(self::TIPI_INTERVENTO, self::TIPI_INTERVENTO),
                'expanded' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_intervento.tipo_intervento',
            ])
            ->add('allacciamentoAcquedottoTipoAllaccio', ChoiceType::class, [
                'required' => true,
                'choices' => array_combine(self::TIPI_ALLACCIO, self::TIPI_ALLACCIO),
                'expanded' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_intervento.tipo_allaccio',
            ])
            ->add('allacciamentoAcquedottoTipoUso', ChoiceType::class, [
                'required' => true,
                'choices' => array_combine(self::TIPI_USO, self::TIPI_USO),
                'expanded' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_intervento.tipo_uso',
            ])
            ->add('allacciamentoAcquedottoDiametroReteInterna', NumberType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_intervento.diametro_rete_interna',
            ]);

    }

    public function getBlockPrefix()
    {
        return 'allacciamento_acquedotto_dati_intervento';
    }
}
