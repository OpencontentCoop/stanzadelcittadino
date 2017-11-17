<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Command\LoadServiziCommand;
use AppBundle\DataFixtures\ORM\LoadData;
use AppBundle\Entity\Allegato;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Ente;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\Servizio;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\AppBundle\Base\AbstractAppTestCase;
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google\Spreadsheet\SpreadsheetService;

/**
 * Class ServizioCreateCommandTest
 */
class LoadServiziCommandTest extends AbstractAppTestCase
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->em->getConnection()->executeQuery('DELETE FROM servizio_erogatori')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM erogatore_ente')->execute();
        $this->em->getConnection()->executeQuery('DELETE FROM ente_asili')->execute();
        $this->cleanDb(ComponenteNucleoFamiliare::class);
        $this->cleanDb(Pratica::class);
        $this->cleanDb(OperatoreUser::class);
        $this->cleanDb(Ente::class);
        $this->cleanDb(Servizio::class);
    }

    /**
     * @test
     */
    public function testExecute()
    {
        $expectedServicesCount = $this->getCountServizi();
        $serviziRepo = $this->em->getRepository(Servizio::class);
        $this->assertEquals(0, count($serviziRepo->findAll()));

        $application = new Application(self::$kernel);
        $application->add(new LoadServiziCommand());

        $command = $application->find('ocsdc:carica-servizi');
        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command' => $command->getName(),
        ));

        $this->assertEquals($expectedServicesCount, count($serviziRepo->findAll()));

        $output = $commandTester->getDisplay();
        $this->assertContains('Servizi caricati: '.$expectedServicesCount, $output);
        $this->assertContains('Servizi aggiornati: 0', $output);

        //all services have a erogatore
        foreach ($serviziRepo->findAll() as $servizio) {
            $this->assertGreaterThanOrEqual(1, $servizio->getErogatori()->count());
        }


        //idempotence
        $commandTester->execute(array(
            'command' => $command->getName(),
        ));
        $this->assertEquals($expectedServicesCount, count($serviziRepo->findAll()));
        $output = $commandTester->getDisplay();
        $this->assertContains('Servizi caricati: 0', $output);
        $this->assertContains('Servizi aggiornati: '.$expectedServicesCount, $output);
    }

    private function getCountServizi()
    {
        $serviceRequest = new DefaultServiceRequest("");
        ServiceRequestFactory::setInstance($serviceRequest);

        $spreadsheetService = new SpreadsheetService();
        $worksheetFeed = $spreadsheetService->getPublicSpreadsheet(LoadData::PUBLIC_SPREADSHEETS_ID);
        $worksheet = $worksheetFeed->getByTitle('Servizi');

        $data = $worksheet->getCsv();
        $dataArray = str_getcsv($data, "\r\n");
        foreach ($dataArray as &$row) {
            $row = str_getcsv($row, ",");
        }

        array_shift($dataArray); # remove column header

        return count($dataArray);
    }
}
