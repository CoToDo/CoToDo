<?php

namespace App\Model;

use App\Entity\User;
use App\Exception\WrongLineFormatException;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

class ImportModel {

    /** @var string  */
    private $defProject = "new_project";

    /** @var array */
    private $wrongLines = array();

    /** @var ToDoParser  */
    private $parser;

    /** @var ImportSync */
    private $sync;

    /** @var User user */
    private $user;

    /**
     * ImportModel constructor. Setup default project name
     * @param ManagerRegistry $doctrine
     * @param ProjectRepository $projectRepository
     * @param User $user
     * @param string $defProject
     */
    public function __construct(ObjectManager $em, User $user, string $defProject = null) {
        $this->parser = new ToDoParser();
        $this->sync = new ImportSync($em, $this->defProject, $user);
        $this->user = $user;
        $this->defProject = $defProject;
    }

    public function importFromString(string $txtAreaData) {
        $separator = PHP_EOL;
        $line = strtok($txtAreaData, $separator);
        while ($line !== false) {
            try {
                $task = $this->parser->parse($line);
            } catch (WrongLineFormatException $e) {
                $this->wrongLine($line);
                $line = strtok($separator);
                continue;
            }

            if(empty($task->getProject())) {
                $task->setProjects(array($this->user->getName(). "-project"));
            }

            $this->sync->saveTask($task);
            $line = strtok($separator);
        }
        strtok('', '');
        return $this->wrongLines;
    }

    public function importFromFile(string $filePath) {
        $handle = fopen("$filePath", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                try {
                    $task = $this->parser->parse($line);
                } catch (WrongLineFormatException $e) {
                    $this->wrongLine($line);
                    continue;
                }
                if (empty($task->getProject())) {
                    $task->setProjects(array($this->user->getName() . "-project"));
                }
                $this->sync->saveTask($task);
            }
            fclose($handle);
            // TODO delete file
        }
        return $this->wrongLines;
    }

    private function wrongLine($line) {
        $this->wrongLines[] = $line;
    }

}
