<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Team;
use App\Form\CommentType;
use App\Form\ProjectType;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/projects")
 */
class ProjectController extends Controller
{
    /**
     * @Route("/", name="project_index", methods="GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findMyProjects($this->getUser()->getId()),
            'userRole' => $this->getUser()]);
    }

    /**
     * @Route("/create", name="project_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function new(Request $request): Response
    {

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project, [
            'userId' => $this->getUser()->getId(),
            'teamRepository' => $this->getDoctrine()->getRepository(Team::class)
        ]);
        $form->handleRequest($request);

        //Automatically set createDate
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $project->getCreateDate()) {
            $project->setCreateDate($dateTime);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);


    }

    /**
     * @Route("/{id}/create", name="subproject_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("parentProject.getTeam().isAdmin(user)")
     */
    public function newSubproject(Request $request, Project $parentProject): Response
    {

        $project = new Project();
        $project->setParentProject($parentProject);
        $form = $this->createForm(ProjectType::class, $project, [
            'userId' => $this->getUser()->getId(),
            'teamRepository' => $this->getDoctrine()->getRepository(Team::class)
        ]);
        $form->handleRequest($request);

        //Automatically set createDate
        $dateTime = new \DateTime('now');;
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $project->getCreateDate()) {
            $project->setCreateDate($dateTime);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
            'team' => $project->getTeam(),
            'userRole' => $this->getUser()]);


    }

    /**
     * @Route("/{id}", name="project_show", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function show(TaskRepository $taskRepository, Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'subprojects' => $project->getSubProjects(),
            'team' => $project->getTeam(),
            'userRole' => $this->getUser(),
            'tasks' => $taskRepository->findUncompleteTasks($project->getId())]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user)")
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project, [
            'userId' => $this->getUser()->getId(),
            'teamRepository' => $this->getDoctrine()->getRepository(Team::class)
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_edit', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isLeader(user)")
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('project_index');
    }


    /**
     * @Route("/{id}/tasks", name="project_task_index", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function indexTasks(TaskRepository $taskRepository, Project $project): Response
    {
        return $this->render('task/index.html.twig', [
            'team' => $project->getTeam(),
            'tasks' => $taskRepository->findTasksSortedByCompletionDate($project->getId()),
            'project' => $project,
            'userRole' => $this->getUser()]);
    }

    /**
     * @Route("/{id}/tasks/create", name="project_task_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user)")
     */
    public function createTask(Request $request, Project $project): Response
    {
        $task = new Task();
        $task->setProject($project);

        //Automatically set createDate
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $task->getCreateDate()) {
            $task->setCreateDate($dateTime);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('project_task_index', ['id' => $project->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idp}/tasks/{id}", name="project_task_show", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function showTask(Request $request, Project $project, Task $task): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTask($task);
            $comment->setUser($this->getUser());
            $dateTime = new \DateTime('now');;
            $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

            if (null === $comment->getDate()) {
                $comment->setDate($dateTime);
            }


            echo $comment;
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('project_task_show', ['id' => $task->getId(), 'idp' => $project->getId()]);
        }

        return $this->render('task/show.html.twig', [
            'user' => $this->getUser(),
            'task' => $task,
            'project' => $project,
            'team' => $project->getTeam(),
            'userRole' => $this->getUser(),
            'comments' => $task->getComments(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{idp}/tasks/{id}/edit", name="project_task_edit", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user)")
     */
    public function editTask(Request $request, Project $project, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_task_edit', ['id' => $task->getId(), 'idp' => $project->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idp}/tasks/{id}/complete", name="project_task_complete", methods="GET")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user) or project.getTeam().isLeader(user)")
     */
    public function completeTask(Project $project, Task $task): Response
    {
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if($task->getCompletionDate() == null){
            $task->setCompletionDate($dateTime);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);

    }

    /**
     * @Route("/{idp}/tasks/{id}/reopen", name="project_task_reopen", methods="GET")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user) or project.getTeam().isLeader(user)")
     */
    public function reopenTask(Project $project, Task $task): Response
    {

        if($task->getCompletionDate() != null){
            $task->removeCompletionDate();
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);

    }


    /**
     * @Route("/{idp}/tasks/{id}", name="project_task_delete", methods="DELETE")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user)")
     */
    public function deleteTask(Request $request, Project $project, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('project_task_index', ['id' => $project->getId()]);
    }

}
