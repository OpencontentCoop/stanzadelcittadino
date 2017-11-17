<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Interface AllegatoInterface
 */
interface AllegatoInterface
{
    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return AllegatoInterface
     */
    public function setFile(File $file = null) : AllegatoInterface;

    /**
     * @return File
     */
    public function getFile();

    /**
     * @return string
     */
    public function getId() : string;

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param string $filename
     * @return AllegatoInterface
     */
    public function setFilename($filename) : AllegatoInterface;

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return AllegatoInterface
     */
    public function setDescription($description) : AllegatoInterface;

    /**
     * @return ArrayCollection
     */
    public function getPratiche() : Collection;

    /**
     * @param Pratica $pratica
     * @return $this
     */
    public function addPratica(Pratica $pratica);

    /**
     * @param Pratica $pratica
     * @return $this
     */
    public function removePratica(Pratica $pratica);

    /**
     * @return CPSUser
     */
    public function getOwner() : CPSUser;

    /**
     * @param $owner
     * @return $this
     */
    public function setOwner(CPSUser $owner);

    /**
     * @return string
     */
    public function getOriginalFilename();

    /**
     * @param string $originalFilename
     * @return $this
     */
    public function setOriginalFilename($originalFilename);

    /**
     * @return string
     */
    public function getChoiceLabel() : string;

    /**
     * @return string
     */
    public function getType() : string;

    /**
     * @param string $type
     * @return AllegatoInterface
     */
    public function setType($type);
}
