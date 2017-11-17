<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;


use AppBundle\Entity\RemoteContent;

interface RemoteContentProviderServiceInterface
{
    /**
     * @param array $enti
     *
     * @return RemoteContent[]
     */
    public function getLatestNews(array $enti);

    /**
     * @param array $enti
     *
     * @return RemoteContent[]
     */
    public function getLatestDeadlines(array $enti);
}
