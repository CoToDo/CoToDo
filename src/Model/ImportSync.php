<?php

namespace App\Model;

use App\Constants;
use App\Controller\WorkController;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Work;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TeamRepository;
use App\Repository\WorkRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

class ImportSync {

    private $defProject;
    private $em;
    private $projectRepository;
    private $taskRepository;
    private $teamRepository;
    private $workRepository;
    private $tagRepository;
    private $user;
    private $dateTime;

    /**
     * ImportSync constructor.
     */
    public function __construct(ObjectManager $em, $defProject, User $user) {
        $this->em = $em;
        $this->projectRepository = $em->getRepository(Project::class);
        $this->taskRepository = $em->getRepository(Task::class);
        $this->teamRepository = $em->getRepository(Team::class);
        $this->workRepository = $em->getRepository(Work::class);
        $this->tagRepository = $em->getRepository(Tag::class);
        $this->defProject = $defProject;
        $this->user = $user;
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
                    $taskInDb = $this->taskRepository->find($taskId);
                    if (!isset($workId)) {
                        $work = $this->setWorkData();
                        $work->setUser($this->user);
                        $work->setTask($taskInDb);
                        $this->em->persist($work);
                    }
                    $this->saveTags($task, $taskInDb);
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

                    $this->saveTags($task, $taskToSave);
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

            $this->saveTags($task, $taskToSave);
        }
        $this->em->flush();
    }

    private function saveTags(TaskTO $taskTO, Task $task) {
        foreach ($taskTO->getTags() as $tagName) {
            $tag = $this->tagRepository->findOneBy(['name' => $tagName]);
            if (!isset($tag)) {
                $tag = new Tag();
                $tag->setName($tagName);
            }
            $tag->addTask($task);
            $this->em->persist($tag);
        }
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