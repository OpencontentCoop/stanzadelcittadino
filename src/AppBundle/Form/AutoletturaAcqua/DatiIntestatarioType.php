<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\AutoletturaAcqua;

use AppBundle\Entity\CPSUser;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DatiIntestatarioType extends AbstractType
{
    const CAMPI_INTESTATARIO = array(
        'intestatario_codice_utente',
        'intestatario_nome',
        'intestatario_cognome',
        'intestatario_indirizzo',
        'intestatario_cap',
        'intestatario_citta',
        'intestatario_telefono',
        'intestatario_email',
    );

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.autolettura_acqua.dati_intestatario.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.autolettura_acqua.dati_intestatario.title', true);

        /** @var CPSUser $user */
        $user = $builder->getData()->getUser();

        foreach (self::CAMPI_INTESTATARIO as $identifier) {
            $type = TextType::class;
            $opts = [
                "label" => 'steps.autolettura_acqua.dati_intestatario.'.$identifier
            ];
            switch ($identifier) {
                case 'intestatario_telefono':
                    $type = TextType::class;
                    break;
                case 'intestatario_cap':
                    $type = IntegerType::class;
                    break;
                case 'intestatario_email':
                    $type = EmailType::class;
                    break;
                default:
                    break;
            }
            $builder->add($identifier, $type, $opts);
        }
    }

    public function getBlockPrefix()
    {
        return 'autolettura_acqua_intestatario';
    }
}
