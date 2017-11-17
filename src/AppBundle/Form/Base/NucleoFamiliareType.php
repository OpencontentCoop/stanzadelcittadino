<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class NucleoFamiliareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.common.nucleo_familiare.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.common.nucleo_familiare.title', true);

        $builder->add('nucleo_familiare', CollectionType::class, [
            "entry_type" => ComponenteNucleoFamiliareType::class,
            "entry_options" => ["label" => false],
            "allow_add" => true,
            "allow_delete" => true,
            "by_reference" => false,
            "label" => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'nucleo_familiare';
    }
}
