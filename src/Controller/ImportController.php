<?php

namespace App\Controller;

use App\Entity\ToDoTxtFile;
use App\Model\ImportModel;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TeamRepository;
use App\Repository\WorkRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Runner\Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends Controller {
    const CONTROLLER_NAME = "controller_name";
    const IMPORT_CONTROLLER = "ImportController";
    const WRONG = 'wrong';
    const MIME_TYPE_TEXT_PLAIN = "text/plain";

    /**
     * @Route("/import", name="import")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    public function index(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) {
        $ToDoTxtFile = new ToDoTxtFile();
        $form = $this->createFormBuilder($ToDoTxtFile)
            ->add('file', FileType::class, array('label' => 'Plain text file (txt)', 'attr' => array('accept' => 'text/plain')))
            ->add('save', SubmitType::class, array('label' => 'Import', 'attr' => array('class' => 'btn btn-large btn-primary')))
            ->getForm();

        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
//                $logger->info('form OK');
                $file = $ToDoTxtFile->getFile();
//                $logger->info($file->getClientSize() . " size");
//                var_dump($file->getClientOriginalExtension() . " " . $file->getClientMimeType());
                if ($file->getClientMimeType() !== self::MIME_TYPE_TEXT_PLAIN || $file->getClientOriginalExtension() !== 'txt') {
//                    $logger->info('wrong mime, or extension ');
                    $this->flashMessageWrongMimeType();
                    return $this->renderPageWithErrorBeforeImport($form);
                }
//                var_dump($file->getClientOriginalExtension() . " " . $file->getClientMimeType());
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('file_directory'), $fileName);
                $ToDoTxtFile->setFile($fileName);
                $file = new File($this->getParameter('file_directory') . '/' . $ToDoTxtFile->getFile());
                $import = new ImportModel($doctrine->getManager(), $this->getUser());
                $txtWrongLines = $import->importFromFile($file->getPath() . "/" . $ToDoTxtFile->getFile());
                $this->flashMessagesAfterImport($txtWrongLines);
//                $logger->info('after import');
                return $this->render('import/import.html.twig', [
                    self::CONTROLLER_NAME => self::IMPORT_CONTROLLER,
                    self::WRONG => implode("\n", $txtWrongLines)
                ]);
            } else {
                /* TODO some bug in symfony or php, with memory size. trying to upload file when form is not valid, so there is need to redirect and not show flash messages, wtf
                    trying setup memory in kernel, but not work, only for some inputs ...
                    ini_set('post_max_size', '3M');
                    ini_set('memory_limit', '512M'); */
//                $logger->info('form not submited|not');
                if (!$form->isSubmitted()) {
//                    $logger->info('form not submited');
                    return $this->renderPageWithErrorBeforeImport($form);
                } else {
//                    $logger->info('form not valid');
                    return $this->redirectToRoute('import');
                }
            }
        } catch (\Exception $exception) {
            $logger->info('exception');
            $this->flashMessageException($exception);
            return $this->renderPageWithErrorBeforeImport($form);
        }
    }

    private function renderPageWithErrorBeforeImport($form) {
        return $this->render('import/index.html.twig', [
            self::CONTROLLER_NAME => self::IMPORT_CONTROLLER,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("import/text", name="import_text", methods="POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function importText(Request $request, ManagerRegistry $doctrine) {
        $txtFileData = $request->get('txtFileData');
        $import = new ImportModel($doctrine->getManager(), $this->getUser());
        $txtWrongLines = $import->importFromString($txtFileData);
        $this->flashMessagesAfterImport($txtWrongLines);
        return $this->render('import/import.html.twig', [
            self::CONTROLLER_NAME => self::IMPORT_CONTROLLER,
            self::WRONG => implode("\n", $txtWrongLines)
        ]);
    }

    private function flashMessagesAfterImport($txtWrongLines) {
        if (empty($txtWrongLines)) {
            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
        } else {
            $this->addFlash(
                'warning',
                'Some lines couldn\'t be proccesed!'
            );
        }
    }

    private function flashMessageWrongMimeType() {
        $this->addFlash(
            'warning',
            'Wrong mime type!'
        );
    }

    private function flashMessageException($ex) {
        $this->addFlash(
            'warning',
            "Exception! " . $ex->getMessage()
        );
    }
}
