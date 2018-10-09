<?php

namespace App\Model;


use App\Entity\Task;

class ImportModel {

    private $defProject = "new_project";
    private const REG_DATE_TIME = "/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";

    /**
     * ImportModel constructor.
     * @param string $defProject
     */
    public function __construct(string $defProject = null)
    {
        $this->defProject = $defProject;
    }

    public function import(string $txtAreaData) {
        $separator = PHP_EOL;
        $line = strtok($txtAreaData, $separator);

        while ($line !== false) {
            $task = $this->parse($line);
            $this->saveToDatabse($task);
            $line = strtok($separator);
        }
        strtok('', '');
    }

    public function parse(string $line) {
        $task = new TaskTO();
        $arLine = explode(" ", $line);

        $this->getCompletion($arLine, $task);
        $this->getPriority($arLine, $task);
        $this->getDate($arLine, $task);

        return $task;
    }

    /**
     * Get completion from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getCompletion(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if ($outArLine[0] == "x") {
                $task->setCompletion(true);
                array_shift($outArLine);
            } else {
                $task->setCompletion(false);
            }
        } else {
            // TODO wrong line
        }
    }

    /**
     * get Priority from string and set it to TaskTO object
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getPriority(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if (preg_match("/^\([A-Z]\)$/", $outArLine[0])) {
                $task->setPriority($outArLine[0][1]);
                array_shift($outArLine);
            }
        } else {
            // TODO wrong line
        }
    }

    /**
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getDate(&$outArLine, $task) {
        if (isset($outArLine[0])) {
            if (preg_match(self::REG_DATE_TIME, $outArLine[0])) {
                if ($task->isCompletion()) {
                    $task->setCompletionDate(new \DateTime($outArLine[0]));
                } else {
                    $task->setCreateDate(new \DateTime($outArLine[0]));
                }
                if (isset($outArLine[0])) {
                    if (preg_match(self::REG_DATE_TIME, $outArLine[1])) {
                        $task->setCreateDate(new \DateTime($outArLine[1]));
                        array_slice($outArLine, 2);
                    }
                } else {
                    // TODO wrong line
                }
            }
        } else {
            // TODO wrong line
        }
    }

    private function saveToDatabse($task) {

    }

}