<?php

namespace App\Controller;

use App\Constants;
use App\Entity\Team;
use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleType;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/teams")
 */
class TeamController extends Controller
{
    /**
     * @Route("/", name="team_index", methods="GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findMyTeams($this->getUser()->getId()),
            'userRole' => $this->getUser()]);
    }

    /**
     * @Route("/create", name="team_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
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

            $user = $this->getUser();
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
     * @Security("has_role('ROLE_USER')")
     * @Security("team.isMember(user)")
     */
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
            'roles' => $team->getRoles(),
            'projects' => $team->getProjects(),
            'userRole' => $this->getUser()]);
    }

    /**
     * @Route("/{id}/edit", name="team_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("team.isLeader(user)")
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
     * @Security("has_role('ROLE_USER')")
     * @Security("team.isLeader(user)")
     */
    public function delete(Request $request, Team $team): Response
    {
        if ($this->isCsrfTokenValid('delete' . $team->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($team);
            $em->flush();
        }

        return $this->redirectToRoute('team_index');
    }

    /**
     * @Route("/{id}/add", name="team_add_user", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("team.isAdmin(user)")
     */
    public function addUser(Request $request, Team $team): Response
    {
        $role = new Role();
        $role->setTeam($team);
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute('team_show', ['id' => $team->getId()]);
        }

        return $this->render('role/new.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }
}
