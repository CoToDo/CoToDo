<?php

namespace App\Entity;

use App\Constants;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoleRepository")
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="roles")
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="roles")
     */
    private $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Role
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @param Team|null $team
     * @return Role
     */
    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return Role
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return $this->getTeam().$this->getType().$this->getUser();
    }

    /**
     * @return bool
     */
    public function isRoleAdmin() {
        return Constants::ADMIN == $this->getType();
    }

    /**
     * @return bool
     */
    public function isRoleLeader() {
        return Constants::LEADER == $this->getType();
    }

    /**
     * @return bool
     */
    public function isRoleUser() {
        return Constants::USER == $this->getType();
    }

}
