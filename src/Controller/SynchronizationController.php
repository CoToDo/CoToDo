<?php

namespace App\Controller;

use App\Model\SynchronizationModel;
use Google_Client;
use Google_Service_Drive;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SynchronizationController extends Controller
{
    /**
     * @Route("/sync", name="synchronization")
     */
    public function index() {
        $client = new Google_Client();
        $client->setAuthConfig("/home/privrja/CoToDo/client_secret.json");
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, "https://www.googleapis.com/auth/apps.order"));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $client->setAccessToken($_SESSION['access_token_drive']);
            $drive = new Google_Service_Drive($client);
            $optParams = array(
                'pageSize' => 10,
                'fields' => 'nextPageToken, files(id, name)'
            );
            $files = $drive->files->listFiles($optParams);
            if (count($files->getFiles()) == 0) {
                print "No files found.\n";
            } else {
                print "Files:\n";
                foreach ($files->getFiles() as $file) {
                    printf("%s (%s)\n", $file->getName(), $file->getId());
                }
            }
        } else {
            return $this->redirectToRoute("sync_auth");
        }
        return $this->render('synchronization/index.html.twig', [
            'controller_name' => 'synchronizationcontroller',
        ]);
    }

    /**
     * @Route("/sync/auth", name="sync_auth")
     */
    public function auth() {
        $client = new Google_Client();
        $client->setAuthConfig("/home/privrja/CoToDo/client_secret.json");
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/sync/auth');
        $client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, "https://www.googleapis.com/auth/apps.order"));

        if (!isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            return $this->redirect(filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token_drive'] = $client->getAccessToken();
            return $this->redirectToRoute("synchronization");
        }
    }

}
