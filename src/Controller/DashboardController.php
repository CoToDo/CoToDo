<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TaskRepository;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")7
     * @Security("has_role('ROLE_USER')")
     */
    public function index(TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tasks' => $taskRepository->findMyTasksSortedByPriority($user->getId())
        ]);
    }


    /**
     * @Route("/dashboard/{param}", name="dashboard_search")7
     * @Security("has_role('ROLE_USER')")
     */
    public function search(TaskRepository $taskRepository, $param) : Response {
        $user = $this->getUser();
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tasks' => $taskRepository->findMyTasksSortedByPriorityMatch($user->getId(), $param)
        ]);
    }
}
