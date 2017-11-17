<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Services;

use AppBundle\Entity\TerminiUtilizzo;
use AppBundle\Services\TermsAcceptanceCheckerService;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class TermsAcceptanceCheckerServiceTest
 */
class TermsAcceptanceCheckerServiceTest extends AbstractAppTestCase
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->cleanDb(TerminiUtilizzo::class);
    }

    /**
     * @test
     */
    public function testItExists()
    {
        $service = $this->container->get('ocsdc.cps.terms_acceptance_checker');
        $this->assertInstanceOf(TermsAcceptanceCheckerService::class, $service);
    }

    /**
     * @test
     */
    public function testReturnsFalseIfUserNeverAcceptedTerms()
    {
        $this->createDefaultTerm(true);

        $cpsUser = $this->createCPSUser(false);
        $service = new TermsAcceptanceCheckerService($this->container->get('doctrine'));
        $this->assertFalse($service->checkIfUserHasAcceptedMandatoryTerms($cpsUser));
    }

    /**
     * @test
     */
    public function testReturnsTrueIfUserAcceptedAllMandatoryTerms()
    {
        $this->createDefaultTerm(true);

        $cpsUser = $this->createCPSUser(true);
        $service = new TermsAcceptanceCheckerService($this->container->get('doctrine'));
        $this->assertTrue($service->checkIfUserHasAcceptedMandatoryTerms($cpsUser));
    }

    /**
     * @test
     */
    public function testReturnsFalseIfUserDidntAcceptAllMandatoryTerms()
    {
        $mandatoryTerm1 = new TerminiUtilizzo();
        $mandatoryTerm1->setName('memento mori')
            ->setText('Ricordati che devi Rovereto')
            ->setMandatory(true);
        $this->em->persist($mandatoryTerm1);
        $mandatoryTerm2 = new TerminiUtilizzo();
        $mandatoryTerm2->setName('Verona ha')
            ->setText('Una squadra sola, forsa hellas')
            ->setMandatory(true);
        $this->em->persist($mandatoryTerm2);
        $this->em->flush();

        $cpsUser = $this->createCPSUser(false);
        $cpsUser->addTermsAcceptance($mandatoryTerm1);
        $service = new TermsAcceptanceCheckerService($this->container->get('doctrine'));
        $this->assertFalse($service->checkIfUserHasAcceptedMandatoryTerms($cpsUser));
    }

    /**
     * @test
     */
    public function testReturnsFalseIfUserDidntAcceptLatestVersionOfAllMandatoryTerms()
    {
        $mandatoryTerm = new TerminiUtilizzo();
        $mandatoryTerm->setName('memento mori')
            ->setText('Ricordati che devi Rovereto')
            ->setMandatory(true);
        $this->em->persist($mandatoryTerm);
        $cpsUser = $this->createCPSUser(false);
        $cpsUser->addTermsAcceptance($mandatoryTerm);
        $this->em->flush();

        $mandatoryTerm->setLatestRevision(time()+1000);
        $mandatoryTerm->setText('Ricordati che devi Arco');

        $service = new TermsAcceptanceCheckerService($this->container->get('doctrine'));
        $this->assertFalse($service->checkIfUserHasAcceptedMandatoryTerms($cpsUser));
    }
}
