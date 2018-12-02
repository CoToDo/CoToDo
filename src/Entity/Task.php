<?php

namespace App\Entity;

use App\Model\GraphTO;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
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
     * @ORM\Column(type="string", length=2)
     */
    private $priority;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completionDate;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today UTC")
     */
    private $deadline;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag", mappedBy="tasks", cascade={"persist", "remove"})
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Work", mappedBy="task", cascade={"remove"})
     * @ORM\JoinColumn(name="work_id", referencedColumnName="work_id", onDelete="CASCADE")
     */
    private $works;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="task", cascade={"remove"})
     * @ORM\JoinColumn(name="comment_id", referencedColumnName="comment_id", onDelete="CASCADE")
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     */
    private $project;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->works = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * @return Task
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     * @return Task
     */
    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

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
     * @param \DateTimeInterface $createDate
     * @return Task
     */
    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCompletionDate(): ?\DateTimeInterface
    {
        return $this->completionDate;
    }

    /**
     * @param \DateTimeInterface $completionDate
     * @return Task
     */
    public function setCompletionDate(\DateTimeInterface $completionDate): self
    {
        $this->completionDate = $completionDate;

        return $this;
    }

    /**
     * @return Task
     */
    public function removeCompletionDate(): self
    {
        $this->completionDate =null;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    /**
     * @param \DateTimeInterface $deadline
     * @return Task
     */
    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     * @return Task
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addTask($this);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     * @return Task
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            $tag->removeTask($this);
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

    /**
     * @param Work $work
     * @return Task
     */
    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works[] = $work;
            $work->setTask($this);
        }

        return $this;
    }

    /**
     * @param Work $work
     * @return Task
     */
    public function removeWork(Work $work): self
    {
        if ($this->works->contains($work)) {
            $this->works->removeElement($work);
            // set the owning side to null (unless already changed)
            if ($work->getTask() === $this) {
                $work->setTask(null);
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

    /**
     * @param Comment $comment
     * @return Task
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTask($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     * @return Task
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTask() === $this) {
                $comment->setTask(null);
            }
        }

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
     * @return Project|null
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * @param Project|null $project
     * @return Task
     */
    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @param $user
     * @return null
     */
    public function getWorkId($user)
    {
        foreach ($user->getWorks() as $work) {
            if ($this->getId() == $work->getTask()->getId() && $work->getEndDate() == null) {
                return $work->getId();
            }
        }
        return null;
    }

    /**
     * @param $user
     * @return null
     */
    public function getWork($user)
    {
        foreach ($user->getWorks() as $work) {
            if ($this->getId() == $work->getTask()->getId() && $work->getEndDate() == null) {
                return $work;
            }
        }
        return null;
    }

    /**
     * Check if user is set to this task
     * @param User $user
     * @return bool
     */
    public function isUserSet(User $user)
    {
        foreach($this->getWorks() as $work) {
            if($work->getUser()->equals($user) && $work->getEndDate() == null) return true;
        }
        return false;
    }
}
