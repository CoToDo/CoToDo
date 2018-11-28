<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\DayEnum;
use App\Model\DayTO;
use App\Repository\TaskRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $monColor = $this->getProperColor($dateTime, "Mon");
        $tueColor = $this->getProperColor($dateTime, "Thu");
        $wedColor = $this->getProperColor($dateTime, "Wed");
        $thuColor = $this->getProperColor($dateTime, "Thu");
        $friColor = $this->getProperColor($dateTime, "Fri");
        $satColor = $this->getProperColor($dateTime, "Sat");
        $sunColor = $this->getProperColor($dateTime, "Sun");
        $monDate = $this->getDayWithInterval(0);
        $tueDate = $this->getDayWithInterval(1);
        $wedDate = $this->getDayWithInterval(2);
        $thuDate = $this->getDayWithInterval(3);
        $friDate = $this->getDayWithInterval(4);
        $satDate = $this->getDayWithInterval(5);
        $sunDate = $this->getDayWithInterval(6);
        $monTasks = array();
        $tueTasks = array();
        $wedTasks = array();
        $thuTasks = array();
        $friTasks = array();
        $satTasks = array();
        $sunTasks = array();
        foreach ($taskRepository->findMyTasksSortedByPriority($user->getId()) as $task) {
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

        $this->setUpDaysTO($taskRepository, $user);
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

    private function getDayWithInterval($interval) {
        $monDate = $this->getMonday();
        $monDate->add(new \DateInterval("P" . $interval . "D"));
        return $monDate;
    }

    private function getProperColor($dateTime, $day) {
        return ($dateTime->format("D") === $day) ? DashboardController::TODAY_COLOR : DashboardController::OTHER_COLOR;
    }

    private function setUpDaysTO(TaskRepository $taskRepository, User $user) {
        $dateTime = new \DateTime('now');
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $days = array();
        for ($i = 0; $i < 7; $i++) {
            $day = new DayTO();
            $day->setColor($this->getProperColor($dateTime, DayEnum::$values[$i]));
            $day->setDate($this->getDayWithInterval($i));
            foreach ($taskRepository->findMyTasksSortedByPriority($user->getId()) as $task) {
                if ($task->getDeadline()->format('dMY') === $day->getDate()->format('dMY')) {
                    $day->getTasks()[] = $task;
                }
            }
            $days[] = $day;
        }

        var_dump($days);
    }
}
