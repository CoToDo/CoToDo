<?php

namespace App\Controller;

use App\Entity\ToDoTxtFile;
use App\FlashMessages;
use App\Model\ImportModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends Controller {
    const WRONG = 'wrong';
    const MIME_TYPE_TEXT_PLAIN = "text/plain";

    /**
     * @Route("/import", name="import")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function index(Request $request, ManagerRegistry $doctrine) {
        return $this->importElaboration($request, $doctrine, false);
    }

    /**
     * @Route("/import/error", name="import_error")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function importError(Request $request, ManagerRegistry $doctrine) {
        return $this->importElaboration($request, $doctrine, true);
    }

    private function importElaboration(Request $request, ManagerRegistry $doctrine, bool $showError) {
        $ToDoTxtFile = new ToDoTxtFile();
        $form = $this->createFormBuilder($ToDoTxtFile)
            ->add('file', FileType::class, array('label' => 'Plain text file (txt)', 'attr' => array('accept' => 'text/plain')))
            ->add('save', SubmitType::class, array('label' => 'Import', 'attr' => array('class' => 'btn btn-large btn-primary')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $ToDoTxtFile->getFile();
            $txtWrongLines = $this->dealWithFile($file, $doctrine, $ToDoTxtFile);
            $this->flashMessagesAfterImport($txtWrongLines);
            return $this->render('import/import.html.twig', [
                self::WRONG => implode("\n", $txtWrongLines)
            ]);
        } else {
            if ($form->isSubmitted()) {
                return $this->redirectToRoute('import_error');
            } else {
                if ($showError) {
                    return $this->render('import/error.html.twig', array(
                        'form' => $form->createView(),
                    ));
                } else {
                    return $this->render('import/index.html.twig', array(
                        'form' => $form->createView(),
                    ));
                }
            }
        }
    }

    /**
     * Transfer file and import it
     * @param UploadedFile $file
     * @param ManagerRegistry $doctrine
     * @param ToDoTxtFile $ToDoTxtFile
     * @return array wrong lines, which couldn't be parsed
     */
    private function dealWithFile($file, $doctrine, $ToDoTxtFile) {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('file_directory'), $fileName);
        $ToDoTxtFile->setFile($fileName);
        $file = new File($this->getParameter('file_directory') . '/' . $ToDoTxtFile->getFile());
        $import = new ImportModel($doctrine->getManager(), $this->getUser());
        return $import->importFromFile($file->getPath() . "/" . $ToDoTxtFile->getFile());
    }

    /**
     * @Route("import/text", name="import_text", methods="POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function importText(Request $request, ManagerRegistry $doctrine) {
        $txtFileData = $request->get('txtFileData');
        $import = new ImportModel($doctrine->getManager(), $this->getUser());
        $txtWrongLines = $import->importFromString($txtFileData);
        $this->flashMessagesAfterImport($txtWrongLines);
        return $this->render('import/import.html.twig', [
            self::WRONG => implode("\n", $txtWrongLines)
        ]);
    }

    /**
     * Setup flash message in order of wrong parsed line
     * @param array $txtWrongLines
     */
    private function flashMessagesAfterImport($txtWrongLines) {
        if (empty($txtWrongLines)) {
            $this->addFlash(
                FlashMessages::SUCCESS,
                'Your changes were saved!'
            );
        } else {
            $this->addFlash(
                FlashMessages::WARNING,
                'Some lines couldn\'t be proccesed!'
            );
        }
    }
}
