<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     */
    public function index(ProjectRepository $projectRepository)
    {
        return $this->render('search/index.html.twig', [
            'projects' => $projectRepository->findAllSearch(),
            'controller_name' => 'SearchController',
        ]);
    }
}
