<?php

namespace App\Model;


class TaskTO
{

    /** @var boolean */
    private $completion = false;

    /** @var string */
    private $name = "";

    /** @var string */
    private $priority;

    /** @var \DateTime */
    private $createDate;

    /** @var \DateTime */
    private $completionDate;

    /** @var \DateTime */
    private $deadline;

    /** @var array */
    private $tags;

    /** @var array */
    private $projects;

    public function isPrioritySet()
    {
        if (isset($this->priority)) {
            return true;
        } else {
            return false;
        }
    }

    public function getProject()
    {
        return $this->projects[0];
    }

    /**
     * @return bool
     */
    public function isCompletion(): bool
    {
        return $this->completion;
    }

    /**
     * @param bool $completion
     */
    public function setCompletion(bool $completion): void
    {
        $this->completion = $completion;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     */
    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->createDate;
    }

    /**
     * @param \DateTime $createDate
     */
    public function setCreateDate(\DateTime $createDate): void
    {
        $this->createDate = $createDate;
    }

    /**
     * @return \DateTime
     */
    public function getCompletionDate(): ?\DateTime
    {
        return $this->completionDate;
    }

    /**
     * @param \DateTime $completionDate
     */
    public function setCompletionDate(\DateTime $completionDate): void
    {
        $this->completionDate = $completionDate;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline(): ?\DateTime
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     */
    public function setDeadline(\DateTime $deadline): void
    {
        $this->deadline = $deadline;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    /**
     * @param array $projects
     */
    public function setProjects(array $projects): void
    {
        $this->projects = $projects;
    }

}