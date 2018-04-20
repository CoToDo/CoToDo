<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Work;
use App\Form\WorkType;
use App\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/works")
 */
class WorkController extends Controller
{

    /**
     * @Route("/{id}/create", name="work_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("task.getProject().getTeam().isAdmin(user)")
     */
    public function create(Request $request, Task $task): Response
    {
        $work = new Work();
        $work->setTask($task);
        $form = $this->createForm(WorkType::class, $work, [
            'teamId' => $task->getProject()->getTeam()->getId(),
            'userRepository' => $this->getDoctrine()->getRepository(User::class)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($work);
            $em->flush();

            return $this->redirectToRoute('project_task_show', ['idp' => $task->getProject()->getId(), 'id' => $task->getId()]);
        }

        return $this->render('work/new.html.twig', [
            'work' => $work,
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="work_show", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getTask().getProject().getTeam().isMember(user)")
     */
    public function show(Work $work): Response
    {
        return $this->render('work/show.html.twig', ['work' => $work]);
    }

    /**
     * @Route("/{id}/edit", name="work_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getTask().getProject().getTeam().isAdmin(user)")
     */
    public function edit(Request $request, Work $work): Response
    {
        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('work_edit', ['id' => $work->getId()]);
        }

        return $this->render('work/edit.html.twig', [
            'work' => $work,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="work_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getTask().getProject().getTeam().isAdmin(user)")
     */
    public function delete(Request $request, Work $work): Response
    {
        if ($this->isCsrfTokenValid('delete'.$work->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($work);
            $em->flush();
        }

        return $this->redirectToRoute('work_index');
    }
}
