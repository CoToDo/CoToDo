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
        return $this->setUpDaysTO($taskRepository, $user);
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
        $tasksNumber = array();
        for ($i = 0; $i < 7; $i++) {
            $day = new DayTO();
            $day->setColor($this->getProperColor($dateTime, DayEnum::$values[$i]));
            $day->setDate($this->getDayWithInterval($i));
            foreach ($taskRepository->findMyTasksSortedByPriority($user->getId()) as $task) {
                if ($task->getDeadline()->format('dMY') === $day->getDate()->format('dMY')) {
                    $day->addTask($task);
                }
            }
            $tasksNumber[] = sizeof($day->getTasks());
            $days[] = $day;
        }
        $maxTasks = max($tasksNumber);
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tasks' => $taskRepository->findMyTasksSortedByPriority($user->getId()),
            'user' => $this->getUser(),
            'maxTasks' => $maxTasks,
            'mon' => $days[0],
            'tue' => $days[1],
            'wed' => $days[2],
            'thu' => $days[3],
            'fri' => $days[4],
            'sat' => $days[5],
            'sun' => $days[6],
        ]);
    }
}
