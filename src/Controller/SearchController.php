<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="search")
     * @Security("has_role('ROLE_USER')")
     */
    public function index(ProjectRepository $projectRepository)
    {
        return $this->render('search/index.html.twig', [
            'projects' => $projectRepository->findAllSearch(),
            'controller_name' => 'SearchController',
        ]);
    }

    /**
     * @Route("/search/{data}", name="search_projects", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function showProjects($data, ProjectRepository $projectRepository)
    {
        if(count($projectRepository->findProjectsMatch($data))==1){
            foreach($projectRepository->findProjectsMatch($data) as $project){
                return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
            }

        }

        return $this->render('search/show_projects.html.twig', [
            'controller_name' => 'SearchController',
            'projects' => $projectRepository->findProjectsMatch($data)
        ]);
    }

}

