<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TaskRepository;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DashboardController extends Controller {
    const TODAY_COLOR = "#aa0000";
    const OTHER_COLOR = "#000000";
    const TEXT_SIZE = 10;

    /**
     * Render DashBoard view
     * @param TaskRepository $taskRepository
     * @return Response
     * @throws \Exception
     * @Route("/dashboard", name="dashboard")
     * @Security("has_role('ROLE_USER')")
     */
    public function index(TaskRepository $taskRepository): Response {
        $user = $this->getUser();
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $monColor = ($dateTime->format("D") === "Mon") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
        $tueColor = ($dateTime->format("D") === "Tue") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
        $wedColor = ($dateTime->format("D") === "Wed") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
        $thuColor = ($dateTime->format("D") === "Thu") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
        $friColor = ($dateTime->format("D") === "Fri") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
        $satColor = ($dateTime->format("D") === "Sat") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
        $sunColor = ($dateTime->format("D") === "Sun") ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;

        $monDate = $this->getMonday();

        $tueDate = $this->getDaywithInterval(1);
        $wedDate = $this->getDaywithInterval(2);
        $thuDate = $this->getDaywithInterval(3);
        $friDate = $this->getDaywithInterval(4);
        $satDate = $this->getDaywithInterval(5);
        $sunDate = $this->getDaywithInterval(6);
        $monTasks = array();
        $tueTasks = array();
        $wedTasks = array();
        $thuTasks = array();
        $friTasks = array();
        $satTasks = array();
        $sunTasks = array();
        foreach ($taskRepository->findMyTasksSortedByPriority($user->getId()) as $task) {
            var_dump($task->getDeadline()->format('dMY'));
            switch ($task->getDeadline()->format('dMY')) {
                case $monDate->format('dMY'):
                    $monTasks[] = $task;
                    break;
                case $tueDate->format('dMY'):
                    $tueTasks[] = $task;
                    break;
                case $wedDate->format('dMY'):
                    $wedTasks[] = $task;
                    break;
                case $thuDate->format('dMY'):
                    $thuTasks[] = $task;
                    break;
                case $friDate->format('dMY'):
                    $friTasks[] = $task;
                    break;
                case $satDate->format('dMY'):
                    $satTasks[] = $task;
                    break;
                case $sunDate->format('dMY'):
                    $sunTasks[] = $task;
                    break;
            }
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tasks' => $taskRepository->findMyTasksSortedByPriority($user->getId()),
            'user' => $this->getUser(),
            'monColor' => $monColor,
            'tueColor' => $tueColor,
            'wedColor' => $wedColor,
            'thuColor' => $thuColor,
            'friColor' => $friColor,
            'satColor' => $satColor,
            'sunColor' => $sunColor,
            'monDate' => $monDate->format('d.m'),
            'tueDate' => $tueDate->format('d.m'),
            'wedDate' => $wedDate->format('d.m'),
            'thuDate' => $thuDate->format('d.m'),
            'friDate' => $friDate->format('d.m'),
            'satDate' => $satDate->format('d.m'),
            'sunDate' => $sunDate->format('d.m'),
            'monTasks' => $monTasks,
            'tueTasks' => $tueTasks,
            'wedTasks' => $wedTasks,
            'thuTasks' => $thuTasks,
            'friTasks' => $friTasks,
            'satTasks' => $satTasks,
            'sunTasks' => $sunTasks,
            'monV' => sizeof($monTasks) * DashboardController::TEXT_SIZE,
            'tueV' => sizeof($tueTasks) * DashboardController::TEXT_SIZE,
            'wedV' => sizeof($wedTasks) * DashboardController::TEXT_SIZE,
            'thuV' => sizeof($thuTasks) * DashboardController::TEXT_SIZE,
            'friV' => sizeof($friTasks) * DashboardController::TEXT_SIZE,
            'satV' => sizeof($satTasks) * DashboardController::TEXT_SIZE,
            'sunV' => sizeof($sunTasks) * DashboardController::TEXT_SIZE,
        ]);
    }

    private function getMonday() {
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $weekNo = $dateTime->format("W");
        $monDate = new \DateTime();
        $monDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $monDate->setISODate($dateTime->format("Y"), $weekNo);
        return $monDate;
    }

    private function getDaywithInterval($interval) {
        $monDate = $this->getMonday();
        $monDate->add(new \DateInterval("P" . $interval . "D"));
        return $monDate;
    }
}
