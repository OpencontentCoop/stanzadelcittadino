<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form;

use Craue\FormFlowBundle\Storage\StorageKeyGeneratorInterface;

/**
 * Class UserSessionStorageKeyGenerator
 */
class UserSessionStorageKeyGenerator implements StorageKeyGeneratorInterface
{
    /**
     * @param string $key
     * @return string
     */
    public function generate($key)
    {
        return $key;
    }
}
