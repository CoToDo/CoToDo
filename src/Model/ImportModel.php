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
            $parser = new ToDoParser();
            $task = $parser->parse($line);
            $this->saveToDatabse($task);
            $line = strtok($separator);
        }
        strtok('', '');
    }

    private function saveToDatabse($task) {

    }

}