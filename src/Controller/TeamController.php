<?php

namespace App\Controller;

use App\Constants;
use App\Entity\Team;
use App\Entity\Role;
use App\Entity\User;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/team")
 */
class TeamController extends Controller
{
    /**
     * @Route("/", name="team_index", methods="GET")
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', ['teams' => $teamRepository->findAll()]);
    }

    /**
     * @Route("/create", name="team_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {

        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($team);
            $em->flush();

            //id 1 should be id of actual user, not set yet
            $user=$this->getDoctrine()->getRepository(User::class)->find(1);

            $role = new Role();
            $role->setUser($user);
            $role->setTeam($team);
            $role->setType(Constants::LEADER);
            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute('team_index');
        }




        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="team_show", methods="GET")
     */
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', ['team' => $team]);
    }

    /**
     * @Route("/{id}/edit", name="team_edit", methods="GET|POST")
     */
    public function edit(Request $request, Team $team): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_edit', ['id' => $team->getId()]);
        }

        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="team_delete", methods="DELETE")
     */
    public function delete(Request $request, Team $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
        }

        return $this->redirectToRoute('team_index');
    }
}
