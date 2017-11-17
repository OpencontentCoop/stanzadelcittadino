<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;

use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Pratica;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DatiRichiedenteType extends AbstractType
{
    const CAMPI_RICHIEDENTE = array(
        'richiedente_nome' => true,
        'richiedente_cognome' => true,
        'richiedente_luogo_nascita' => true,
        'richiedente_data_nascita' => true,
        'richiedente_indirizzo_residenza' => true,
        'richiedente_cap_residenza' => true,
        'richiedente_citta_residenza' => true,
        'richiedente_telefono' => false,
        'richiedente_email' => false,
    );

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];
        $helper->setGuideText('steps.common.dati_richiedente.guida_alla_compilazione', true);
        $helper->setStepTitle('steps.common.dati_richiedente.title', true);

        /** @var Pratica $pratica */
        $pratica = $builder->getData();

        /** @var CPSUser $user */
        $user = $pratica->getUser();

        foreach (self::CAMPI_RICHIEDENTE as $identifier => $disabledBecauseProvidedByCPS) {
            $type = TextType::class;
            $opts = [
                "label" => 'steps.common.dati_richiedente.' . $identifier,
                'disabled' => $disabledBecauseProvidedByCPS,
            ];
            switch ($identifier) {
                case 'richiedente_telefono':
                    $type = TextType::class;
                    $opts['disabled'] = $user->getTelefono() == null ? false : true;
                    break;
                case 'richiedente_cap':
                    $type = IntegerType::class;
                    break;
                case 'richiedente_email':
                    $type = EmailType::class;
                    $opts['disabled'] = $user->getEmail() == null ? false : true;
                    break;
                case 'richiedente_data_nascita':
                    $type = DateType::class;
                    $opts['widget'] = 'single_text';
                    break;
                default:
                    break;
            }
            $builder->add($identifier, $type, $opts);
        }

    }

    public function getBlockPrefix()
    {
        return 'pratica_richiedente';
    }
}
