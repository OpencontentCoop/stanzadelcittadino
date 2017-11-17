<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Services;

use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class P7MSignatureCheckServiceTest
 */
class P7MSignatureCheckServiceTest extends AbstractAppTestCase
{

    const INVALID_FILE = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lenovo-yoga-xp1.pdf';
    const VALID_FILE = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'AttoFirmatoDiProva.pdf.p7m';


    /**
     * @test
     */
    public function testItExists()
    {
        $this->assertNotNull($this->container->get('ocsdc.p7m_signature_check'));
    }

    public function testItReturnsTrueIfCheckingAValidFile() {
        $service = $this->container->get('ocsdc.p7m_signature_check');
        $this->assertFalse($service->check(self::VALID_FILE));
    }

    public function testItReturnsFalseIfCheckingAnInvalidFile() {
        $service = $this->container->get('ocsdc.p7m_signature_check');
        $this->assertFalse($service->check(self::INVALID_FILE));
    }

}
