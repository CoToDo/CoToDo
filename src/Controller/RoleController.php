<?php

namespace App\Controller;

use App\Constants;
use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/roles")
 */
class RoleController extends Controller
{
    /**
     * @Route("/{id}/edit", name="role_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("role.getTeam().isAdmin(user)")
     */
    public function edit(Request $request, Role $role): Response
    {
        $user = $this->getUser();
        if(!$role->getTeam()->isMember($user)) {
            return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
        }

        $userRole = $role->getTeam()->getMemberRole($this->getUser());
        if ($userRole == Constants::USER) {
            return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
        }

        $lastUserId = $role->getUser()->getId();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($userRole == Constants::ADMIN && $role->getType() == Constants::LEADER) {
                return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
            }
            if($role->getTeam()->isMember($role->getUser()) && $lastUserId != $role->getUser()->getId()) {
                //user has already in team
                return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
        }

        return $this->render('role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="role_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("role.getTeam().isAdmin(user)")
     */
    public function delete(Request $request, Role $role): Response
    {
        $this->canDoIt($role, $role->getType());
        if ($this->isCsrfTokenValid('delete' . $role->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($role, $role->getType());
            $em->flush();
        }

        return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
    }

    private function canDoIt(Role $role, $lastRoleType)
    {
        if ($lastRoleType == Constants::ADMIN && $role->getType() == Constants::LEADER) {
            return false;
        }
        return true;
    }


}
