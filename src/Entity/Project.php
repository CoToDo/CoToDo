<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
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
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="leaderProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leader;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="manageProjects")
     */
    private $managers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="project")
     */
    private $subProject;

    public function __construct()
    {
        $this->managers = new ArrayCollection();
        $this->subProject = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(?\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getLeader(): ?User
    {
        return $this->leader;
    }

    public function setLeader(?User $leader): self
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(User $manager): self
    {
        if (!$this->managers->contains($manager)) {
            $this->managers[] = $manager;
        }

        return $this;
    }

    public function removeManager(User $manager): self
    {
        if ($this->managers->contains($manager)) {
            $this->managers->removeElement($manager);
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getSubProject(): Collection
    {
        return $this->subProject;
    }

    public function addSubProject(Project $subProject): self
    {
        if (!$this->subProject->contains($subProject)) {
            $this->subProject[] = $subProject;
            $subProject->setProject($this);
        }

        return $this;
    }

    public function removeSubProject(Project $subProject): self
    {
        if ($this->subProject->contains($subProject)) {
            $this->subProject->removeElement($subProject);
            // set the owning side to null (unless already changed)
            if ($subProject->getProject() === $this) {
                $subProject->setProject(null);
            }
        }

        return $this;
    }
}
