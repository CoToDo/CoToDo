<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SynchronizationController extends Controller
{
    /**
     * @Route("/synchronization", name="synchronization")
     */
    public function index()
    {
        return $this->render('synchronization/index.html.twig', [
            'controller_name' => 'SynchronizationController',
        ]);
    }
}
