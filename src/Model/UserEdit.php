<?php

namespace App\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

//This class holds necessary data when user submits user_edit form
class UserEdit
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $mail;

    /**
     * @var string
     *
     * Annotation UserPassword checks if the password matches current users password
     * @SecurityAssert\UserPassword()
     */
    protected $currentPassword;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;
    }



}