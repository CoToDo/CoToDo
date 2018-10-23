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
use App\Repository\TaskRepository;
use App\Repository\TeamRepository;
use App\Repository\WorkRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ImportSync {

    private $defProject;
    private $em;
    private $doctrine;
    private $projectRepository;
    private $taskRepository;
    private $teamRepository;
    private $workRepository;
    private $user;
    private $dateTime;

    /**
     * ImportSync constructor.
     */
    public function __construct(ManagerRegistry $doctrine, ProjectRepository $projectRepository, TaskRepository $taskRepository, TeamRepository $teamRepository, WorkRepository $workRepository, $defProject, User $user) {
        $this->doctrine = $doctrine;
        $this->em = $this->em = $this->doctrine->getManager();
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->teamRepository = $teamRepository;
        $this->workRepository = $workRepository;
        $this->defProject = $defProject;
        $this->user = $user;
        /* date time inicialization */
        $this->dateTime = new \DateTime('now');
        $this->dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

    }

    /**
     * Save task to database
     * @param TaskTO $task
     */
    public function saveTask($task) {
        // check if project hasn't already exists
        $projectId = $this->projectRepository->findIdProjectByName($task->getProject(), $this->user);
        if (isset($projectId)) {
            $project = $this->projectRepository->find($projectId);
            // find task in this project

            $taskId = $this->taskRepository->findTaskInProject($projectId, $task->getName());
            if (isset($taskId)) {
                $memberRole = $project->getTeam()->getMemberRole($this->user);
                if (isset($memberRole)) {
                    $teamId = $project->getTeam()->getId();
                }

                if (isset($teamId)) {
                    /* exist task, team, project */
                    /* work? */
                    $workId = $this->workRepository->findWorkWithTaskAndUser($taskId, $this->user->getId());
                    if (!isset($workId)) {
                        $taskInDb = $this->taskRepository->find($taskId);
                        $work = $this->setWorkData();
                        $work->setUser($this->user);
                        $work->setTask($taskInDb);
                        $this->em->persist($work);
                    }
                }
            } else {
                /* exist team on this project where this user is? */
                $memberRole = $project->getTeam()->getMemberRole($this->user);
                if (isset($memberRole)) {
                    $teamId = $project->getTeam()->getId();
                }

                if (isset($teamId)) {
                    /* save task, exist team, project => work not exist */
                    $taskToSave = $this->setTaskData($task);
                    $taskToSave->setProject($project);
                    $this->em->persist($taskToSave);

                    $work = $this->setWorkData();
                    $work->setTask($taskToSave);
                    $work->setUser($this->user);
                    $this->em->persist($work);
                }
            }
        } else {
            /* save team, role, project, task, work */
            /** set team */
            $team = $this->setTeamData($task->getProject() . "-team");
            $this->em->persist($team);
            /** set role */
            $role = $this->setRoleData(Constants::LEADER);
            $role->setTeam($team);
            $role->setUser($this->user);
            $this->em->persist($role);

            /** set project */
            $project = $this->setProjectData($task->getProject());
            $project->setTeam($team);
            $this->em->persist($project);

            /** set task */
            $taskToSave = $this->setTaskData($task);
            $taskToSave->setProject($project);
            $this->em->persist($taskToSave);

            /** set work */
            $work = $this->setWorkData();
            $work->setTask($taskToSave);
            $work->setUser($this->user);
            $this->em->persist($work);
        }
        $this->em->flush();
    }

    private function setWorkData() {
        $work = new Work();
        $work->setDescription(WorkController::ASSIGNED_BY . $this->user->getUserName());
        return $work;
    }

    private function setTaskData(TaskTO $task) {
        $taskToSave = new Task();
        $taskToSave->setName($task->getName());
        $tmp = $task->getPriority();
        if (isset($tmp)) {
            $taskToSave->setPriority($task->getPriority());
        } else {
            $taskToSave->setPriority('F');
        }

        $tmp = $task->getCreateDate();
        if (isset($tmp)) {
            $taskToSave->setCreateDate($task->getCreateDate());
        } else {
            $taskToSave->setCreateDate($this->dateTime);
        }

        $tmp = $task->getCompletionDate();
        if (isset($tmp)) {
            $taskToSave->setCompletionDate($task->getCompletionDate());
        }

        $tmp = $task->getDeadline();
        if (isset($tmp)) {
            $taskToSave->setDeadline($task->getDeadline());
        } else {
            /** TODO copy datetime there */
            $taskToSave->setDeadline($this->dateTime->add(\DateInterval::createFromDateString('3600')));
        }
        return $taskToSave;
    }

    private function setTeamData(string $teamName) {
        $team = new Team();
        $team->setName($teamName);
        return $team;
    }

    private function setRoleData(string $roleType) {
        $role = new Role();
        $role->setType($roleType);
        return $role;
    }

    private function setProjectData($projectName) {
        $project = new Project();
        $project->setName($projectName);
        $project->setDescription($projectName);
        $project->setCreateDate($this->dateTime);
        return $project;
    }
}