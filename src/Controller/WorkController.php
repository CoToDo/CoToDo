<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Work;
use App\Form\WorkType;
use App\Model\NotificationModel;
use App\Repository\WorkRepository;
use App\WarningMessages;
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

    const ASSIGNED_BY = "Assigned by ";

    /**
     * @param Request $request
     * @param Task $task
     * @return Response
     * @Route("/{id}/create", name="work_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("task.getProject().getTeam().isAdmin(user)")
     */
    public function create(Request $request, Task $task, \Swift_Mailer $mailer): Response
    {
        $work = new Work();
        $work->setTask($task);
        $form = $this->createForm(WorkType::class, $work, [
            'teamId' => $task->getProject()->getTeam()->getId(),
            'userRepository' => $this->getDoctrine()->getRepository(User::class)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $work->setDescription(WorkController::ASSIGNED_BY . $this->getUser()->getUserName());
            if ($task->isUserSet($work->getUser())) {
                //user has work on task

                $this->addFlash('warning',WarningMessages::WARNING_USER);
                return $this->render('work/new.html.twig', [
                    'work' => $work,
                    'task' => $task,
                    'form' => $form->createView(),
                ]);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($work);
            $em->flush();

            // persist notification
            $notificationModel = new NotificationModel();
            $notification = $notificationModel->work($work->getUser(), $work->getTask()->getProject(), $work->getTask(), $this->getUser());
            $em->persist($notification);
            $em->flush();

            $message = (new \Swift_Message('CoToDo Notification'))
                ->setFrom('info.cotodo@gmail.com')
                ->setTo($notification->getUser()->getMail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/task_work.html.twig',
                        array('notification' => $notification)
                    ),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('project_task_show', ['idp' => $task->getProject()->getId(), 'id' => $task->getId()]);
        }

        return $this->render('work/new.html.twig', [
            'work' => $work,
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Work $work
     * @return Response
     * @Route("/{id}", name="work_show", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getTask().getProject().getTeam().isMember(user)")
     */
    public function show(Work $work): Response
    {
        return $this->render('work/show.html.twig', [
            'work' => $work,
            'team' => $work->getTask()->getProject()->getTeam(),
            'userRole' => $this->getUser()]);
    }

    /**
     * @param Request $request
     * @param Work $work
     * @Route("/{id}/edit", name="work_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getTask().getProject().getTeam().isAdmin(user)")
     * @return Response
     */
    public function edit(Request $request, Work $work): Response
    {
        $form = $this->createForm(WorkType::class, $work, [
            'teamId' => $work->getTask()->getProject()->getTeam()->getId(),
            'userRepository' => $this->getDoctrine()->getRepository(User::class),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_task_index', ['id' => $work->getTask()->getProject()->getId()]);
        }

        return $this->render('work/edit.html.twig', [
            'work' => $work,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Work $work
     * @return Response
     * @Route("/{id}", name="work_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getTask().getProject().getTeam().isAdmin(user)")
     */
    public function delete(Request $request, Work $work): Response
    {
        $task = $work->getTask();
        $project = $task->getProject();
        if ($this->isCsrfTokenValid('delete' . $work->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($work);
            $em->flush();
        }

        return $this->redirectToRoute('project_task_show', ['idp' => $project->getId(), 'id' => $task->getId()]);
    }

    /**
     * @param Work $work
     * @return Response
     * @Route("/{id}/start", name="work_set_start", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getUser().equals(user)")
     */
    public function setStart(Work $work): Response
    {
        // set startDate
        $dateTime = new \DateTime('now');;
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $work->getStartDate()) {
            $work->setStartDate($dateTime);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($work);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @param Work $work
     * @return Response
     * @Route("/{id}/end", name="work_set_end", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("work.getUser().equals(user)")
     * @Security("work.isStartSet()")
     */
    public function setEnd(Work $work): Response
    {
        // set createDate
        $dateTime = new \DateTime('now');;
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $work->getEndDate()) {
            $work->setEndDate($dateTime);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($work);
        $em->flush();

        $newWork = new Work();
        $newWork->setUser($work->getUser());
        $newWork->setDescription($work->getDescription());
        $newWork->setTask($work->getTask());

        $em->persist($newWork);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @param Request $request
     * @param Task $task
     * @return Response
     * @Route("/{id}/assign", name="work_assign_yourself", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("task.getProject().getTeam().isMember(user)")
     */
    public function assignYourself(Request $request, Task $task): Response
    {
        $work = new Work();
        $work->setTask($task);
        $work->setUser($this->getUser());
        $work->setDescription(WorkController::ASSIGNED_BY . $this->getUser()->getUserName());

        if ($task->isUserSet($work->getUser())) {
            //user has work on task
            $this->addFlash('warning',WarningMessages::WARNING_USER);
            return $this->redirectToRoute('project_task_show', ['idp' => $task->getProject()->getId(), 'id' => $task->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($work);
        $em->flush();

        return $this->redirectToRoute('project_task_show', ['idp' => $task->getProject()->getId(), 'id' => $task->getId()]);
    }

}
