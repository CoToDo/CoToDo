<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{
    public function index(NotificationRepository $notificationRepository, $dropdownLeft = false)
    {
        $notifications = $notificationRepository->findMyNotifications($this->getUser());
        return $this->render('notification/index.html.twig', [
            'controller_name' => 'NotificationController',
            'notifications' => $notifications,
            'dropdown_left' => $dropdownLeft
        ]);
    }

    /**
     * @Route("/notification/{id}", name="notification_show")
     * @Security("has_role('ROLE_USER')")
     * @Security("notification.getUser().equals(user)")
     */
    public function show(Notification $notification) {
       $notification->setShow(false);

       $em = $this->getDoctrine()->getManager();
       $em->persist($notification);
       $em->flush();

       echo $this->container->get('router')->getContext()->getBaseUrl();
       return $this->redirect( $notification->getLink());
    }

}
