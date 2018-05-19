<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkRepository")
 */
class Work
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
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Task", inversedBy="works")
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="works")
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Work
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @param \DateTimeInterface $startDate
     * @return Work
     */
    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param \DateTimeInterface $endDate
     * @return Work
     */
    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Task|null
     */
    public function getTask(): ?Task
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     * @return Work
     */
    public function setTask(?Task $task): self
    {
        $this->task = $task;

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
     * @return Work
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
        return "" . $this->getDescription();
    }

    /**
     * Check if start date is set
     * @return bool
     */
    public function isStartSet()
    {
        if($this->getStartDate()===null) return false;
        return true;
    }

    /**
     * Check if end date is set
     * @return bool
     */
    public function isEndSet()
    {
        if($this->getEndDate()===null) return false;
        return true;
    }


    /**
     * @return \DateInterval|null
     */
    public function getWorkTime(): ?\DateInterval
    {
        return $this->getStartDate()->diff($this->getEndDate());
    }

}
