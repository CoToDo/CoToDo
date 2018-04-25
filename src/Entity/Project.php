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
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="subProjects")
     */
    private $parentProject;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="parentProject", cascade={"remove"})
     * @ORM\JoinColumn(name="subProject_id", referencedColumnName="subProject_id", onDelete="CASCADE")
     */
    private $subProjects;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="projects")
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project", cascade={"remove"})
     * @ORM\JoinColumn(name="task_id", referencedColumnName="task_id", onDelete="CASCADE")
     */
    private $tasks;

    /**
     * Project constructor.
     */
    public function __construct()
    {
        $this->managers = new ArrayCollection();
        $this->subProjects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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
     * @return Project
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Project
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    /**
     * @param \DateTimeInterface|null $createDate
     * @return Project
     */
    public function setCreateDate(?\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getLeader(): ?User
    {
        return $this->leader;
    }

    /**
     * @param User|null $leader
     * @return Project
     */
    public function setLeader(?User $leader): self
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return string
     */
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

    /**
     * @param Project $subProject
     * @return Project
     */
    public function addSubProject(Project $subProject): self
    {
        if (!$this->subProjects->contains($subProject)) {
            $this->subProjects[] = $subProject;
            $subProject->setParentProject($this);
        }

        return $this;
    }

    /**
     * @param Project $subProject
     * @return Project
     */
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

    /**
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @param Team|null $team
     * @return Project
     */
    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * @return Project
     */
    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return Project
     */
    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

}
