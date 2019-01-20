<?php

namespace App\Controller;

use App\Model\ImportModel;
use App\FlashMessages;
use App\Model\ExportModel;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class SynchronizationController extends Controller
{

    const WRONG = 'wrong';
    const CREDENTIALS_JSON = "/credentials.json";
    const GOOGLE_SERVICE_DRIVE_ORDER = "https://www.googleapis.com/auth/apps.order";
    private $defaultPath;

    private function getDefaultPath() {
        $this->defaultPath = $this->get('kernel')->getRootDir();
        $this->defaultPath = substr($this->defaultPath, 0, -3);
    }

    /**
     * @Route("/sync", name="synchronization")
     * @Security("has_role('ROLE_USER')")
     */
    public function index() {
        $this->getDefaultPath();
        $client = new Google_Client();
        $client->setAuthConfig($this->defaultPath . self::CREDENTIALS_JSON);
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, self::GOOGLE_SERVICE_DRIVE_ORDER));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $client->setAccessToken($_SESSION['access_token_drive']);


        } else {
            return $this->redirectToRoute("sync_auth");
        }
        return $this->render('synchronization/index.html.twig', [
            'controller_name' => 'SynchronizationController',
        ]);
    }

    /**
     * @Route("/sync/auth", name="sync_auth")
     * @Security("has_role('ROLE_USER')")
     */
    public function auth() {
        $this->getDefaultPath();
        $client = new Google_Client();
        $client->setAuthConfig($this->defaultPath . self::CREDENTIALS_JSON);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/sync/auth');
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, self::GOOGLE_SERVICE_DRIVE_ORDER));

        if (!isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            return $this->redirect(filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token_drive'] = $client->getAccessToken();
            return $this->redirectToRoute("synchronization");
        }
    }

    /**
    * @Route("/sync/export", name="sync_export")
    * @Security("has_role('ROLE_USER')")
    */
    public function export(Request $request) {
        if ($this->exportHelper()){
            return $this->redirect($request->headers->get('referer'));
        } else {
            return $this->redirectToRoute("sync_auth");
        }
    }

    /**
     * @Route("/sync/export/dash", name="sync_export_dash")
     * @Security("has_role('ROLE_USER')")
     */
    public function exportDash(Request $request) {
        if ($this->exportHelper()){
            return $this->redirectToRoute("dashboard");
        } else {
            return $this->redirectToRoute("sync_auth");
        }
    }

    public function exportHelper() {
        $this->getDefaultPath();
        $cotodo = null;
        $client = new Google_Client();
        $client->setAuthConfig($this->defaultPath . self::CREDENTIALS_JSON);
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, self::GOOGLE_SERVICE_DRIVE_ORDER));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $client->setAccessToken($_SESSION['access_token_drive']);
            $drive = new Google_Service_Drive($client);

            $optParamsCotodo = array(
                'q' => "name='CoToDo'",
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
            );


            $cotodoFiles = $drive->files->listFiles($optParamsCotodo);
            if (count($cotodoFiles->getFiles()) == 0) {
                $folderMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => 'CoToDo',
                    'mimeType' => 'application/vnd.google-apps.folder'));

                $cotodo = $drive->files->create($folderMetadata, array(
                    'fields' => 'id'));
            } else if (count($cotodoFiles->getFiles()) == 1) {
                $cotodo = $cotodoFiles->getFiles()[0];
            } else {
                $this->addFlash(
                    FlashMessages::INFO,
                    'Too much files found! Check your drive and keep just one CoToDo folder!'
                );

                return $this->redirectToRoute("synchronization");
            }

            $optParamsTodo = array(
                'q' => "'".$cotodo->getId(). "' in parents and name='todo.txt'",
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
            );

            $todoFiles = $drive->files->listFiles($optParamsTodo);

            $exportModel = new ExportModel();
            $content = $exportModel->exportUser($this->getUser());

            if (count($todoFiles->getFiles()) == 0) {

                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => 'todo.txt',
                    'parents' => array($cotodo->getId())
                ));

                $drive->files->create($fileMetadata, array(
                    'data' => $content,
                    'fields' => 'id'))->getId();

            } else if (count($todoFiles->getFiles()) == 1) {
                $emptyFile = new Google_Service_Drive_DriveFile();

                $drive->files->update($todoFiles->getFiles()[0]->getId(), $emptyFile, array(
                    'data' => $content));
            } else {
                $this->addFlash(
                    FlashMessages::INFO,
                    'Too much todo.txt files found! Check your drive and keep just one todo.txt file in CoToDo folder!'
                );

            }

        } else {
            return false;
        }
        return true;
    }

    /**
     * @Route("/sync/import", name="sync_import")
     * @Security("has_role('ROLE_USER')")
     * @param ManagerRegistry $doctrine
     */
    public function import(ManagerRegistry $doctrine) {
        $this->getDefaultPath();
        $cotodo = null;
        $client = new Google_Client();
        $client->setAuthConfig($this->defaultPath . self::CREDENTIALS_JSON);
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, self::GOOGLE_SERVICE_DRIVE_ORDER));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $client->setAccessToken($_SESSION['access_token_drive']);
            $drive = new Google_Service_Drive($client);

            $optParamsCotodo = array(
                'q' => "name='CoToDo'",
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
            );


            $cotodoFiles = $drive->files->listFiles($optParamsCotodo);
            if (count($cotodoFiles->getFiles()) == 0) {
                $this->addFlash(
                    FlashMessages::INFO,
                    'Nothing to import! Files for import must in CoToDo folder and be named todo.txt!'
                );
                return $this->redirectToRoute("synchronization");
            } else if (count($cotodoFiles->getFiles()) == 1) {
                $cotodo = $cotodoFiles->getFiles()[0];
            } else {
                $this->addFlash(
                    FlashMessages::INFO,
                    'Too much files found! Check your drive and keep just one CoToDo folder!'
                );

                return $this->redirectToRoute("synchronization");
            }

            $optParamsTodo = array(
                'q' => "'".$cotodo->getId(). "' in parents and name='todo.txt'",
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
            );

            $todoFiles = $drive->files->listFiles($optParamsTodo);

            if (count($todoFiles->getFiles()) == 0) {
                $this->addFlash(
                    FlashMessages::INFO,
                    'Nothing to import! Files for import must in CoToDo folder and be named todo.txt!'
                );
                return $this->redirectToRoute("synchronization");

            } else if (count($todoFiles->getFiles()) == 1) {
                $data = null;
                $file = $drive->files->get($todoFiles->getFiles()[0]->getId(), array('alt' => 'media'));

                $importModel = new ImportModel($doctrine->getManager(), $this->getUser());

                $txtWrongLines = $importModel->importFromString($file->getBody()->getContents());
                $this->flashMessagesAfterImport($txtWrongLines);
                return $this->render('import/import.html.twig', [
                    self::WRONG => implode("\n", $txtWrongLines)
                ]);

            } else {
                $this->addFlash(
                    FlashMessages::INFO,
                    'Too much todo.txt files found! Check your drive and keep just one todo.txt file in CoToDo folder!'
                );

            }

        } else {
            return $this->redirectToRoute("sync_auth");
        }
        return $this->redirectToRoute("synchronization");
    }

    /**
     * @Route("/sync/auto", name="sync_auto")
     * @Security("has_role('ROLE_USER')")
     * @param ManagerRegistry $doctrine
     */
    public function auto(){
        $user=$this->getUser()->setAutoSync(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute("synchronization");
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
