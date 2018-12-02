<?php

namespace App\Model;
class DayTO {

    const TEXT_SIZE = 10;

    /**
     * @var string $color
     */
    private $color;

    /**
     * @var \DateTime date
     */
    private $date;

    /**
     * @var array $tasks
     */
    private $tasks = array();

    /**
     * @return string
     */
    public function getColor(): string {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void {
        $this->color = $color;
    }

    /**
     * @return \DateTime
     */
    public function getDate() :\DateTime {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getDateFormatDM() {
        return $this->date->format('d.m');
    }

    /**
     * @return string
     */
    public function getDateInFormatdMY(): string {
        return $this->date->format("dMY");
    }

    /**
     * @return int
     */
    public function getVertical(): int {
        return sizeof($this->tasks) * DayTO::TEXT_SIZE;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getTasks(): array {
        return $this->tasks;
    }

    public function addTask($task) {
        $this->tasks[] = $task;
    }

    /**
     * @param array $tasks
     */
    public function setTasks(array $tasks): void {
        $this->tasks = $tasks;
    }

}