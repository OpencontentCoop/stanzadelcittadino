<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\ContributoAssociazioni;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OrgRichiedenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ruoloUtenteOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.ruolo_utente'])
            ->add('ragioneSocialeOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.ragione_sociale'])
            ->add('cfOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.cf'])
            ->add('pivaOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.piva'])
            ->add('indirizzoOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.indirizzo'])
            ->add('civicoOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.civico'])
            ->add('capOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.cap'])
            ->add('comuneOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.comune'])
            ->add('provinciaOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.provincia'])
            ->add('emailOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.email'])
            ->add('telOrgRichiedente', TextType::class, ["label" => 'steps.common.org_richiedente.tel']);
    }

    public function getBlockPrefix()
    {
        return 'contributo_associazioni_org_richiedente';
    }
}
