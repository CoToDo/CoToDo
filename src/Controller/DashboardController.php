<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        $user=$this->getDoctrine()->getRepository(User::class)->find(1);

        if(!$user){
            throw $this->createNotFoundException("No user found for id 1");
        }


        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'userName' => $user->getName(),
            'lastName' => $user->getLastName()
        ]);
    }
}
