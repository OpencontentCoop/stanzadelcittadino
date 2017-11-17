<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="utente")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"operatore" = "OperatoreUser", "cps" = "CPSUser"})
 * @UniqueEntity(fields="usernameCanonical", errorPath="username", message="fos_user.username.already_used")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email", column=@ORM\Column(type="string", name="email", length=255, unique=false, nullable=true)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(type="string", name="email_canonical", length=255, unique=false, nullable=true))
 * })
 * @package AppBundle\Entity
 */
abstract class User extends BaseUser
{

    const ROLE_OPERATORE_ADMIN = 'ROLE_OPERATORE_ADMIN';
    const ROLE_OPERATORE = 'ROLE_OPERATORE';
    const ROLE_USER = 'ROLE_USER';


    const USER_TYPE_OPERATORE = 'operatore';
    const USER_TYPE_CPS = 'cps';

    const FAKE_EMAIL_DOMAIN = 'cps.didnt.have.my.email.tld';

    /**
     * @var string
     *
     * @ORM\Column(name="cognome", type="string")
     */
    private $cognome;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string")
     */
    private $nome;

    /**
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    protected $type;

    protected $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $emailContatto;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $cellulareContatto;

    /**
     * User constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->id = Uuid::uuid4();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return (string) $this->id;
    }

    public function hasPassword()
    {
        return $this->password !== null;
    }

    /**
     * @return string
     */
    public function getCognome()
    {
        return $this->cognome;
    }

    /**
     * @param $cognome
     *
     * @return User
     */
    public function setCognome($cognome)
    {
        $this->cognome = $cognome;

        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param $nome
     *
     * @return User
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->fullName == null){
            $this->fullName = $this->cognome . ' ' . $this->nome;
        }
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getEmailContatto()
    {
        return $this->emailContatto;
    }

    /**
     * @param string $emailContatto
     *
     * @return $this
     */
    public function setEmailContatto($emailContatto)
    {
        $this->emailContatto = $emailContatto;

        return $this;
    }

    /**
     * @return string
     */
    public function getCellulareContatto()
    {
        return $this->cellulareContatto;
    }

    /**
     * @param string $cellulareContatto
     *
     * @return $this
     */
    public function setCellulareContatto($cellulareContatto)
    {
        $this->cellulareContatto = $cellulareContatto;

        return $this;
    }


}
