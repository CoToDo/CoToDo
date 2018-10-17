<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

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