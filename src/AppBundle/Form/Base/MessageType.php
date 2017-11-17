<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MessageType
 */
class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $data = $builder->getData();

        $builder
            ->add('sender_id', HiddenType::class, array(
                'required' => true,
                'data' => $data['sender_id'],
                'mapped' => false,
                'attr' => ['hidden' => true]
            ))
            ->add('thread_id', HiddenType::class, array(
                'required' => true,
                'data' => $data['thread_id'],
                'mapped' => false,
                'attr' => ['hidden' => true],
            ))
            ->add('return_url', HiddenType::class, array(
                'required' => false,
                'data' => isset($data['return_url']) ? $data['return_url'] : '',
                'mapped' => false,
                'attr' => ['hidden' => true],
            ))
            ->add('message', TextType::class, array('required' => true, 'label' => 'messaggi.messaggio', 'attr' => ['class' => 'message']))
            ->add('submit', SubmitType::class, array('label' => 'messaggi.invia'));
    }
}
