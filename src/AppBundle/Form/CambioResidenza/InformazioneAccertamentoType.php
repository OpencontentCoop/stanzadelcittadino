<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class InformazioneAccertamentoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];

        $helper->setGuideText('steps.cambio_residenza.informazioni_accertamento.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.cambio_residenza.informazioni_accertamento.title', true);
        $builder
            ->add('infoAccertamento', TextareaType::class, [
                'label' => false,
                'required' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'cambio_residenza_informazioni_accertamento';
    }
}
