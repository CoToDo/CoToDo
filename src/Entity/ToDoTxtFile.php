<?php
/**
 * Created by PhpStorm.
 * User: Kiki
 * Date: 17. 10. 2018
 * Time: 13:30
 */

namespace App\Entity;



class ToDoTxtFile
{

    /**
     * @Assert\NotBlank(message="Please, upload the file.")
     * @Assert\File(mimeTypes={ "text/plain"})
     */
    private $file;
    public function getFile() {
        return $this->file;
    }
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }
}