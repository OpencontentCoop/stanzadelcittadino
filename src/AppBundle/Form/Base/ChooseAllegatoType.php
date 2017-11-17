<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Base;


use AppBundle\Entity\Allegato;
use AppBundle\Entity\Pratica;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChooseAllegatoType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|EntityRepository
     */

    protected $validator;

    /**
     * ChooseAllegatoType constructor.
     *
     * @param EntityManager $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManager $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('add', FileType::class, [
            'mapped' => false,
            'label' => false,
            'required' => false,
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    }


    /**
     * FormEvents::PRE_SET_DATA $listener
     *
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        $this->addChoice($event->getForm());
    }

    /**
     * FormEvents::PRE_SUBMIT $listener
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $options = $event->getForm()->getConfig()->getOptions();

        /** @var Pratica $pratica */
        $pratica = $options['pratica'];
        $fileDescription = $options['fileDescription'];
        $purgeFiles = $options['purge_files'];
        $class = $options['class'];

        $data = $event->getData();

        $fileUpload = $data['add'] ?? null;
        if (isset( $data['choose'] ) && $data['choose'] != '') {
            $fileChoices = (array)$data['choose'];
        } else {
            $fileChoices = array();
        }

        $hasNewFile = false;

        if ($fileUpload instanceof UploadedFile) {

            $uploadResult = $this->handleUploadedFile($fileUpload, $pratica, $fileDescription, $class);
            if ($uploadResult instanceof ConstraintViolationListInterface) {
                foreach ($uploadResult as $violation) {
                    $event->getForm()->addError(new FormError($violation->getMessage()));
                }
            } else {
                $hasNewFile = $uploadResult->getId();
                $newFileList = [$hasNewFile];
                $this->addChoiceListToPratica($newFileList, $pratica, $fileDescription, $class, $purgeFiles);
            }
        } elseif (!empty( $fileChoices )) {
            $this->addChoiceListToPratica($fileChoices, $pratica, $fileDescription, $class, $purgeFiles);
        }

        if ($options['required']){
            if ($hasNewFile) {
                $event->getForm()->addError(new FormError('Il file è stato caricato correttamente'));
                $data['choose'] = $hasNewFile;
                $event->setData($data);
            }elseif(empty( $fileChoices )) {
                $event->getForm()->addError(new FormError('Il campo file è richiesto'));
            }
        }

        $this->removeChoice($event->getForm());
        $this->addChoice($event->getForm());

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'purge_files' => false,
            'class' => Allegato::class
        ))->setRequired(array(
            'fileDescription',
            'pratica'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'choose_allegato';
    }

    /**
     * @param Pratica $pratica
     * @param $fileDescription
     *
     * @return Allegato[]
     */
    protected function getCurrentAllegati(Pratica $pratica, $fileDescription, $class)
    {
        $user = $pratica->getUser();
        $queryBuilder = $this->entityManager->getRepository($class)->createQueryBuilder('a');
        $queryBuilder->setCacheable(false);

        return $queryBuilder
            ->where('a.owner = :user AND a.description = :fileDescription')
            ->andWhere($queryBuilder->expr()->isInstanceOf('a', $class))
            ->andWhere(':praticaId MEMBER OF a.pratiche')
            ->setParameter('user', $user)
            ->setParameter('praticaId', $pratica->getId())
            ->setParameter('fileDescription', $fileDescription)
            ->orderBy('a.updatedAt', 'DESC')
            ->getQuery()->execute();
    }

    /**
     * @param Pratica $pratica
     * @param $fileDescription
     *
     * @return Allegato[]
     */
    protected function getAllAllegati(Pratica $pratica, $fileDescription, $class)
    {
        $user = $pratica->getUser();
        $queryBuilder = $this->entityManager->getRepository($class)->createQueryBuilder('a');

        return $queryBuilder
            ->where('a.owner = :user AND a.description = :fileDescription')
            ->andWhere($queryBuilder->expr()->isInstanceOf('a', $class))
            ->setParameter('user', $user)
            ->setParameter('fileDescription', $fileDescription)
            ->orderBy('a.updatedAt', 'DESC')
            ->getQuery()->execute();
    }

    /**
     * @param FormInterface $form
     */
    protected function addChoice(FormInterface $form)
    {
        $options = $form->getConfig()->getOptions();

        /** @var Pratica $pratica */
        $pratica = $options['pratica'];

        $fileDescription = $options['fileDescription'];
        $class = $options['class'];

        $fileChoices = $this->getCurrentAllegati($pratica, $fileDescription, $class);
        $allAllegati = $this->getAllAllegati($pratica, $fileDescription, $class);
        $form->add('choose', EntityType::class, [
            'class' => $class,
            'choices' => $allAllegati,
            'choice_label' => 'name',
            'mapped' => false,
            'expanded' => true,
            'multiple' => false,
            'required' => $options['required'] && count($fileChoices) > 0,
            'data' => count($fileChoices) > 0 ? $fileChoices[0] : null,
            'label' => false,
            'placeholder' => 'Carica un nuovo file..'
        ]);
    }

    /**
     * @param FormInterface $form
     */
    private function removeChoice(FormInterface $form)
    {
        $form->remove('choose');
    }

    /**
     * @param UploadedFile $fileUpload
     * @param Pratica $pratica
     * @param $fileDescription
     *
     * @return Allegato|ConstraintViolationListInterface
     */
    protected function handleUploadedFile(UploadedFile $fileUpload, Pratica $pratica, $fileDescription, $class)
    {
        /** @var Allegato $newAllegato */
        $newAllegato = new $class();
        $newAllegato->setFile($fileUpload);
        $newAllegato->setDescription($fileDescription);
        $newAllegato->setOwner($pratica->getUser());
        $violations = $this->validator->validate($newAllegato);

        if ($violations->count() > 0) {
            return $violations;
        } else {
            $this->entityManager->persist($newAllegato);
            $this->entityManager->flush();

            return $newAllegato;
        }
    }

    /**
     * @param array $fileChoices
     * @param Pratica $pratica
     * @param $fileDescription
     * @param bool $purgeFiles
     */
    private function addChoiceListToPratica(
        array &$fileChoices,
        Pratica $pratica,
        $fileDescription,
        $class,
        $purgeFiles = false
    ) {
        foreach ($fileChoices as $key => $fileChoose) {
            $allegato = $this->entityManager->getRepository($class)->findOneById($fileChoose);

            $reflect = new \ReflectionClass($allegato);
            $method = 'add' . $reflect->getShortName();
            if ($allegato instanceof Allegato) {
                $pratica->$method ($allegato);
                break;
            }
        }

        if (!empty( $fileChoices )) {
            $currentAllegati = $this->getCurrentAllegati($pratica, $fileDescription, $class);
            foreach ($currentAllegati as $praticaAllegato) {
                if (!in_array((string)$praticaAllegato->getId(), $fileChoices)) {
                    $pratica->removeAllegato($praticaAllegato);
                    if ($purgeFiles && $praticaAllegato->getPratiche()->isEmpty()) {
                        $this->entityManager->remove($praticaAllegato);
                        $this->entityManager->flush();
                    }
                }
            }

            $this->entityManager->persist($pratica);
            $this->entityManager->flush();
        }
    }
}
