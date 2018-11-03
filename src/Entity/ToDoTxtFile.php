<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ToDoTxtFile
{

    /**
     * @Assert\NotBlank(message="Please, upload the file.")
     * @Assert\File(mimeTypes={"text/plain"}, maxSize = "512k")
     */
    private $file;

    /**
     * @return UploadedFile|null
     */
    public function getFile() {
        return $this->file;
    }
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }
}