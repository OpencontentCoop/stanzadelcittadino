<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

use AppBundle\Entity\Allegato;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

/**
 * Class CPSAllegatiDirectoryNamer
 */
class DirectoryNamerService implements DirectoryNamerInterface
{
    /**
     * @param object          $object
     * @param PropertyMapping $mapping
     * @return string
     */
    public function directoryName($object, PropertyMapping $mapping):string
    {
        if ($object instanceof Allegato) {
            return $object->getOwner()->getId();
        }

        return 'misc';
    }
}
