<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\OccupazioneSuoloPubblico;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OccupazioneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('indirizzoOccupazione', TextType::class, ["label" => 'steps.occupazione_suolo_pubblico.occupazione.indirizzo'])
            ->add('civicoOccupazione', TextType::class, ["label" => 'steps.occupazione_suolo_pubblico.occupazione.civico'])
            ->add('lunghezzaOccupazione', TextType::class, ["label" => 'steps.occupazione_suolo_pubblico.occupazione.lunghezza'])
            ->add('larghezzaOccupazione', TextType::class, ["label" => 'steps.occupazione_suolo_pubblico.occupazione.larghezza'])
            ->add('metriQuadriOccupazione', TextType::class, ["label" => 'steps.occupazione_suolo_pubblico.occupazione.metri_quadri'])
            ->add('motivazioneOccupazione', TextareaType::class, ["label" => 'steps.occupazione_suolo_pubblico.occupazione.motivazione']);
    }

    public function getBlockPrefix()
    {
        return 'occupazione_suolo_pubblico_occupazione';
    }
}
