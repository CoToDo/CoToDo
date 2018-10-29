<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\WorkRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class RestApisController
 * @package App\Controller
 * @\Sensio\Bundle\FrameworkExtraBundle\Configuration\Route("/projects")
 */
class RestApisController extends Controller {

    /**
     * Render project's tasks details + adding comments form
     * @param Request $request
     * @param Project $project
     * @param Task $task
     * @param WorkRepository $workRepository
     * @return Response
     * @Route("/{idp}/tasks/{id}/times", name="project_task_times", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function getTimes(Request $request, Project $project, Task $task, WorkRepository $workRepository): Response {
        $task->getUsersTimes($users,$times);

        return $this->render('file/rest.html.twig', [
            'values' => json_encode($times)
        ]);
    }

    /**
     * Render project's tasks details + adding comments form
     * @param Request $request
     * @param Project $project
     * @param Task $task
     * @param WorkRepository $workRepository
     * @return Response
     * @Route("/{idp}/tasks/{id}/users", name="project_task_users", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function getUsers(Request $request, Project $project, Task $task, WorkRepository $workRepository): Response {
        $task->getUsersTimes($users,$times);

        return $this->render('file/rest.html.twig', [
            'values' => json_encode($users)
        ]);
    }
}