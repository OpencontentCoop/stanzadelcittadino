<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use AppBundle\Entity\CambioResidenza;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class DichiarazioneProvenienzaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.cambio_residenza.dichiarazione_provenienza.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.cambio_residenza.dichiarazione_provenienza.title', true);

        /** @var CambioResidenza $pratica */
        $pratica = $builder->getData();
        $choices = array();
        foreach ($pratica->getTipiProvenienza() as $provenienza) {
            $choices[$helper->translate('steps.cambio_residenza.dichiarazione_provenienza.' . $provenienza)] = $provenienza;
        }

        $builder->add('provenienza', ChoiceType::class, [
            'choices' => $choices,
            'expanded' => true,
            'multiple' => false,
            'label' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'cambio_residenza_dichiarazione_provenienza';
    }
}
