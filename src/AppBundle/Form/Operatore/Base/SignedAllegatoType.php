<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form\Operatore\Base;


use AppBundle\Entity\Allegato;
use AppBundle\Entity\Pratica;
use AppBundle\Form\Base\ChooseAllegatoType;
use AppBundle\Services\P7MSignatureCheckService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class SignedAllegatoType extends ChooseAllegatoType
{
    /**
     * @var P7MSignatureCheckService
     */
    protected $service;

    /**
     * ChooseAllegatoType constructor.
     *
     * @param EntityManager $entityManager
     * @param ValidatorInterface $validator
     * @param P7MSignatureCheckService $service
     */
    public function __construct(EntityManager $entityManager, ValidatorInterface $validator, P7MSignatureCheckService $service)
    {
        parent::__construct($entityManager, $validator);
        $this->service = $service;
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
        //$violations = $this->validator->validate($newAllegato);
        $violations = $this->validator->validate(
            $this->service->check($fileUpload->getPathname()),
            new IsTrue(['message' => 'Il file non è p7m'])
        );

        if ($violations->count() > 0) {
            return $violations;
        } elseif (!$this->service->check($fileUpload->getPathname())) {
            $violations = $this->validator->validate(
                $this->service->check($fileUpload->getPathname()),
                new IsTrue(['message' => 'Il file non è p7m'])
            );
            return $violations;
        } else {
            $this->entityManager->persist($newAllegato);
            $this->entityManager->flush();

            return $newAllegato;
        }
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
            ->andWhere($queryBuilder->expr()->isNull('a.numeroProtocollo'))
            ->setParameter('user', $user)
            ->setParameter('fileDescription', $fileDescription)
            ->orderBy('a.updatedAt', 'DESC')
            ->getQuery()->execute();
    }


}
