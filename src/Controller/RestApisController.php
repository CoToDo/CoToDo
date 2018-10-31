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
     * @Route("/{idp}/tasks/{id}/graph", name="project_task_graph", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function getTimes(Task $task, WorkRepository $workRepository): Response {

        return $this->render('file/rest.html.twig', [
            'values' => $workRepository->findUserTimes($task->getId()),
        ]);
    }
}