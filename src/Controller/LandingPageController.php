<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LandingPageController extends Controller
{
    /**
     * Render Landig page view
     * @return Response
     * @Route("/", name="landing_page")
     */
    public function index()
    {
        if ($this->getUser()) {

            if($this->getUser()->getAutoSync()){
                return $this->redirectToRoute("sync_export_dash");

            }

            return $this->redirectToRoute('dashboard');
        }
        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController',
        ]);
    }
}
