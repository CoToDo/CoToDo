<?php

namespace App\Entity;

use App\Constants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="team", cascade={"remove"})
     * @ORM\JoinColumn(name="project_id", referencedColumnName="project_id", onDelete="CASCADE")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Role", mappedBy="team", cascade={"remove"})
     * @ORM\JoinColumn(name="role_id", referencedColumnName="role_id", onDelete="CASCADE")
     */
    private $roles;

    /**
     * Team constructor.
     */
    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Team
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /**
     * @param Project $project
     * @return Team
     */
    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setTeam($this);
        }

        return $this;
    }

    /**
     * @param Project $project
     * @return Team
     */
    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getTeam() === $this) {
                $project->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     * @return Team
     */
    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->setTeam($this);
        }

        return $this;
    }

    /**
     * @param Role $role
     * @return Team
     */
    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            // set the owning side to null (unless already changed)
            if ($role->getTeam() === $this) {
                $role->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->getName();
    }


    /**
     * Check if user is member of a team
     *
     * @param $user
     * @return bool
     */
    public function isMember($user)
    {
        foreach ($user->getUserRoles() as $role) {
            if ($this->getId() == $role->getTeam()->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $user
     * @return null
     */
    public function getMemberRole($user)
    {
        foreach ($user->getUserRoles() as $role) {
            if ($this->getId() == $role->getTeam()->getId()) {
                return $role->getType();
            }
        }
        return null;
    }

    /**
     * Check if user is just leader
     *
     * @param $user
     * @return bool
     */
    public function isLeader($user)
    {
        foreach ($this->getRoles() as $role) {
            if ($user->getId() == $role->getUser()->getId() && $role->getType() == Constants::LEADER) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is admin or leader
     *
     * @param $user
     * @return bool
     */
    public function isAdmin($user)
    {
        foreach ($this->getRoles() as $role) {
            if ($user->getId() == $role->getUser()->getId() && ($role->getType() == Constants::ADMIN || $role->getType() == Constants::LEADER)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is just admin
     *
     * @param $user
     * @return bool
     */
    public function isOnlyAdmin($user)
    {
        foreach ($this->getRoles() as $role) {
            if ($user->getId() == $role->getUser()->getId() && $role->getType() == Constants::ADMIN) {
                return true;
            }
        }
        return false;
    }

}
