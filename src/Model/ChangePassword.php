<?php

namespace App\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;


//This class holds necessary data when user submits change-password form
class ChangePassword
{

    /**
     * @var string
     *
     * Annotation UserPassword checks if the password matches current users password
     * @SecurityAssert\UserPassword()
     */
    protected $oldPassword;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    protected $newPassword;

    /**
     * @return string
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param $oldPassword
     * @return string
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param $newPassword
     * @return string
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

}