<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Command\ServizioCreateCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class ServizioCreateCommandTest
 */
class ServizioCreateCommandTest extends AbstractAppTestCase
{
    public function testExecute()
    {
        $application = new Application(self::$kernel);

        $application->add(new ServizioCreateCommand());

        $command = $application->find('ocsdc:crea-servizio');
        $commandTester = new CommandTester($command);

        $slug = 'test_servizio';
        $name = 'Test Servizio';
        $commandTester->execute(array(
            'command' => $command->getName(),
            'slug' => $slug,
            'name' => $name,
            'fcqn' => '\AppBundle\Entity\Test',
            'flow' => 'ocsdc.form.flow.test',
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('Slug: '.$slug, $output);
    }
}
