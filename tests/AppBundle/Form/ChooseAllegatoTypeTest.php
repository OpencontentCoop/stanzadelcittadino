<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Form;

use AppBundle\Entity\Allegato;
use AppBundle\Entity\AllegatoOperatore;
use AppBundle\Entity\AsiloNido;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Ente;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use AppBundle\Form\Base\ChooseAllegatoType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Form\PreloadedExtension;
use Tests\AppBundle\Base\AbstractAppTestCase;

class ChooseAllegatoTypeTest extends AbstractAppTestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    private $emRegistry;

    public function setUp()
    {
        parent::setUp();

        system('rm -rf ' . __DIR__ . "/../../../var/uploads/pratiche/allegati/*");

        $this->em->getConnection()->executeQuery('DELETE FROM servizio_erogatori')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM erogatore_ente')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM ente_asili')->execute();
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Allegato::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(Servizio::class);
        $this->cleanDb(AsiloNido::class);
        $this->cleanDb(OperatoreUser::class);
        $this->cleanDb(Ente::class);
        $this->cleanDb(User::class);

        /* TODO FIXME use mockup */
        $this->emRegistry = new Registry(
            $this->container,
            $this->container->getParameter('doctrine.connections'),
            $this->container->getParameter('doctrine.entity_managers'),
            $this->container->getParameter('doctrine.default_connection'),
            $this->container->getParameter('doctrine.default_entity_manager')
        );

        $this->factory = Forms::createFormFactoryBuilder()->addExtensions($this->getExtensions())->getFormFactory();

    }

    protected function getExtensions()
    {
        $type = new ChooseAllegatoType($this->em, $this->container->get('validator'));
        return array(
            new PreloadedExtension(array($type), array()),
            new DoctrineOrmExtension($this->emRegistry),
        );
    }

    public function chooseAllegatoTypeDataProvider()
    {
        return array(
            array(Allegato::class),
            array(AllegatoOperatore::class),
        );
    }

    /**
     * @dataProvider chooseAllegatoTypeDataProvider
     * @param $class
     */
    public function testISeeAListOfFileWithPredefinedDescription($class)
    {
        $label = 'testLabel';
        $fileDescription = 'testFileDescription';

        $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        $expected = 2;
        for($i=1; $i<=$expected; $i++){
            $this->addNewAllegatoForUser($fileDescription, $user, $class);
        }

        $form = $this->factory->create(
            ChooseAllegatoType::class,
            null,
            [
                'label' => $label,
                'fileDescription' => $fileDescription,
                'required' => true,
                'pratica' => $pratica,
                'mapped' => false,
                'class' => $class,
            ]
        );

        $view = $form->createView();
        foreach($view->children as $child){
            if($child->vars["name"] == 'choose'){
                /** @var \Symfony\Component\Form\ChoiceList\View\ChoiceView $choice */
                $number = 0;
                foreach($child->vars["choices"] as $choice){
                    if ($choice->data instanceof Allegato){
                        $this->assertEquals($fileDescription, $choice->data->getDescription());
                        $number++;
                    }
                }
                $this->assertEquals($expected,$number);
            }
        }
    }

    /**
     * @dataProvider chooseAllegatoTypeDataProvider
     * @param $class
     */
    public function testISeeAErrorIfTypeIsRequiredAndNoFilesAreSelected($class)
    {
        $label = 'testLabel';
        $fileDescription = 'testFileDescription';

        $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        $form = $this->factory->create(
            ChooseAllegatoType::class,
            null,
            [
                'label' => $label,
                'fileDescription' => $fileDescription,
                'required' => true,
                'pratica' => $pratica,
                'mapped' => false,
                'class' => $class,
            ]
        );

        $formData = array();
        $form->submit($formData);
        foreach($form->getErrors() as $error){
            $this->assertEquals("Il campo file è richiesto", $error->getMessage());
        }
        $this->assertTrue($form->isSynchronized());
    }

    /**
     * @dataProvider chooseAllegatoTypeDataProvider
     * @param $class
     */
    public function testISeeNewFileInList($class)
    {
        $label = 'testLabel';
        $fileDescription = 'testFileDescription';

        $user = $this->createCPSUser();
        $pratica = $this->createPratica($user);

        $form = $this->factory->create(
            ChooseAllegatoType::class,
            null,
            [
                'label' => $label,
                'fileDescription' => $fileDescription,
                'required' => true,
                'pratica' => $pratica,
                'mapped' => false,
                'class' => $class,
            ]
        );


        copy(__DIR__.'/test.pdf', __DIR__.'/run_test.pdf');
        $file = new UploadedFile(__DIR__.'/run_test.pdf', 'test.pdf', null, null, null, true);

        $formData = array('add' => $file);

        $form->submit($formData);

        foreach($form->getErrors() as $error){
            $this->assertEquals("Il file è stato caricato correttamente", $error->getMessage());

        }

        $view = $form->createView();

        foreach($view->children as $child){
            if($child->vars["name"] == 'choose'){
                /** @var \Symfony\Component\Form\ChoiceList\View\ChoiceView $choice */
                foreach($child->vars["choices"] as $choice){
                    if ($choice->data instanceof Allegato){
                        $this->assertEquals('test.pdf', $choice->data->getOriginalFilename());
                    }
                }
            }
        }

    }

    private function addNewAllegatoForUser($description, CPSUser $user, $class)
    {
        /** @var Allegato $allegato */
        $allegato = new $class();
        $allegato->setOwner($user);
        $allegato->setDescription($description);
        $allegato->setFilename(rand(1,5).'somefile.pdf');
        $allegato->setOriginalFilename(rand(1,5).'somefile.pdf');
        $this->em->persist($allegato);

        $this->em->flush();

        return $allegato;
    }

}
