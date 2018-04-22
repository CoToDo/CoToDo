<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index", methods="GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     * @Security("has_role('ROLE_USER')"))
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'userRole' => $this->getUser()]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("userIn.equals(user)")
     */
    public function edit(Request $request, User $userIn): Response
    {
        $form = $this->createForm(UserType::class, $userIn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', ['id' => $userIn->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $userIn,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("userIn.equals(user)")
     */
    public function delete(Request $request, User $userIn): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userIn->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($userIn);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
