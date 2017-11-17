<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AllegatoOperatore
 */
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class AllegatoOperatore extends Allegato
{

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Pratica", mappedBy="allegatiOperatore")
     * @var ArrayCollection $pratiche
     */
    private $pratiche3;

    /**
     * ModuloCompilato constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->type = 'allegato_operatore';
        $this->pratiche3 = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getPratiche(): Collection
    {
        return $this->pratiche3;
    }

    /**
     * @param Pratica $pratica
     * @return $this
     */
    public function addPratica(Pratica $pratica)
    {
        if (!$this->pratiche3->contains($pratica)) {
            $this->pratiche3->add($pratica);
        }
        return $this;
    }

    /**
     * @param Pratica $pratica
     * @return $this
     */
    public function removePratica(Pratica $pratica)
    {
        if ($this->pratiche3->contains($pratica)) {
            $this->pratiche3->removeElement($pratica);
        }
        return $this;
    }

}
