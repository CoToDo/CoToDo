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
            $sync = new ImportSync($this->defProject);
            $task = $parser->parse($line);

            // TODO check if there are more projects


            $sync->saveTask($task);
            $line = strtok($separator);
        }
        strtok('', '');
    }

}