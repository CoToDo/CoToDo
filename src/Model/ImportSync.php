<?php

namespace App\Model;

use App\Entity\Task;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ImportSync {

    private $defProject;
    private $em;
    private $doctrine;
    private $projectRepository;

    /**
     * ImportSync constructor.
     */
    public function __construct(ManagerRegistry $doctrine, ProjectRepository $projectRepository, $defProject) {
        $this->doctrine = $doctrine;
        $this->em = $this->em = $this->doctrine->getManager();
        $this->projectRepository = $projectRepository;
        $this->defProject = $defProject;
    }

    /**
     * Save task to database
     * @param TaskTO $task
     */
    public function saveTask($task) {
        $project = $this->projectRepository->findProjectByName($task->getProject());
        $taskToSave = new Task();
        $taskToSave->setName($task->getName());
        $taskToSave->setPriority($task->getPriority());
        $taskToSave->setCreateDate($task->getCreateDate());
        $taskToSave->setCompletionDate($task->getCompletionDate());
        $taskToSave->setDeadline($task->getDeadline());
        $taskToSave->setProject($project);
        $this->em->persist($taskToSave);
        $this->em->flush();
    }

}