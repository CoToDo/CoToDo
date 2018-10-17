<?php

namespace App\Controller;

use App\Entity\ToDoTxtFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends Controller
{
    /**
     * @Route("/import", name="import")
     */
    public function index(Request $request)
    {
        $ToDoTxtFile=new ToDoTxtFile();
        $form = $this->createFormBuilder($ToDoTxtFile)
            ->add('file', FileType::class, array('label' => 'Plain text file (txt)'))
            ->add('save', SubmitType::class, array('label' => 'Submit', 'attr'=> array('class'=>'btn btn-large btn-primary')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $ToDoTxtFile->getFile();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('file_directory'), $fileName);
            $ToDoTxtFile->setFile($fileName);

            $file = new File($this->getParameter('file_directory').'/' . $ToDoTxtFile->getFile());
//            echo $file->getPath() . "/" . $ToDoTxtFile->getFile();

            return $this->render('import/index.html.twig', [
                'controller_name' => 'ImportController',
                'form' => $form->createView(),
            ]);
        } else {
            return $this->render('import/index.html.twig', [
                'controller_name' => 'ImportController',
                'form' => $form->createView(),
            ]);
        }

    }
}
