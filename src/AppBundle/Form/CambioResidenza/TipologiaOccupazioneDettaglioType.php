<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\CambioResidenza;

use AppBundle\Entity\CambioResidenza;
use AppBundle\Form\Base\ChooseAllegatoType;
use AppBundle\Form\Extension\TestiAccompagnatoriProcedura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TipologiaOccupazioneDettaglioType extends AbstractType
{
    const OCCUPAZIONE_LOCAZIONE_ERP_FILE_DESCRIPTION = "Contratto o verbale di consegna immobile in locazione";
    const OCCUPAZIONE_AUTOCERTIFICAZIONE_FILE_DESCRIPTION = "Autocertificazione del proprietario dell'appartamento";

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TestiAccompagnatoriProcedura $helper */
        $helper = $options["helper"];

        /** @var CambioResidenza $pratica */
        $pratica = $builder->getData();
        $occupazione = $pratica->getTipoOccupazione();

        $helper->setStepTitle('steps.cambio_residenza.tipologia_occupazione_dettaglio.title', true);

        switch ($occupazione) {
            case CambioResidenza::OCCUPAZIONE_PROPRIETARIO:
                $helper->setGuideText('steps.cambio_residenza.tipologia_occupazione_dettaglio.guida_alla_compilazione.proprietario',
                    true);
                $builder
                    ->add('proprietarioCatastoSezione', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.proprietario.catasto_sezione',
                        'required' => true,
                    ])
                    ->add('proprietarioCatastoFoglio', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.proprietario.catasto_foglio',
                        'required' => true,
                    ])
                    ->add('proprietarioCatastoParticella', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.proprietario.catasto_particella',
                        'required' => false,
                    ])
                    ->add('proprietarioCatastoSubalterno', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.proprietario.catasto_subalterno',
                        'required' => false,
                    ]);
                break;

            case CambioResidenza::OCCUPAZIONE_LOCAZIONE:
                $helper->setGuideText('steps.cambio_residenza.tipologia_occupazione_dettaglio.guida_alla_compilazione.locazione',
                    true);
                $builder
                    ->add('contrattoAgenzia', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.contratto.agenzia',
                        'required' => true,
                    ])
                    ->add('contrattoNumero', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.contratto.numero',
                        'required' => true,
                    ])
                    ->add('contrattoData', DateType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.contratto.data',
                        'required' => true,
                        'widget' => 'single_text',
                        'format' => 'dd-MM-yyyy',
                        'attr' => [
                            'class' => 'form-control input-inline datepicker',
                            'data-provide' => 'datepicker',
                            'data-date-format' => 'dd-mm-yyyy'
                        ]
                    ]);
                break;

            case CambioResidenza::OCCUPAZIONE_LOCAZIONE_ERP:
                $helper->setGuideText('steps.cambio_residenza.tipologia_occupazione_dettaglio.guida_alla_compilazione.locazione_erp',
                    true);
                $builder
                    ->add('verbaleConsegna', ChooseAllegatoType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.verbale_consegna',
                        'fileDescription' => self::OCCUPAZIONE_LOCAZIONE_ERP_FILE_DESCRIPTION,
                        'required' => true,
                        'pratica' => $builder->getData(),
                        'mapped' => false,
                    ]);
                break;

            case CambioResidenza::OCCUPAZIONE_COMODATO:
                $helper->setGuideText('steps.cambio_residenza.tipologia_occupazione_dettaglio.guida_alla_compilazione.comodato',
                    true);
                $builder
                    ->add('contrattoAgenzia', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.contratto.agenzia',
                        'required' => true,
                    ])
                    ->add('contrattoNumero', TextType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.contratto.numero',
                        'required' => true,
                    ])
                    ->add('contrattoData', DateType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.contratto.data',
                        'required' => true,
                        'widget' => 'single_text',
                        'format' => 'dd-MM-yyyy',
                        'attr' => [
                            'class' => 'form-control input-inline datepicker',
                            'data-provide' => 'datepicker',
                            'data-date-format' => 'dd-mm-yyyy'
                        ]
                    ]);
                break;

            case CambioResidenza::OCCUPAZIONE_USUFRUTTO:
                $helper->setGuideText('steps.cambio_residenza.tipologia_occupazione_dettaglio.guida_alla_compilazione.usufruttuario',
                    true);
                $builder
                    ->add('usufruttuarioInfo', TextareaType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.usufruttuario_info',
                        'required' => true,
                    ]);
                break;

            case CambioResidenza::OCCUPAZIONE_AUTOCERTIFICAZIONE:
                $helper->setGuideText('steps.cambio_residenza.tipologia_occupazione_dettaglio.guida_alla_compilazione.autocertificazione',
                    true);
                $builder
                    ->add('autocertificazione', ChooseAllegatoType::class, [
                        'label' => 'steps.cambio_residenza.tipologia_occupazione_dettaglio.autocertificazione',
                        'fileDescription' => self::OCCUPAZIONE_AUTOCERTIFICAZIONE_FILE_DESCRIPTION,
                        'required' => true,
                        'pratica' => $builder->getData(),
                        'mapped' => false,
                    ]);
                break;
        }
    }

    public function getBlockPrefix()
    {
        return 'cambio_residenza_tipologia_occupazione_dettaglio';
    }
}
