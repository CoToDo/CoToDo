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

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString()
    {
       return $this->getTeam().$this->getType().$this->getUser();
    }

    public function isRoleAdmin() {
        return Constants::ADMIN == $this->getType();
    }

    public function isRoleLeader() {
        return Constants::LEADER == $this->getType();
    }

    public function isRoleUser() {
        return Constants::USER == $this->getType();
    }

}
