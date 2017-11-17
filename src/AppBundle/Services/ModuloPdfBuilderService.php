<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;


use AppBundle\Entity\Allegato;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\ModuloCompilato;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\RispostaOperatore;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\GeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Translation\TranslatorInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class ModuloPdfBuilderService
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PropertyMappingFactory
     */
    private $propertyMappingFactory;

    /**
     * @var DirectoryNamerInterface
     */
    private $directoryNamer;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var string
     */
    private $dateTimeFormat;

    public function __construct(
        Filesystem $filesystem,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        PropertyMappingFactory $propertyMappingFactory,
        DirectoryNamerInterface $directoryNamer,
        GeneratorInterface $generator,
        EngineInterface $templating,
        $dateTimeFormat
    ) {
        $this->filesystem = $filesystem;
        $this->em = $em;
        $this->translator = $translator;
        $this->propertyMappingFactory = $propertyMappingFactory;
        $this->directoryNamer = $directoryNamer;
        $this->generator = $generator;
        $this->templating = $templating;
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @param Pratica $pratica
     * @param CPSUser $user
     *
     * @return RispostaOperatore
     */
    public function createUnsignedResponseForPratica(Pratica $pratica)
    {
        $unsignedResponse = new RispostaOperatore();
        $this->createAllegatoInstance($pratica, $unsignedResponse);
        $servizioName = $pratica->getServizio()->getName();
        $now = new \DateTime();
        $now->setTimestamp($pratica->getSubmissionTime());
        $unsignedResponse->setOriginalFilename("Servizio {$servizioName} " . $now->format('Ymdhi'));
        $unsignedResponse->setDescription(
            $this->translator->trans(
                'pratica.modulo.descrizioneRisposta',
                [
                    'nomeservizio' => $pratica->getServizio()->getName(),
                    'datacompilazione' => $now->format($this->dateTimeFormat)
                ])
        );
        $this->em->persist($unsignedResponse);
        return $unsignedResponse;
    }

    /**
     * @param Pratica $pratica
     * @param CPSUser $user
     *
     * @return ModuloCompilato
     */
    public function createForPratica(Pratica $pratica)
    {
        $moduloCompilato = new ModuloCompilato();
        $this->createAllegatoInstance($pratica, $moduloCompilato);
        $servizioName = $pratica->getServizio()->getName();
        $now = new \DateTime();
        $now->setTimestamp($pratica->getSubmissionTime());
        $moduloCompilato->setOriginalFilename("Modulo {$servizioName} " . $now->format('Ymdhi'));
        $moduloCompilato->setDescription(
            $this->translator->trans(
                'pratica.modulo.descrizione',
                [
                    'nomeservizio' => $pratica->getServizio()->getName(),
                    'datacompilazione' => $now->format($this->dateTimeFormat)
                ])
        );
        $this->em->persist($moduloCompilato);

        return $moduloCompilato;
    }

    /**
     * @param Pratica $pratica
     *
     * @return string
     */
    private function renderForPratica(Pratica $pratica)
    {
        $className = (new \ReflectionClass($pratica))->getShortName();

        return $this->renderForClass($pratica, $className);
    }

    /**
     * @param Pratica $pratica
     *
     * @return string
     */
    private function renderForResponse(Pratica $pratica)
    {
        $className = (new \ReflectionClass(RispostaOperatore::class))->getShortName();

        return $this->renderForClass($pratica, $className);
    }

    /**
     * @param ModuloCompilato $moduloCompilato
     *
     * @return string
     */
    private function getDestinationDirectoryFromContext(Allegato $moduloCompilato)
    {
        /** @var PropertyMapping $mapping */
        $mapping = $this->propertyMappingFactory->fromObject($moduloCompilato)[0];
        $path = $this->directoryNamer->directoryName($moduloCompilato, $mapping);
        $destinationDirectory = $mapping->getUploadDestination() . '/' . $path;

        return $destinationDirectory;
    }

    /**
     * @param Pratica $pratica
     * @param $allegato
     */
    private function createAllegatoInstance(Pratica $pratica, Allegato $allegato)
    {
        $content = null;
        if($allegato instanceof RispostaOperatore) {
            $content = $this->renderForResponse($pratica);
        } else {
            $content = $this->renderForPratica($pratica);
        }

        $allegato->setOwner($pratica->getUser());
        $destinationDirectory = $this->getDestinationDirectoryFromContext($allegato);
        $fileName = uniqid().'.pdf';
        $filePath = $destinationDirectory.DIRECTORY_SEPARATOR.$fileName;

        $this->filesystem->dumpFile($filePath, $content);
        $allegato->setFile(new File($filePath));
        $allegato->setFilename($fileName);
    }

    /**
     * @param Pratica $pratica
     * @param $className
     * @return string
     */
    private function renderForClass(Pratica $pratica, $className): string
    {
        $html = $this->templating->render('AppBundle:Pratiche:pdf/'.$className.'.html.twig', [
            'pratica' => $pratica,
            'user' => $pratica->getUser(),
        ]);

        $header = $this->templating->render('@App/Pratiche/pdf/parts/header.html.twig', [
            'pratica' => $pratica,
            'user' => $pratica->getUser(),
        ]);
        $footer = $this->templating->render('@App/Pratiche/pdf/parts/footer.html.twig', [
            'pratica' => $pratica,
            'user' => $pratica->getUser(),
        ]);

        $content = $this->generator->getOutputFromHtml($html, array(
            'header-html' => $header,
            'footer-html' => $footer,
            'margin-top' => 20,
            'margin-right' => 0,
            'margin-bottom' => 20,
            'header-spacing' => 6,
            'encoding' => 'UTF-8',
            'margin-left' => 0,
            'images' => true,
            'no-background' => false,
            'lowquality' => false
        ));

        return $content;
    }
}
