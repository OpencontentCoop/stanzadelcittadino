<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Operatore\Base;

use AppBundle\Entity\Pratica;
use AppBundle\Entity\RispostaOperatore;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UploadRispostaOperatoreType extends AbstractType
{
    const FILE_DESCRIPTION = "File Risposta firmato (formato p7m)";


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Pratica $pratica */
        $pratica = $builder->getData();

        $helper = $options["helper"];
        $helper->setGuideText('operatori.flow.upload_risposta_firmata.guida_alla_compilazione', true);
        $helper->setDescriptionText('operatori.flow.upload_risposta_firmata.testo_descrittivo', true, [
            '%link_download_risposta%' => '/operatori/'.$pratica->getId().'/risposta_non_firmata'
        ]);

        $builder
            ->add('allegati_operatore', SignedAllegatoType::class, [
                'label' => 'operatori.flow.upload_risposta_firmata.allega_risposta_firmata',
                'fileDescription' => self::FILE_DESCRIPTION,
                'required' => true,
                'pratica' => $pratica,
                'class' => RispostaOperatore::class,
                'mapped' => false
            ]);
    }

    public function getBlockPrefix()
    {
        return 'upload_risposta_firmata';
    }
}
