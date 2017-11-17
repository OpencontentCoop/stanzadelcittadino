<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class CPSAuthenticatorTestAbstract
 *
 * @package Tests\AppBundle\Security
 */
class CPSAuthenticatorTest extends AbstractAppTestCase
{
    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function testIGetAnExceptionIfUserProviderIsNotACPSUserProvider()
    {
        $this->expectException(\InvalidArgumentException::class);

        $authenticator = $this->container->get('ocsdc.cps.token_authenticator');
        $wrongProvider = $mockLogger = $this->getMockBuilder(UserProviderInterface::class)->getMock();
        $authenticator->getUser([], $wrongProvider);
    }

    /**
     * @test
     */
    public function testOnAuthenticationFailureReturnsResponse()
    {
        $authenticator = $this->container->get('ocsdc.cps.token_authenticator');
        $response = $authenticator->onAuthenticationFailure(new Request(), new AuthenticationException('some'));
        $this->assertTrue($response instanceof Response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
