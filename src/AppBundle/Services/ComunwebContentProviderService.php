<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\Ente;
use AppBundle\Entity\RemoteContent;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Psr\Log\LoggerInterface;

class ComunwebContentProviderService implements RemoteContentProviderServiceInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $apiLanguage = 'ita-IT';

    private $searchEndpointBaseUri = '/api/opendata/v2/content/search/';

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param Ente[] $enti
     *
     * @return array
     */
    public function getLatestNews(array $enti)
    {
        $query = "classes 'avviso' sort [published=>desc] limit 5";
        return $this->getRemoteContents($enti, $query);
    }

    /**
     * @param Ente[] $enti
     *
     * @return array
     */
    public function getLatestDeadlines(array $enti)
    {
        $query = "scadenza range [yesterday,next month] sort [scadenza=>asc] limit 15";
        return $this->getRemoteContents($enti, $query);
    }


    private function buildUri($siteUrl, $query)
    {
        return Psr7\Uri::resolve(Psr7\uri_for($siteUrl), $this->searchEndpointBaseUri . $query);
    }

    /**
     * @param Ente[] $enti
     * @param string $query
     *
     * @return RemoteContent[]
     */
    private function getRemoteContents(array $enti, $query)
    {
        $sortableData = [];
        foreach ($enti as $ente) {
            $response = $this->getRemoteResponse($ente, $query);
            if ($response->getStatusCode() == 200) {
                $body = json_decode((string)$response->getBody(), true);
                if (isset($body['totalCount']) && $body['totalCount'] > 0) {
                    foreach ($body['searchHits'] as $searchHit) {
                        $timestamp = $this->getItemTimestamp($searchHit);
                        $remoteContent =  $this->createRemoteContent($searchHit, $ente->getSiteUrl(), $ente->getName());
                        $sortableData[$timestamp][] = $remoteContent;
                    }
                }
            }
        }
        $data = [];
        krsort($sortableData);
        foreach ($sortableData as $key => $news) {
            $data = array_merge($data, $news);
        }

        return $data;
    }

    /**
     * @param Ente $ente
     * @param string $query
     *
     * @return Psr7\Response|\Psr\Http\Message\ResponseInterface
     */
    private function getRemoteResponse(Ente $ente, $query)
    {
        $response = new Psr7\Response('404');
        $siteUrl = $ente->getSiteUrl();
        if ($siteUrl) {
            try {
                $response = $this->client->get($this->buildUri($siteUrl, $query));
            }catch(\Exception $e){
                $this->logger->error($e->getMessage());
            }
        }
        return $response;
    }

    private function getItemTimestamp(array $item)
    {
        $dateTime = new \DateTime($item['metadata']['published']);

        return $dateTime->getTimestamp();
    }

    private function createRemoteContent(array $item, $siteUrl, $siteName)
    {
        $link = Psr7\Uri::resolve(Psr7\uri_for($siteUrl), '/content/view/full/' . $item['metadata']['mainNodeId']);

        $dateField = $item['metadata']['published'];
        if (isset($item['data'][$this->apiLanguage]['scadenza'])) {
            $dateField = $item['data'][$this->apiLanguage]['scadenza'];
        }
        $dateTime = new \DateTime($dateField);
        $summary = '';
        if (isset($item['data'][$this->apiLanguage]['abstract'])) {
            $summary = $item['data'][$this->apiLanguage]['abstract'];
        }

        return (new RemoteContent())
            ->setSiteName($siteName)
            ->setSiteUrl($siteUrl)
            ->setTitle($item['metadata']['name'][$this->apiLanguage])
            ->setSummary(strip_tags($summary))
            ->setLink((string)$link)
            ->setDate($dateTime)
            ->setSource($item['metadata']['link']);
    }
}
