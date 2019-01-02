<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class TestDataController extends Controller  {

    /**
     * @Route("/test/data", name="test_data")
     * @Security("has_role('ROLE_USER')")
     */
    public function index() {
        $testData = array();
        $form = $this->createFormBuilder($testData)
            ->add('test', SubmitType::class, array('label' => 'Test'))
            ->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            //TODO add data to DB here

            return $this->render('test_data/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->render('test_data/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
