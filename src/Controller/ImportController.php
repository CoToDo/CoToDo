<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\ToDoTxtFile;
use App\Model\ImportModel;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TeamRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends Controller
{
    /**
     * @Route("/import", name="import")
     * @Security("has_role('ROLE_USER')")
     */
    public function index(Request $request)
    {
        $ToDoTxtFile=new ToDoTxtFile();
        $form = $this->createFormBuilder($ToDoTxtFile)
            ->add('file', FileType::class, array('label' => 'Plain text file (txt)'))
            ->add('save', SubmitType::class, array('label' => 'Import', 'attr'=> array('class'=>'btn btn-large btn-primary')))
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

    /**
     * @Route("import/text", name="import_text", methods="POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function importText(Request $request, ManagerRegistry $doctrine, ProjectRepository $projectRepository, TaskRepository $taskRepository, TeamRepository $teamRepository) {
        $txtFileData = $request->get('txtFileData');
        $import = new ImportModel($doctrine, $projectRepository, $taskRepository, $teamRepository, $this->getUser(), "main_project");
        $txtWrongLines = $import->import($txtFileData);

        return $this->render('import/import.html.twig', [
            'controller_name' => 'ImportController',
            'wrong' => implode("\n", $txtWrongLines)
        ]);
    }

}
