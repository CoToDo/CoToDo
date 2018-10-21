<?php

namespace App\Model;

use App\Entity\User;
use App\Exception\WrongLineFormatException;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
     * @param ManagerRegistry $doctrine
     * @param ProjectRepository $projectRepository
     * @param User $user
     * @param string $defProject
     */
    public function __construct(ManagerRegistry $doctrine, ProjectRepository $projectRepository, User $user, string $defProject = null) {
        $this->parser = new ToDoParser();
        $this->sync = new ImportSync($doctrine, $projectRepository, $this->defProject, $user);
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
                $line = strtok($separator);
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

    public function importFromFile(string $filePath) {

    }

    private function wrongLine($line) {
        $this->wrongLines[] = $line;
    }

}
