<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Form;

use Craue\FormFlowBundle\Form\FormFlowInterface;
use Craue\FormFlowBundle\Storage\DataManager as BaseDataManager;
use Craue\FormFlowBundle\Storage\SerializableFile;
use Craue\FormFlowBundle\Storage\StorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Manages data of flows and their steps.
 *
 * It uses the following data structure with {@link $this->user->getId()} as name of the root element within the storage:
 * <code>
 *    $this->user->getId() => array(
 *        name of the flow => array(
 *            instance id of the flow => array(
 *                'data' => array() // the actual step data
 *            )
 *        )
 *    )
 * </code>
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class DataManager extends BaseDataManager
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * DataManager constructor.
     * @param StorageInterface      $storage
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(StorageInterface $storage, TokenStorageInterface $tokenStorage)
    {
        parent::__construct($storage);
        $this->tokenStorage = $tokenStorage;
        $this->user = $this->tokenStorage->getToken()->getuser();
    }

    /**
     * {@inheritDoc}
     */
    public function save(FormFlowInterface $flow, array $data)
    {
        // handle file uploads
        if ($flow->isHandleFileUploads()) {
            array_walk_recursive($data, function (&$value, $key) {
                if (SerializableFile::isSupported($value)) {
                    $value = new SerializableFile($value);
                }
            });
        }

        // drop old data
        $this->drop($flow);

        // save new data
        $savedFlows = $this->getStorage()->get($this->user->getId(), array());

        $savedFlows = array_merge_recursive($savedFlows, array(
            $flow->getName() => array(
                $flow->getInstanceId() => array(
                    self::DATA_KEY => $data,
                ),
            ),
        ));

        $this->getStorage()->set($this->user->getId(), $savedFlows);
    }

    /**
     * {@inheritDoc}
     */
    public function drop(FormFlowInterface $flow)
    {
        $savedFlows = $this->getStorage()->get($this->user->getId(), array());

        // remove data for only this flow instance
        if (isset($savedFlows[$flow->getName()][$flow->getInstanceId()])) {
            unset($savedFlows[$flow->getName()][$flow->getInstanceId()]);
        }

        $this->getStorage()->set($this->user->getId(), $savedFlows);
    }

    /**
     * {@inheritDoc}
     */
    public function load(FormFlowInterface $flow)
    {
        $data = array();

        // try to find data for the given flow
        $savedFlows = $this->getStorage()->get($this->user->getId(), array());
        if (isset($savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY])) {
            $data = $savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY];
        }

        // handle file uploads
        if ($flow->isHandleFileUploads()) {
            $tempDir = $flow->getHandleFileUploadsTempDir();
            array_walk_recursive($data, function (&$value, $key) use ($tempDir) {
                if ($value instanceof SerializableFile) {
                    $value = $value->getAsFile($tempDir);
                }
            });
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(FormFlowInterface $flow)
    {
        $savedFlows = $this->getStorage()->get($this->user->getId(), array());

        return isset($savedFlows[$flow->getName()][$flow->getInstanceId()][self::DATA_KEY]);
    }

    /**
     * {@inheritDoc}
     */
    public function listFlows()
    {
        return array_keys($this->getStorage()->get($this->user->getId(), array()));
    }

    /**
     * {@inheritDoc}
     */
    public function listInstances($name)
    {
        $savedFlows = $this->getStorage()->get($this->user->getId(), array());

        if (array_key_exists($name, $savedFlows)) {
            return array_keys($savedFlows[$name]);
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function dropAll()
    {
        $this->getStorage()->remove($this->user->getId());
    }
}
