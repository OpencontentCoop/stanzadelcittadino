<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Protocollo;

use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\HttpFoundation\ParameterBag;

class PiTreProtocolloParameters extends ParameterBag
{
    public function __construct(array $parameters = array())
    {
        parent::__construct($parameters);
    }

    public static function getEnteParametersKeys()
    {
        return array(
            'recipientIDArray',
            'recipientTypeIDArray',
            'codeNodeClassification',
            'codeAdm',
            'trasmissionIDArray'
        );
    }

    /**
     * @return string
     */
    public function getCodeAdm()
    {
        return $this->get('codeAdm');
    }

    /**
     * @param string $codeAdm
     */
    public function setCodeAdm($codeAdm)
    {
        $this->set('codeAdm', $codeAdm );
    }

    /**
     * @return string
     */
    public function getRecipientIDArray()
    {
        return $this->get('recipientIDArray');
    }

    /**
     * @param string $recipientIDArray
     */
    public function setRecipientIdArray( $recipientIDArray )
    {
        $this->set('recipientIDArray', $recipientIDArray );
    }

    /**
     * @param string $recipientID
     */
    // FIXME: il wrapper da errore se passo un array, verificare con Francesco
    public function addRecipientId( $recipientID )
    {
        $recipientIDArray = array();
        if ($this->has('recipientIDArray'))
        {
            $recipientIDArray = $this->getRecipientIDArray();
        }
        $recipientIDArray []= $recipientID;
        $this->set('recipientIDArray', $recipientIDArray );
    }

    /**
     * @return string
     */
    public function getRecipientTypeIDArray()
    {
        return $this->get('recipientTypeIDArray');
    }

    /**
     * @param string $recipientIdType
     */
    public function setRecipientTypeIDArray($recipientTypeIDArray)
    {
        $this->set('recipientTypeIDArray', $recipientTypeIDArray);
    }

    /**
     * @param string $recipientID
     */
    // FIXME: il wrapper da errore se passo un array, verificare con Francesco
    public function addRecipientTypeID( $recipientTypeID )
    {
        $recipientTypeIDArray = array();
        if ($this->has('recipientTypeIDArray'))
        {
            $recipientTypeIDArray = $this->getrecipientTypeIDArray();
        }
        $recipientTypeIDArray []= $recipientTypeID;
        $this->set('recipientTypeIDArray', $recipientTypeIDArray );
    }

    /**
     * @return string
     */
    public function getTrasmissionIDArray()
    {
        return $this->get('trasmissionIDArray');
    }

    /**
     * @param string $recipientIdType
     */
    public function setTrasmissionIDArray($trasmissionIDArray)
    {
        $this->set('trasmissionIDArray', $trasmissionIDArray);
    }

    /**
     * @return string
     */
    public function getCodeNodeClassification()
    {
        return $this->get('codeNodeClassification');
    }

    /**
     * @param string $codeNodeClassification
     */
    public function setCodeNodeClassification($codeNodeClassification)
    {
        $this->set('codeNodeClassification', $codeNodeClassification);
    }

    public function setFilePath($filePath)
    {
        $this->set('filePath', $filePath);
    }

    public function setProjectDescription($projectDescription)
    {
        $this->set('projectDescription', $projectDescription);
    }

    public function setDocumentDescription($documentDescription)
    {
        $this->set('documentDescription', $documentDescription);
    }

    public function setDocumentObj($documentObj)
    {
        $this->set('documentObj', $documentObj);
    }

    public function setDocumentId($documentId)
    {
        $this->set('documentId', $documentId);
    }

    public function setAttachmentDescription($attachmentDescription)
    {
        $this->set('attachmentDescription', $attachmentDescription);
    }

    /**
     * @param $idProject
     * idProject valorizzato fa in modo che il documento venga inserito in quel preciso fascicolo
     */
    public function setIdProject($idProject)
    {
        $this->set('idProject', $idProject);
    }

    /**
     * @param $createProject
     */
    public function setCreateProject($createProject)
    {
        $this->set('createProject', $createProject);
    }

}
