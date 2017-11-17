<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Services;

/**
 * Class P7MSignatureCheckService
 */
class P7MSignatureCheckService
{
    /**
     * @param string $absolutePath
     * @return bool
     */
    public function check($absolutePath): bool
    {
        exec("openssl pkcs7 -inform DER -in $absolutePath -print_certs 2>&1", $output);
        $regex = '/(subject).*(serialNumber).*(issuer).*(BEGIN CERTIFICATE).*(END CERTIFICATE).*/';
        if (preg_match($regex, implode('', $output)) === 1) {
            return true;
        }

        return false;
    }

}
