<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class SelezionaEnteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.common.seleziona_ente.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.common.seleziona_ente.title', true);

        $builder->add('ente', EntityType::class, [
            'class' => 'AppBundle\Entity\Ente',
            'choices' => $builder->getData()->getServizio()->getEnti(),
            'choice_label' => 'name',
            'expanded' => false,
            'multiple' => false,
            'label' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'pratica_seleziona_ente';
    }
}
