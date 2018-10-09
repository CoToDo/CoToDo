<?php

namespace App\Model;


use App\Entity\Task;

class ImportModel {

    private $defProject = "new_project";

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
            echo $line;
            $task = $this->parse($line);
            $line = strtok($separator);
        }
        strtok('', '');
    }

    public function parse(string $line) {
        $task = new TaskTO();
        $arLine = explode(" ", $line);

        $this->getCompletion($arLine, $task);
        $this->getPriority($arLine, $task);

        return $task;
    }

    /**
     * @param array $arLine
     * @param TaskTO $task
     */
    private function getCompletion(&$outArLine, $task) {
        if ($outArLine[0] == "x") {
            $task->setCompletion(true);
            array_shift($outArLine);
        } else {
            $task->setCompletion(false);
        }
    }

    /**
     * @param array $outArLine
     * @param TaskTO $task
     */
    private function getPriority(&$outArLine, $task) {
        if (preg_match("/^\([A-Z]\)$/", $outArLine[0])) {
            var_dump($outArLine[0]);
            $task->setPriority($outArLine[0][1]);
            array_shift($outArLine);
        }
    }

    private function saveToDatabse() {

    }

}