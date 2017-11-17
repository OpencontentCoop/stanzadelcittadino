<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\ScheduledAction;

use AppBundle\Entity\ScheduledAction;

interface ScheduledActionHandlerInterface
{
    public function executeScheduledAction(ScheduledAction $action);
}
