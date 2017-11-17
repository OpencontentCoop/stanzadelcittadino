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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DatiContattoType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.allacciamento_acquedotto.dati_contatto.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.allacciamento_acquedotto.dati_contatto.title', true);

        $builder
            ->add('allacciamentoAcquedottoUseAlternateContact', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    "Usa l'indirizzo di residenza" => 0,
                    "Usa i dati sotto riportati" => 1,
                ],
                'expanded' => true,
                'label' => 'steps.allacciamento_acquedotto.dati_contatto.use_alternate',
            ])
            ->add('allacciamentoAcquedottoAlternateContactVia', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_contatto.indirizzo',
            ])
            ->add('allacciamentoAcquedottoAlternateContactCivico', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_contatto.numero_civico',
            ])
            ->add('allacciamentoAcquedottoAlternateContactCAP', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_contatto.cap',
            ])
            ->add('allacciamentoAcquedottoAlternateContactComune', TextType::class, [
                'required' => false,
                'label' => 'steps.allacciamento_acquedotto.dati_contatto.comune',
            ]);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

    }

    public function getBlockPrefix()
    {
        return 'allacciamento_acquedotto_dati_contatto';
    }

    /**
     * FormEvents::PRE_SUBMIT $listener
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if ((int)$data['allacciamentoAcquedottoUseAlternateContact'] == 1){
            unset($data['allacciamentoAcquedottoUseAlternateContact']);
            foreach($data as $key => $value){
                if (empty($value)){
                    $event->getForm()->addError(new FormError('Completa tutti i campi'));
                    break;
                }
            }
        }
    }

}
