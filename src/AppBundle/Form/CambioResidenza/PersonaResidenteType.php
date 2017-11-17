<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PersonaResidenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('nome', TextType::class, ['required' => true])
                ->add('cognome', TextType::class, ['required' => true])
                ->add('codiceFiscale', TextType::class, ['required' => true])
                ->add('rapportoParentela', TextType::class, ['required' => true]);
    }

    public function getBlockPrefix()
    {
        return 'persona_residente';
    }
}
