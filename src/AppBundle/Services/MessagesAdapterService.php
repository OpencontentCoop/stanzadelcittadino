<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\CPSUser;
use AppBundle\Entity\Ente;
use AppBundle\Entity\OperatoreUser;
use AppBundle\Entity\Servizio;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class MessagesAdapterService
 */
class MessagesAdapterService
{
    const REMOTE_ENDPOINT_UNAVAILABLE_EXCEPTION_MESSAGE = 'Remote messages endpoint is unavailable';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MessagesAdapterService constructor.
     * @param Client          $client
     * @param LoggerInterface $logger
     */
    public function __construct(Client $client, LoggerInterface $logger, RegistryInterface $doctrine)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $user
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getDecoratedThreadsForUser(User $user)
    {
        try {
            $response = \GuzzleHttp\json_decode((string) $this->client->get('/user/'.$user->getId().'/threads')->getBody());

            $repo = null;
            switch (get_class($user)) {
                case CPSUser::class :
                    $repo = $this->doctrine->getRepository('AppBundle:OperatoreUser');
                    $response = $this->decorateThreadsForUser($response, $repo);
                    break;
                case OperatoreUser::class :
                    $repo = $this->doctrine->getRepository('AppBundle:CPSUser');
                    $response = $this->decorateThreadsForOperatore($response, $repo);
                    break;
            }

            return $response;
        } catch (RequestException $e) {
            $this->logger->error(self::REMOTE_ENDPOINT_UNAVAILABLE_EXCEPTION_MESSAGE);

            return null;
        }
    }

    /**
     * @param UserInterface $sender
     * @param string        $content
     * @param string        $threadId
     * @return mixed
     */
    public function postMessageToThread(UserInterface $sender, $content, $threadId)
    {
        $message = [
            'senderId' => $sender->getId(),
            'content' => $content,
            'threadId' => $threadId,
        ];
        try {
            $response = \GuzzleHttp\json_decode((string) $this->client->put('/thread/'.$threadId, ['json' => $message])->getBody());
            return $this->decorateMessages($response, $sender);
        } catch (RequestException $e) {
            $this->logger->error(self::REMOTE_ENDPOINT_UNAVAILABLE_EXCEPTION_MESSAGE);

            return null;
        }
    }

    /**
     * @param string $threadId
     * @return array
     */
    public function getDecoratedMessagesForThread($threadId, User $user)
    {
        try {
            $response = \GuzzleHttp\json_decode((string) $this->client->get('/thread/'.$threadId)->getBody());
            return $this->decorateMessages($response, $user);
        } catch (RequestException $e) {
            $this->logger->error(self::REMOTE_ENDPOINT_UNAVAILABLE_EXCEPTION_MESSAGE);

            return null;
        }
    }

    /**
     * @param CPSUser  $user
     * @param Ente     $ente
     * @param Servizio $servizio
     * @return mixed
     */
    public function getThreadsForUserEnteAndService(CPSUser $user, Ente $ente, Servizio $servizio)
    {
        //find operatore
        $operatore = $this->getOperatoreForEnteAndServizio($ente, $servizio);
        if (!$operatore) {
            return null;
        }
        $threadId = $user->getId().'~'.$operatore->getId();
        try {
            $response = \GuzzleHttp\json_decode((string)$this->client->get('/thread/'.$threadId)->getBody());
            if (count($response) > 0 && $this->checkThreadIdIsCorrect($response[0])) {
                $repo = $this->doctrine->getRepository('AppBundle:OperatoreUser');
                $response = $this->decorateThreadsForUser($response, $repo);
                return $response;
            }
        } catch (RequestException $e) {
            $this->logger->error(self::REMOTE_ENDPOINT_UNAVAILABLE_EXCEPTION_MESSAGE);
            return null;
        }
        return $this->createThreadsForUserEnteAndService($user, $ente, $servizio);
    }

    /**
     * @param CPSUser  $user
     * @param Ente     $ente
     * @param Servizio $servizio
     * @return mixed
     */
    public function createThreadsForUserEnteAndService(CPSUser $user, Ente $ente, Servizio $servizio)
    {
        //find operatore
        $operatore = $this->getOperatoreForEnteAndServizio($ente, $servizio);
        $threadId = $user->getId().'~'.$operatore->getId();
        try {
            $response =  \GuzzleHttp\json_decode((string)$this->client->put('/thread', ['json' => ['threadId' => $threadId, 'servizioId' => $servizio->getId()]])->getBody());
            $repo = $this->doctrine->getRepository('AppBundle:OperatoreUser');
            $response = $this->decorateThreadsForUser($response, $repo);
            return $response;
        } catch (RequestException $e) {
            $this->logger->error(self::REMOTE_ENDPOINT_UNAVAILABLE_EXCEPTION_MESSAGE);
            return null;
        }
    }

    private function getOperatoreForEnteAndServizio(Ente $ente, Servizio $servizio)
    {
        $operatori = $ente->getOperatori();
        foreach ($operatori as $operatore) {
            if ($operatore->getServiziAbilitati()->contains($servizio->getId())) {
                return $operatore;
            }
        }
    }

    private function checkThreadIdIsCorrect($t)
    {
        $splitted = preg_split('/~/', $t->threadId);
        foreach ($splitted as $id) {
            if (!preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $id)) {
                return false;
            };
        }

        return true;
    }

    private function decorateThreadsForUser($threads, $repo)
    {
        foreach ($threads as &$thread) {
            $operatoreId = preg_split('/~/', $thread->threadId)[1];
            $operatore = $repo->find($operatoreId);

            $thread->title = $operatore->getEnte()->getName().' ('.$operatore->getFullName().')';
        }

        return $threads;
    }

    private function decorateThreadsForOperatore($threads, $repo)
    {
        foreach ($threads as &$thread) {
            $userId = preg_split('/~/', $thread->threadId)[0];
            $cpsUser = $repo->find($userId);

            $thread->title = $cpsUser->getFullName();
        }

        return $threads;
    }

    private function decorateMessages($undecoratedResponse, User $user)
    {
        foreach ($undecoratedResponse as &$message) {
            $actualTimestamp = strlen((string) $message->timestamp) > 10 ? $message->timestamp / 1000 : $message->timestamp;
            $message->formattedDate = strftime("%e %b %Y %H:%M", $actualTimestamp);
            $message->isMine = false;
            if ($message->senderId == $user->getId()) {
                $message->isMine = true;
            }
        }
        return $undecoratedResponse;
    }

    private function decorateThreads($undecoratedResponse)
    {
        foreach ($undecoratedResponse as &$thread) {
            $thread->nomeThread = 'Servizio';
            $operatoriRepo = $this->getDoctrine()->getRepository('AppBundle:OperatoreUser');
            $operatoreId = preg_split('/~/', $thread->threadId)[1];
            $operatore = $operatoriRepo->find($operatoreId);
            if ($operatore) {
                $thread->nomeThread = $operatore->getEnte()->getName() . ' (' . $operatore->getNome() . ' ' . $operatore->getCognome() . ')';
            }
        }
        return $undecoratedResponse;
    }
}
