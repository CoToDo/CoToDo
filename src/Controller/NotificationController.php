<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{
    /**
     * @Route("/notification", name="notification")
     */
    public function index(NotificationRepository $notificationRepository)
    {
        $notifications = $notificationRepository->findMyNotifications($this->getUser());
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications
        ]);
    }

    public function show()
    {
        $notificationRepository = $this->getDoctrine()->getManager()->getRepository(NotificationRepository::class);
        $notifications = $notificationRepository->findMyNotifications();
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications
        ]);
    }
}
