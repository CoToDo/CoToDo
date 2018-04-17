<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")7
     * @Security("has_role('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $user = $this->getUser();

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'userName' => $user->getName(),
            'lastName' => $user->getLastName()
        ]);


    }
}
