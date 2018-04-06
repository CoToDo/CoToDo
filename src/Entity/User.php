<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $hash;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="user")
     */
    private $teams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="leader")
     */
    private $leaderTeams;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Work", mappedBy="user")
     */
    private $works;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="leader")
     */
    private $leaderProjects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="managers")
     */
    private $manageProjects;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->leaderTeams = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->works = new ArrayCollection();
        $this->leaderProjects = new ArrayCollection();
        $this->manageProjects = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setUser($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            // set the owning side to null (unless already changed)
            if ($team->getUser() === $this) {
                $team->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getLeaderTeams(): Collection
    {
        return $this->leaderTeams;
    }

    public function addLeaderTeam(Team $leaderTeam): self
    {
        if (!$this->leaderTeams->contains($leaderTeam)) {
            $this->leaderTeams[] = $leaderTeam;
            $leaderTeam->setLeader($this);
        }

        return $this;
    }

    public function removeLeaderTeam(Team $leaderTeam): self
    {
        if ($this->leaderTeams->contains($leaderTeam)) {
            $this->leaderTeams->removeElement($leaderTeam);
            // set the owning side to null (unless already changed)
            if ($leaderTeam->getLeader() === $this) {
                $leaderTeam->setLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Work[]
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works[] = $work;
            $work->setUser($this);
        }

        return $this;
    }

    public function removeWork(Work $work): self
    {
        if ($this->works->contains($work)) {
            $this->works->removeElement($work);
            // set the owning side to null (unless already changed)
            if ($work->getUser() === $this) {
                $work->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getLeaderProjects(): Collection
    {
        return $this->leaderProjects;
    }

    public function addLeaderProject(Project $leaderProject): self
    {
        if (!$this->leaderProjects->contains($leaderProject)) {
            $this->leaderProjects[] = $leaderProject;
            $leaderProject->setLeader($this);
        }

        return $this;
    }

    public function removeLeaderProject(Project $leaderProject): self
    {
        if ($this->leaderProjects->contains($leaderProject)) {
            $this->leaderProjects->removeElement($leaderProject);
            // set the owning side to null (unless already changed)
            if ($leaderProject->getLeader() === $this) {
                $leaderProject->setLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getManageProjects(): Collection
    {
        return $this->manageProjects;
    }

    public function addManageProject(Project $manageProject): self
    {
        if (!$this->manageProjects->contains($manageProject)) {
            $this->manageProjects[] = $manageProject;
            $manageProject->addManager($this);
        }

        return $this;
    }

    public function removeManageProject(Project $manageProject): self
    {
        if ($this->manageProjects->contains($manageProject)) {
            $this->manageProjects->removeElement($manageProject);
            $manageProject->removeManager($this);
        }

        return $this;
    }
    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return $this->hash;
    }

    public function  getRoles()
    {
        return array('ROLE_USER');
    }

    public function  eraseCredentials()
    {
    }

    public function getUser()
    {
        return $this->mail;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->mail,
            $this->hash,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->mail,
            $this->hash,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }


}
