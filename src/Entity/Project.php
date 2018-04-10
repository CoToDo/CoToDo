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
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="subProjects")
     */
    private $parentProject;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="parentProject")
     */
    private $subProjects;


    public function __construct()
    {
        $this->managers = new ArrayCollection();
        $this->subProjects = new ArrayCollection();
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

    public function __toString()
    {
        return "" . $this->getName();
    }

    /**
     * @return Collection|Project[]
     */
    public function getSubProjects(): Collection
    {
        return $this->subProjects;
    }

    public function addSubProject(Project $subProject): self
    {
        if (!$this->subProjects->contains($subProject)) {
            $this->subProjects[] = $subProject;
            $subProject->setParentProject($this);
        }

        return $this;
    }

    public function removeSubProject(Project $subProject): self
    {
        if ($this->subProjects->contains($subProject)) {
            $this->subProjects->removeElement($subProject);
            // set the owning side to null (unless already changed)
            if ($subProject->getParentProject() === $this) {
                $subProject->setParentProject(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentProject()
    {
        return $this->parentProject;
    }

    /**
     * @param mixed $parentProject
     */
    public function setParentProject($parentProject): void
    {
        $this->parentProject = $parentProject;
    }

}
