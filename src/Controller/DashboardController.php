<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TaskRepository;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DashboardController extends Controller
{

    const TODAY_COLOR = "#aa0000";
    const OTHER_COLOR = "#000000";

    /**
     * Render DashBoard view
     * @param TaskRepository $taskRepository
     * @return Response
     * @Route("/dashboard", name="dashboard")
     * @Security("has_role('ROLE_USER')")
     */
    public function index(TaskRepository $taskRepository): Response
    {
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
        $weekNo = $dateTime->format("W");
        $newDate = new \DateTime();
        $newDate->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $newDate->setISODate($dateTime->format("Y"), $weekNo);
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'tasks' => $taskRepository->findMyTasksSortedByPriority($user->getId()),
            'user' => $this->getUser(),
            'monDate' => $newDate->format("d.m"),
            'monColor' => $monColor,
            'tueColor' => $tueColor,
            'wedColor' => $wedColor,
            'thuColor' => $thuColor,
            'friColor' => $friColor,
            'satColor' => $satColor,
            'sunColor' => $sunColor,
            'tueDate' => $newDate->add(new \DateInterval('P1D'))->format('d m'),
            'wedDate' => $newDate->add(new \DateInterval('P1D'))->format('d m'),
            'thuDate' => $newDate->add(new \DateInterval('P1D'))->format('d m'),
            'friDate' => $newDate->add(new \DateInterval('P1D'))->format('d m'),
            'satDate' => $newDate->add(new \DateInterval('P1D'))->format('d m'),
            'sunDate' => $newDate->add(new \DateInterval('P1D'))->format('d m'),
        ]);

    }

}
