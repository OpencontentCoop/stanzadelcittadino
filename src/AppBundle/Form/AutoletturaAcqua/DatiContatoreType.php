<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\AutoletturaAcqua;

use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DatiContatoreType extends AbstractType
{
    const CAMPI_CONTATORE = [
        'contatore_numero',
        'contatore_uso',
        'contatore_unita_immobiliari',
    ];

    const TIPI_USO = [
        "DOMESTICO",
        "NON DOMESTICO (uffici, negozi etc.)",
        "IRRIGUO (giardino, orto)",
        "ALLEVAMENTO ANIMALI (stalle per abbevera mento animali)",
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.autolettura_acqua.dati_contatore.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.autolettura_acqua.dati_contatore.title', true);

        foreach (self::CAMPI_CONTATORE as $identifier) {
            $type = TextType::class;
            $opts = [
                "label" => 'steps.autolettura_acqua.dati_contatore.'.$identifier
            ];
            switch ($identifier) {
                case 'contatore_uso':
                    $type = ChoiceType::class;
                    $opts['choices'] = array_combine( self::TIPI_USO, self::TIPI_USO);
                    $opts['expanded'] = true;
                    break;
                case 'contatore_unita_immobiliari':
                    $type = IntegerType::class;
                    break;
                default:
                    break;
            }
            $builder->add($identifier, $type, $opts);
        }
    }

    public function getBlockPrefix()
    {
        return 'autolettura_acqua_contatore';
    }
}
