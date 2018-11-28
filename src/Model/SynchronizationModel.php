<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 27/11/2018
 * Time: 16:40
 */

namespace App\Model;


use Google_Client;
use Google_Service_Drive;

class SynchronizationModel
{
    private $client;

    private $drive;

    public function __construct($path)
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig($path);
        $this->client->addScope(array(Google_Service_Drive::DRIVE_METADATA, Google_Service_Drive::DRIVE_FILE,
            Google_Service_Drive::DRIVE, "https://www.googleapis.com/auth/apps.order"));
        if (isset($_SESSION['access_token_drive']) && $_SESSION['access_token_drive']) {
            $this->client->setAccessToken($_SESSION['access_token_drive']);
            $this->drive = new Google_Service_Drive($this->client);
        } else {
            var_dump("redirect");
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/sync/auth';
            var_dump($redirect_uri);
            header('Location: http://localhost:8000/sync/auth');
//            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
    }

    public function makeFile()
    {
        $optParams = array(
            'name' => 'Test'
        );


        $this->service->files->create($optParams);
    }

    public function getFiles() {
        $files = $this->drive->files->listFiles(array())->getItems();
        echo json_encode($files);
    }

}