<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\ContributoAssociazioni;

use AppBundle\Entity\ContributoAssociazioni;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;


class TipologiaAttivitaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.contributo_associazioni.tipologia_attivita.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.contributo_associazioni.tipologia_attivita.title', true);

        /** @var ContributoAssociazioni $pratica */
        $pratica = $builder->getData();
        $tipologie = [];
        foreach ($pratica->getTipologieAttivita() as $type) {
            $tipologie[$helper->translate('steps.contributo_associazioni.tipologia_attivita.' . $type)] = $type;
        }

        $builder
            ->add('tipologiaAttivita', ChoiceType::class, [
                "label" => 'steps.contributo_associazioni.tipologia_attivita.tipologia',
                'expanded' => true,
                'choices' => $tipologie,
            ]);
    }

    /**
     * FormEvents::PRE_SUBMIT $listener
     *
     * @param FormEvent $event
     */

    public function getBlockPrefix()
    {
        return 'contributo_associazioni_tipologia_attivita';
    }


}
