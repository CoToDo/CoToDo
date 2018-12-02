<?php

namespace App\Controller;

use App\Model\ExportModel;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class SynchronizationController extends Controller
{
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
        $folderId = null;
        $fileId = null;
        $client = new Google_Client();
        $client->setAuthConfig($this->defaultPath . self::CREDENTIALS_JSON);
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, self::GOOGLE_SERVICE_DRIVE_ORDER));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $client->setAccessToken($_SESSION['access_token_drive']);
            $drive = new Google_Service_Drive($client);

            $exportModel = new ExportModel();
            print $exportModel->exportUser($this->getUser());
//            $optParams = array(
//                'pageSize' => 10,
//                'fields' => 'nextPageToken, files(id, name)'
//            );
//
//            $files = $drive->files->listFiles($optParams);
//            if (count($files->getFiles()) == 0) {
////                print "No files found.\n";
//            } else {
////                print "Files:\n";
//                foreach ($files->getFiles() as $file) {
//                    if($file->getName() == "CoToDo"){
//                        $folderId = $file->getId();
////                        printf("%s (%s)\n", $file->getName(), $file->getId());
//                    }
//                    //printf("%s (%s)\n", $file->getName(), $file->getId());
//                }
//            }
//
//            if($folderId == null){
//                $folderMetadata = new Google_Service_Drive_DriveFile(array(
//                    'name' => 'CoToDo',
//                    'mimeType' => 'application/vnd.google-apps.folder'));
//
//                $folderId = $drive->files->create($folderMetadata, array(
//                    'fields' => 'id'))->getId();
//
//            }
//
//
//            foreach ($files->getFiles() as $file) {
//                if($file->getName() == "todo.txt"){
//                    $parents = $drive->files->get($file->getId(), array('fields' => 'parents'));
//
//                    foreach ($parents->parents as $parent) {
////                        print $parent;
//                        if($parent == $folderId) $fileId = $file->getId();
//                    }
//
//                }
//            }
//
//            if($fileId == null){
//                $exportModel = new ExportModel();
//                 $content = $exportModel->exportUser($this->getUser());
//
//                $fileMetadata = new Google_Service_Drive_DriveFile(array(
//                    'name' => 'todo.txt',
//                    'parents' => array($folderId)
//                ));
//
//                $drive->files->create($fileMetadata, array(
//                    'data' => $content,
//                    'fields' => 'id'))->getId();
//            }


        } else {
            return $this->redirectToRoute("sync_auth");
        }
        return $this->render('synchronization/index.html.twig', [
            'controller_name' => 'SynchronizationController',
        ]);
    }

    /**
     * @Route("/sync/auth", name="sync_auth")
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
     */
    public function export() {
        $this->getDefaultPath();
        $folderId = null;
        $fileId = null;
        $client = new Google_Client();
        $client->setAuthConfig($this->defaultPath . self::CREDENTIALS_JSON);
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, self::GOOGLE_SERVICE_DRIVE_ORDER));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $client->setAccessToken($_SESSION['access_token_drive']);
            $drive = new Google_Service_Drive($client);

            $optParams = array(
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
            );

            $files = $drive->files->listFiles($optParams);
            if (count($files->getFiles()) == 0) {
//                print "No files found.\n";
            } else {
//                print "Files:\n";
                foreach ($files->getFiles() as $file) {
                    if($file->getName() == "CoToDo"){
                        $folderId = $file->getId();
//                        printf("%s (%s)\n", $file->getName(), $file->getId());
                    }
                    //printf("%s (%s)\n", $file->getName(), $file->getId());
                }
            }

            if($folderId == null){
                $folderMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => 'CoToDo',
                    'mimeType' => 'application/vnd.google-apps.folder'));

                $folderId = $drive->files->create($folderMetadata, array(
                    'fields' => 'id'))->getId();

            }


            foreach ($files->getFiles() as $file) {
                if($file->getName() == "todo.txt"){
                    $parents = $drive->files->get($file->getId(), array('fields' => 'parents'));

                    foreach ($parents->parents as $parent) {
//                        print $parent;
                        if($parent == $folderId) $fileId = $file->getId();
                    }

                }
            }


            $exportModel = new ExportModel();
            $content = $exportModel->exportUser($this->getUser());

            if($fileId == null){


                $fileMetadata = new Google_Service_Drive_DriveFile(array(
                    'name' => 'todo.txt',
                    'parents' => array($folderId)
                ));

                $drive->files->create($fileMetadata, array(
                    'data' => $content,
                    'fields' => 'id'))->getId();
            } else {
                $emptyFile = new Google_Service_Drive_DriveFile();

//                print $content;
                $drive->files->update($fileId, $emptyFile, array(
                    'data' => $content));
            }

        } else {
            return $this->redirectToRoute("sync_auth");
        }
        return $this->redirectToRoute("synchronization");
    }

}
