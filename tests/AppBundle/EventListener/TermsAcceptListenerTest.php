<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Test\AppBundle\EventListener;

use AppBundle\EventListener\TermsAcceptListener;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tests\AppBundle\Base\AbstractAppTestCase;

/**
 * Class TermsAcceptListenerTest
 */
class TermsAcceptListenerTest extends AbstractAppTestCase
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
    public function testListenerIsProperlySetUp()
    {
        $listeners = $this->container->get('event_dispatcher')->getListeners('kernel.request');
        $found = false;
        foreach ($listeners as $listener) {
            if ($listener[0] instanceof TermsAcceptListener) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @test
     */
    public function testListenerRedirectsToTermsAcceptIfUserNeveracceptedTerms()
    {
        $cpsUser = $this->createCPSUser(false);

        $mockedToken = $this->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mockedToken->expects($this->any())
            ->method('getUser')
            ->willReturn($cpsUser);


        $mockedTokenStorage = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($mockedToken);

        $mockedEvent = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockedRequest->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['_route', '_route_params'],
                ['default', []],
            ]));
        $mockedRequest->query = new ParameterBag();

        $mockedEvent->expects($this->any())
            ->method('getRequest')
            ->willReturn($mockedRequest);

        $mockedEvent->expects($this->once())
            ->method('setResponse')
            ->with($this->callback(function ($arg) {
                $this->assertInstanceOf(RedirectResponse::class, $arg);

                return true;
            }));
        $this->createDefaultTerm(true);
        $listener = new TermsAcceptListener($this->router, $mockedTokenStorage, $this->container->get('ocsdc.cps.terms_acceptance_checker'));
        $listener->onKernelRequest($mockedEvent);
    }


    /**
     * @test
     */
    public function testListenerRedirectsToTermsAcceptIfUserDidntAcceptLatestTerms()
    {
        $cpsUser = $this->createCPSUser(false);

        $mockedToken = $this->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mockedToken->expects($this->any())
            ->method('getUser')
            ->willReturn($cpsUser);


        $mockedTokenStorage = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($mockedToken);

        $mockedEvent = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockedRequest->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['_route', '_route_params'],
                ['default', []],
            ]));
        $mockedRequest->query = new ParameterBag();

        $mockedEvent->expects($this->any())
            ->method('getRequest')
            ->willReturn($mockedRequest);

        $mockedEvent->expects($this->once())
            ->method('setResponse')
            ->with($this->callback(function ($arg) {
                $this->assertInstanceOf(RedirectResponse::class, $arg);

                return true;
            }));

        $this->createDefaultTerm(true);
        $listener = new TermsAcceptListener($this->router, $mockedTokenStorage, $this->container->get('ocsdc.cps.terms_acceptance_checker'));
        $listener->onKernelRequest($mockedEvent);
    }
}
