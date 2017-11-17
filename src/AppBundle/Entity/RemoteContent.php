<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

class RemoteContent implements \JsonSerializable
{
    private $siteName;

    private $siteUrl;

    private $title;

    private $summary;

    private $link;

    /**
     * @var \DateTime
     */
    private $date;

    private $source;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @param mixed $siteName
     *
     * @return RemoteContent
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * @param mixed $siteUrl
     *
     * @return RemoteContent
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return RemoteContent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     *
     * @return RemoteContent
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     *
     * @return RemoteContent
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return RemoteContent
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     *
     * @return RemoteContent
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'source' => $this->source,
            'site_name' => $this->siteName,
            'site_url' => $this->siteUrl,
            'title' => $this->title,
            'summary' => $this->summary,
            'link' => $this->link,
            'date' => $this->date,
            'day' => $this->date->format('d'),
            'month' => $this->date->format('M'),
            'year' => $this->date->format('Y'),
        ];
    }

}
