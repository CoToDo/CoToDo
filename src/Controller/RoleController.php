<?php

namespace App\Controller;

use App\Constants;
use App\FlashMessages;
use App\Entity\Role;
use App\Form\RoleType;
use App\Model\NotificationModel;
use App\Repository\RoleRepository;
use App\Repository\TeamRepository;
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
     * Render edit form for role
     * @param Request $request
     * @param Role $role
     * @return Response
     * @Route("/{id}/edit", name="role_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("(role.getTeam().isOnlyAdmin(user) and (role.isRoleUser() or role.isRoleAdmin())) or role.getTeam().isLeader(user)")
     */
    public function edit(Request $request, Role $role, \Swift_Mailer $mailer, TeamRepository $teamRepository): Response
    {
        $wrong = false;
        $userRole = $role->getTeam()->getMemberRole($this->getUser());
        $lastUserId = $role->getUser()->getId();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($userRole == Constants::ADMIN && $role->getType() == Constants::LEADER) {
                $this->addFlash(
                    FlashMessages::WARNING,
                    'You cannot change to leader!'
                );
                $wrong = true;
            }

            if($role->getTeam()->isMember($role->getUser()) && $lastUserId != $role->getUser()->getId()) {
                $this->addFlash(
                    FlashMessages::WARNING,
                    'User is already in this team!'
                );
                $wrong = true;
            }

            if($userRole == Constants::LEADER && $role->getType() != Constants::LEADER && ($teamRepository->numberOfLeaders($role->getTeam()->getId()) <=1 )) {
                $this->addFlash(
                    FlashMessages::WARNING,
                    'You are the last leader!'
                );
                $wrong=true;
            }

            if($wrong){
                return $this->returnWrong($role, $form);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // persist notification
            $notificationModel = new NotificationModel();
            $notification = $notificationModel->teamRole($role->getUser(), $role->getTeam(), $this->getUser());
            $em->persist($notification);
            $em->flush();

            $message = (new \Swift_Message('CoToDo Notification'))
                ->setFrom('info.cotodo@gmail.com')
                ->setTo($notification->getUser()->getMail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/team_add.html.twig
                        'emails/team_role.html.twig',
                        array('notification' => $notification)
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
        }

        return $this->returnWrong($role, $form);
    }


    /**
     * Render wrong view
     * @param $role
     * @param $form
     * @return Response
     */
    public function returnWrong($role, $form) {
        return $this->render('role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete role
     * @param Request $request
     * @param Role $role
     * @param TeamRepository $teamRepository
     * @return Response
     * @Route("/{id}", name="role_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("role.getTeam().isAdmin(user)")
     */
    public function delete(Request $request, Role $role, TeamRepository $teamRepository): Response
    {
        $userRole = $role->getTeam()->getMemberRole($this->getUser());
        $roleBefore = $role->getType();

        if($userRole == Constants::ADMIN && $roleBefore == Constants::LEADER) {
            return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);
        }

        if($teamRepository->numberOfLeaders($role->getTeam()->getId()) > 1 || $role->getType() == Constants::USER || $role->getType() == Constants::ADMIN) {
            if ($this->isCsrfTokenValid('delete' . $role->getId(), $request->request->get('_token'))) {
                $em = $this->getDoctrine()->getManager();
                foreach ($role->getUser()->getWorks() as $work) {
                    if ($work->getTask()->getProject()->getTeam()->getId() == $role->getTeam()->getId()) {
                        $em->remove($work);
                    }
                }
                $em->remove($role);
                $em->flush();
            }

        } else {
            $this->addFlash(
                FlashMessages::DELETE_LEADER,
                'Cannot delete this user, it is the last leader of team!'
            );
        }

        return $this->redirectToRoute('team_show', ['id' => $role->getTeam()->getId()]);


    }

}
