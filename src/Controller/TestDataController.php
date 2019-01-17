<?php

namespace App\Controller;

use App\Model\TestModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestDataController extends Controller  {
    /**
     * @Route("/test/data", name="test_data")
     * @Security("has_role('ROLE_USER')")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(UserPasswordEncoderInterface $passwordEncoder) {
        $em = $this->getDoctrine()->getManager();
        $test = new TestModel($em, $passwordEncoder);
        $message = $test->addTestingData();
        return $this->render('test_data/index.html.twig', [
            'message' => $message,
        ]);
    }
}
