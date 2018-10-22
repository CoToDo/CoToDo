<?php

namespace App\Model;

use App\Constants;
use App\Controller\WorkController;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Work;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ImportSync {

    private $defProject;
    private $em;
    private $doctrine;
    private $projectRepository;
    private $user;

    /**
     * ImportSync constructor.
     */
    public function __construct(ManagerRegistry $doctrine, ProjectRepository $projectRepository, $defProject, User $user) {
        $this->doctrine = $doctrine;
        $this->em = $this->em = $this->doctrine->getManager();
        $this->projectRepository = $projectRepository;
        $this->defProject = $defProject;
        $this->user = $user;
    }

    /**
     * Save task to database
     * @param TaskTO $task
     */
    public function saveTask($task) {
        // TODO check if task hasn't already exists
        $id = $this->projectRepository->findIdProjectByName($task->getProject(), $this->user);
        if (isset($id)) {
            $project = $this->projectRepository->find($id);
        } else {
            $team = new Team();
            $team->setName($task->getProject() . "team");
            $this->em->persist($team);
            $role = new Role();
            $role->setTeam($team);
            $role->setUser($this->user);
            $role->setType(Constants::LEADER);
            $this->em->persist($role);
            $project = new Project();
            $project->setName($task->getProject());
            $project->setDescription($task->getProject());
            $dateTime = new \DateTime('now');
            $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $project->setCreateDate($dateTime);
            $project->setTeam($team);
            $this->em->persist($project);
        }

        /** set task */
        $taskToSave = new Task();
        $taskToSave->setName($task->getName());
        $tmp = $task->getPriority();
        if (isset($tmp)) {
            $taskToSave->setPriority($task->getPriority());
        }

        $tmp = $task->getCreateDate();
        if (isset($tmp)) {
            $taskToSave->setCreateDate($task->getCreateDate());
        }

        $tmp = $task->getCompletionDate();
        if (isset($tmp)) {
            $taskToSave->setCompletionDate($task->getCompletionDate());
        }

        $tmp = $task->getDeadline();
        if (isset($tmp)) {
            $taskToSave->setDeadline($task->getDeadline());
        }

        $taskToSave->setProject($project);
        $this->em->persist($taskToSave);

        /** set work */
        $work = new Work();
        $work->setTask($taskToSave);
        $work->setUser($this->user);
        $work->setDescription(WorkController::ASSIGNED_BY . $this->user->getUserName());

        $this->em->persist($work);

        $this->em->flush();
    }

}