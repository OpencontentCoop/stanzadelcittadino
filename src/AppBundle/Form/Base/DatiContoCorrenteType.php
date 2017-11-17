<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DatiContoCorrenteType
 */
class DatiContoCorrenteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.contributo_pannolini.dati_conto_corrente.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.contributo_pannolini.dati_conto_corrente.title', true);

        $builder
            ->add('iban', TextType::class, ['required' => true])
            ->add('intestatarioConto', TextType::class, ['required' => true]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'dati_conto_corrente';
    }
}
