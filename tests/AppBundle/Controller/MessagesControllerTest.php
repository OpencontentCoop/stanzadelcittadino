<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Controller\MessagesController;
use AppBundle\Entity\Allegato;
use AppBundle\Entity\ComponenteNucleoFamiliare;
use AppBundle\Entity\Ente;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Pratica;
use AppBundle\Entity\User;
use AppBundle\Services\MessagesAdapterService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\AppBundle\Base\AbstractAppTestCase;

class MessagesControllerTest extends AbstractAppTestCase
{

    /**
     * Setup
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
        $this->cleanDb(Allegato::class);
        $this->cleanDb(User::class);
        $this->cleanDb(Ente::class);
    }

    public function testItExists()
    {
        $this->assertNotNull(new MessagesController());
    }

    public function testItAllowsCPSUserToSendMessageToOperatoreInAnExistingThread()
    {
        $cpsUser = $this->createCPSUser(true, true);

        $mockedMessagesService = $this->getMockBuilder(MessagesAdapterService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $threadsForUser = json_decode((string) $this->getMockedMessagesBackendThreadResponseForUser($cpsUser)->getBody());

        $mockedMessagesService->expects($this->exactly(1))
            ->method('getDecoratedThreadsForUser')
            ->willReturn($threadsForUser);

        $expectedMessage = new \stdClass();
        $expectedMessage->messageId = Uuid::uuid4().'';
        $expectedMessage->timestamp = time();
        $expectedMessage->senderId = $cpsUser->getId();
        $expectedMessageReturn = [ $expectedMessage ];

        $mockedMessagesService->expects($this->once())
            ->method('postMessageToThread')
            ->willReturn($expectedMessageReturn);

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockedMessagesService) {
            $kernel->getContainer()->set('ocsdc.messages_adapter', $mockedMessagesService);
        });

        $crawler = $this->clientRequestAsCPSUser($cpsUser, 'GET', '/user/');
        $forms = $crawler->filterXPath('//form[@name="message"]');
        $this->assertEquals(count($threadsForUser), $forms->count());

        //Crawler returns the form for the first matched element
        $form = $crawler->filterXPath('//form[@name="message"]')->form();
        $values = $form->getValues();
        //sender and thread id come from the context
        $this->assertEquals($values['message[sender_id]'], $cpsUser->getId());

        $values['message[message]'] = "From PHP with love";
        $form->setValues($values);
        $this->submitAsCPSUser($cpsUser, $form);
        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertEquals($expectedMessageReturn, $response);
    }

    /**
     * @test
     */
    public function testThreadHistoryGetsRetrievedCorrectly()
    {
        $cpsUser = $this->createCPSUser(true, true);

        $mockedMessagesService = $this->getMockBuilder(MessagesAdapterService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $threadId = $cpsUser->getId().'~'.Uuid::uuid4();
        $messagesForThread = $this->getDecoratedMessagesResponseForThread($threadId);

        $mockedMessagesService->expects($this->once())
            ->method('getDecoratedMessagesForThread')
            ->willReturn($messagesForThread);

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockedMessagesService) {
            $kernel->getContainer()->set('ocsdc.messages_adapter', $mockedMessagesService);
        });

        $this->clientRequestAsCPSUser($cpsUser, 'GET', '/user/messages/'.$threadId);
        $response = json_decode($this->client->getResponse()->getContent());

        foreach ($response as $message) {
            $this->assertObjectHasAttribute('isMine', $message);
            $this->assertObjectHasAttribute('formattedDate', $message);
        }

        $this->assertEquals(count($messagesForThread), count($response));
    }

    /**
     * @test
     */
    public function testMessageControllerRetrievesThreadsForUser()
    {
        $cpsUser = $this->createCPSUser(true, true);

        $mockedMessagesService = $this->getMockBuilder(MessagesAdapterService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $threadsForUser = (string) $this->getDecoratedThreadResponseForUser($cpsUser)->getBody();

        $mockedMessagesService->expects($this->exactly(1))
            ->method('getDecoratedThreadsForUser')
            ->willReturn(json_decode($threadsForUser));

        static::$kernel->setKernelModifier(function (KernelInterface $kernel) use ($mockedMessagesService) {
            $kernel->getContainer()->set('ocsdc.messages_adapter', $mockedMessagesService);
        });

        $this->clientRequestAsCPSUser($cpsUser, 'GET', '/user/threads');
        $response = json_decode($this->client->getResponse()->getContent());

        $threadsForUser = json_decode($threadsForUser);

        foreach ($response as $k => $v) {
            $this->assertEquals($threadsForUser[$k]->threadId, $v->threadId);
            $this->assertEquals($threadsForUser[$k]->title, $v->title);
            /**
             * FIXME: @coppo
             * qua non ho capito come intendi comporre il nome thread quindi ho lasciato
             * un check molto generico. Questo punto va testato
             */
            $this->assertObjectHasAttribute('nomeThread', $v);
        }
    }

    private function getDecoratedMessagesResponseForThread($threadId)
    {
        $userId = explode('~', $threadId)[0];
        $operatoreId = explode('~', $threadId)[1];
        $message1 = new \stdClass();
        $message1->messageId = Uuid::uuid4();
        $message1->senderId = $userId;
        $message1->content = 'pippo';
        $message1->timestamp = time() - 1000;
        $message1->isMine = true;
        $message1->formattedDate = '14/12/2106 10:14';

        $message2 = new \stdClass();
        $message2->messageId = Uuid::uuid4();
        $message2->senderId = $operatoreId;
        $message2->content = 'pippo';
        $message2->timestamp = time() - 500;
        $message2->isMine = false;
        $message2->formattedDate = '14/12/2106 11:14';
        return [
            $message1,
            $message2,
        ];
    }
}
