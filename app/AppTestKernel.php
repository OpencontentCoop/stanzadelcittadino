<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

require_once __DIR__.'/AppKernel.php';

/**
 * Class AppTestKernel
 */
class AppTestKernel extends AppKernel
{
    private $kernelModifier;

    public function boot()
    {
        parent::boot();
        if ($kernelModifier = $this->kernelModifier) {
            $kernelModifier($this);
//            $this->kernelModifier = null;
        };
    }

    public function setKernelModifier(\Closure $kernelModifier)
    {
        $this->kernelModifier = $kernelModifier;
        $this->shutdown();
    }
}
