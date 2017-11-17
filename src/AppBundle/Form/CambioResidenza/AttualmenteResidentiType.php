<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class AttualmenteResidentiType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.cambio_residenza.attualmente_residenti.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.cambio_residenza.attualmente_residenti.title', true);

        $builder
            ->add('persone_residenti', CollectionType::class, [
                'entry_type' => PersonaResidenteType::class,
                'allow_add' => true,
                'label' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'cambio_residenza_persone_attualmente_residenti';
    }
}
