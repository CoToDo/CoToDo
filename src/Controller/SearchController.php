<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @Route("/searchProjects", name="search_projects", methods="GET|POST")
     */
    public function showProjects(Request $request, ProjectRepository $projectRepository)
    {
        $data = "";
        foreach ($request->get('search') as $r) {
//            echo $r . "\r\n";
            $data = $r;
            break;
        }


        return $this->render('search/show_projects.html.twig', [
            'controller_name' => 'SearchController',
            'projects' => $projectRepository->findProjectsMatch($data)
        ]);
    }

    /**
     * @Route("/tmp", name="tmp")
     */
    public function searchAction(ProjectRepository $projectRepository, Request $request) : Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

//            $projectRepository->findProjectsMatch($form["search"]->getData())
            echo("coje");

            return $this->redirectToRoute('search_projects', ['param' => $form["search"]->getData()]);
        }

        return $this->render('search/in.html.twig', [
            'form' => $form->createView(),
            'projects' => $projectRepository->findAllSearch(),
        ]);
    }
}

