<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Twig;

use AppBundle\Entity\Pratica;
use Carbon\Carbon;

class IterCalculatorExtension extends \Twig_Extension
{

    private $durationStartStatus;

    private $durationEndStatuses;

    public function __construct($durationStartStatus, $durationEndStatuses)
    {
        $this->durationStartStatus = $durationStartStatus;
        $this->durationEndStatuses = (array)$durationEndStatuses;
    }

    public function getName()
    {
        return 'iter_calculator_extension';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('iter_duration', array($this, 'getIterDuration')),
        );
    }

    /**
     * @param $pratica
     * @param null $locale
     *
     * @return string
     */
    public function getIterDuration($pratica, $locale = null)
    {
        if ($pratica instanceof Pratica) {
            $history = $pratica->getStoricoStati()->toArray();
            ksort($history);
            $startTimestamp = null;
            $endTimestamp = time();
            foreach ($history as $timestamp => $statuses) {
                foreach ($statuses as $status) {
                    if ($status[0] === $this->durationStartStatus) {
                        $startTimestamp = $timestamp;
                        break;
                    } elseif (in_array($status[0], $this->durationEndStatuses, true)) {
                        $endTimestamp = $timestamp;
                        break;
                    }
                }
            }

            if ($locale) {
                Carbon::setLocale($locale);
            }

            if ($startTimestamp > 0) {
                $start = Carbon::instance(\DateTime::createFromFormat('U', $startTimestamp));
                $end = Carbon::instance(\DateTime::createFromFormat('U', $endTimestamp));

                return $start->diffForHumans($end, true);
            }
        }

        return '';
    }
}
