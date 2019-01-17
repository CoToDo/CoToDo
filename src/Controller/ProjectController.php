<?php

namespace App\Controller;

use App\Entity\Comment;
use App\FlashMessages;
use App\Model\DownloadModel;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Team;
use App\Form\CommentType;
use App\Form\ProjectType;
use App\Form\TaskType;
use App\Model\NotificationModel;
use App\Repository\ProjectRepository;
use App\Repository\TagRepository;
use App\Repository\TaskRepository;
use App\Repository\WorkRepository;
use App\WarningMessages;
use Psr\Log\LoggerInterface;
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
     * Render user's projects
     * @param ProjectRepository $projectRepository
     * @return Response
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
     * Render create view for create project
     * @param Request $request
     * @return Response
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

        // Automatically set createDate
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $project->getCreateDate()) {
            $project->setCreateDate($dateTime);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            if($project->getTeam() == null) {
                $this->addFlash(
                    FlashMessages::WARNING,
                    'Project must have a team!'
                );

                return $this->render('project/new.html.twig', [
                    'project' => $project,
                    'form' => $form->createView(),
                ]);

            }

            $projectRepository = $this->getDoctrine()->getRepository(Project::class);
            $existingProjectWithSameName = $projectRepository->findOneBy(['name' => $project->getName()]);
            if($existingProjectWithSameName){
                $this->addFlash('warning',WarningMessages::WARNING_PROJECT_NAME_USED);
                return $this->render('project/new.html.twig', [
                    'project' => $project,
                    'form' => $form->createView(),
                ]);
            }

            $string = $project->getName();
            $length = strlen($string);
            for ($i = 0; $i < $length; $i++) {
                if($string[$i] == '_') {
                    $this->addFlash('warning',WarningMessages::WARNING_PROJECT_UNDERSCORE);
                    return $this->render('project/new.html.twig', [
                        'project' => $project,
                        'form' => $form->createView(),
                    ]);
                }
            }

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
     * Render view for create new subproject
     * @param Request $request
     * @param Project $parentProject
     * @return Response
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

        // Automatically set createDate
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
     * Render project details
     * @param TaskRepository $taskRepository
     * @param Project $project
     * @return Response
     * @Route("/{id}", name="project_show", methods="GET")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function show(TaskRepository $taskRepository, Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
            'subprojects' => $project->getSubProjects(),
            'percent' => $this->getPercent($project->getTasks()->count(), count($taskRepository->getCountClosedTasks($project->getId()))),
            'team' => $project->getTeam(),
            'userRole' => $this->getUser(),
            'tasks' => $taskRepository->findUncompleteTasks($project->getId())]);
    }

    /**
     * Render edit form for project
     * @param Request $request
     * @param Project $project
     * @return Response
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
     * Delete project
     * @param Request $request
     * @param Project $project
     * @return Response
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
     * Render project's tasks
     * @param TaskRepository $taskRepository
     * @param Project $project
     * @return Response
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
     * Render form for create task on project
     * @param Request $request
     * @param Project $project
     * @return Response
     * @Route("/{id}/tasks/create", name="project_task_new", methods="GET|POST")
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user)")
     */
    public function createTask(Request $request, Project $project, TagRepository $tagRepository): Response
    {
        $task = new Task();
        $task->setProject($project);

        // Automatically set createDate
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if (null === $task->getCreateDate()) {
            $task->setCreateDate($dateTime);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($task->getTags() as $tag) {
                $tagInDb = $tagRepository->findOneBy(['name' => $tag->getName()]);
                if (isset($tagInDb)) {
                    $task->removeTag($tag);
                    $task->addTag($tagInDb);
                }
            }

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
     * Render project's tasks details + adding comments form
     * @param Request $request
     * @param Project $project
     * @param Task $task
     * @param WorkRepository $workRepository
     * @param \Swift_Mailer $mailer
     * @return Response
     * @Route("/{idp}/tasks/{id}", name="project_task_show", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function showTask(Request $request, Project $project, Task $task, WorkRepository $workRepository, \Swift_Mailer $mailer): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $comment->setTask($task);
            $comment->setUser($this->getUser());
            $dateTime = new \DateTime('now');
            $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));

            if (null === $comment->getDate()) {
                $comment->setDate($dateTime);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            // persist notification
            $notificationModel = new NotificationModel();
            foreach ($workRepository->findUniqueWorks($task->getId()) as $work) {
                $notification = $notificationModel->commment($work->getUser(), $task->getProject(), $task, $this->getUser());
                $em->persist($notification);
                $em->flush();

                $message = (new \Swift_Message('CoToDo Notification'))
                    ->setFrom('info.cotodo@gmail.com')
                    ->setTo($notification->getUser()->getMail())
                    ->setBody(
                        $this->renderView(
                        // templates/emails/team_add.html.twig
                            'emails/task_comment.html.twig',
                            array('notification' => $notification)
                        ),
                        'text/html'
                    );
                $mailer->send($message);
            }

            return $this->redirectToRoute('project_task_show', ['id' => $task->getId(), 'idp' => $project->getId()]);
        }

        return $this->render('task/show.html.twig', [
            'user' => $this->getUser(),
            'task' => $task,
            'works' => $workRepository->findUniqueWorks($task->getId()),
            'project' => $project,
            'team' => $project->getTeam(),
            'userRole' => $this->getUser(),
            'comments' => $task->getComments(),
            'form' => $form->createView()
        ]);
    }

    /**
     * Render project's tasks edit form
     * @param Request $request
     * @param Project $project
     * @param Task $task
     * @param TagRepository $tagRepository
     * @return Response
     * @Route("/{idp}/tasks/{id}/edit", name="project_task_edit", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user)")
     */
    public function editTask(Request $request, Project $project, Task $task, TagRepository $tagRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            var_dump($task->getName());
            foreach ($task->getTags() as $tag) {
                $tagInDb = $tagRepository->findOneBy(['name' => $tag->getName()]);
                if (isset($tagInDb)) {
                    $task->removeTag($tag);
                    $task->addTag($tagInDb);
                }
            }
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
     * Complete task button
     * @param Project $project
     * @param Task $task
     * @return Response
     * @Route("/{idp}/tasks/{id}/complete", name="project_task_complete", methods="GET")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user) or project.getTeam().isLeader(user)")
     */
    public function completeTask(WorkRepository $workRepository, Project $project, Task $task, \Swift_Mailer $mailer): Response
    {
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        if($task->getCompletionDate() == null){
            $task->setCompletionDate($dateTime);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        // persist notifications
        $notificationModel = new NotificationModel();
        foreach ($workRepository->findUniqueWorks($task->getId()) as $work) {
            $notification = $notificationModel->close($work->getUser(), $task->getProject(), $task, $this->getUser());
            $em->persist($notification);
            $em->flush();

            $message = (new \Swift_Message('CoToDo Notification'))
                ->setFrom('info.cotodo@gmail.com')
                ->setTo($notification->getUser()->getMail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/team_add.html.twig
                        'emails/task_close.html.twig',
                        array('notification' => $notification)
                    ),
                    'text/html'
                );
            $mailer->send($message);
        }

        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
    }

    /**
     * @Route("/{idp}/tasks/{id}/history", name="project_task_history", methods="GET|POST")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function showTaskHistory( WorkRepository $workRepository, Task $task): Response
    {
        return $this->render('task/history.html.twig', [
            'task' => $task,
            'works' => $workRepository->findClosedWorks($task->getId())
        ]);
    }

    /**
     * Reopen task button
     * @param Project $project
     * @param Task $task
     * @return Response
     * @Route("/{idp}/tasks/{id}/reopen", name="project_task_reopen", methods="GET")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isAdmin(user) or project.getTeam().isLeader(user)")
     */
    public function reopenTask(WorkRepository $workRepository, Project $project, Task $task, \Swift_Mailer $mailer): Response
    {
        if ($task->getCompletionDate() != null) {
            $task->removeCompletionDate();
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        // persist notifications
        $notificationModel = new NotificationModel();
        foreach ($workRepository->findUniqueWorks($task->getId()) as $work) {
            $notification = $notificationModel->reOpen($work->getUser(), $task->getProject(), $task, $this->getUser());
            $em->persist($notification);
            $em->flush();

            $message = (new \Swift_Message('CoToDo Notification'))
                ->setFrom('info.cotodo@gmail.com')
                ->setTo($notification->getUser()->getMail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/team_add.html.twig
                        'emails/task_reopen.html.twig',
                        array('notification' => $notification)
                    ),
                    'text/html'
                );
            $mailer->send($message);
        }
        return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
    }

    /**
     * Delete task
     *@param Request $request
     * @param Project $project
     * @param Task $task
     * @return Response
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

    /**
     * Download task's ics file
     * @param Task $task
     * @Route("/{idp}/tasks/{id}/download", name="project_task_download")
     * @ParamConverter("project", class="App\Entity\Project", options={"id" = "idp"})
     * @Security("has_role('ROLE_USER')")
     * @Security("project.getTeam().isMember(user)")
     */
    public function downloadIcs(Request $request, Project $project, Task $task): Response
    {
        $actual_link = "http://" . $request->getHttpHost() . "/projects/" . $project->getId() . "/tasks/" . $task->getId();

        $downloadCal = new DownloadModel();
        $downloadCal->downloadIcs($task, $actual_link);

        return $this->render('file/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);

    }

    private function getPercent($allTasks, $closed) {
        if($allTasks == 0) return 0;
        return ($closed / $allTasks) * 100;

    }

}
