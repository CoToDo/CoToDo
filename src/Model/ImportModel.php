<?php

namespace App\Model;

use App\Exception\WrongLineFormatException;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class ImportModel {

    /** @var string  */
    private $defProject = "new_project";

    /** @var array */
    private $wrongLines = array();

    /** @var ToDoParser  */
    private $parser;

    /** @var ImportSync */
    private $sync;

    /**
     * ImportModel constructor. Setup default project name
     * @param string $defProject
     */
    public function __construct(ManagerRegistry $doctrine, ProjectRepository $projectRepository, string $defProject = null) {
        $this->parser = new ToDoParser();
        $this->sync = new ImportSync($doctrine, $projectRepository, $this->defProject);
        $this->defProject = $defProject;
    }

    public function import(string $txtAreaData) {
        $separator = PHP_EOL;
        $line = strtok($txtAreaData, $separator);
        while ($line !== false) {
            try {
                $task = $this->parser->parse($line);
            } catch (WrongLineFormatException $e) {
                $this->wrongLine($line);
            }
            if (empty($task->getProjects()) || isset($task->getProjects()[1])) {
                $this->wrongLine($line);
            }
            $this->sync->saveTask($task);
            $line = strtok($separator);
        }
        strtok('', '');
        return $this->wrongLines;
    }

    private function wrongLine($line) {
        $this->wrongLines[] = $line;
    }

}
