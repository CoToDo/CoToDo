<?php

namespace App\Controller;

use App\Repository\TaskRepository;
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

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tasks' => $taskRepository->findAllSortedByPriority()
        ]);


    }
}
