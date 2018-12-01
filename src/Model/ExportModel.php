<?php

namespace App\Model;

use App\Entity\User;
use Symfony\Component\Filesystem\Filesystem;

class ExportModel
{

    public function downloadExport(User $user){
        header('Content-disposition: attachment; filename=todo.txt');
        header('Content-type: text/plain');

        echo $this->exportUser($user);
    }

    public function exportUser(User $user) {

        $text = "";

        foreach ($user->getWorks() as $work) {
            if($work->getEndDate() != null) {
                continue;
            }

            $row = "";
            $work->getTask()->getCompletionDate(); // x
            if($work->getTask()->getCompletionDate() != null) {
                $row .= "x ";
            }else{
                $row .= "(" . $work->getTask()->getPriority() . ") ";
            }

            if($work->getTask()->getCompletionDate() != null) {
                $row .= $work->getTask()->getCompletionDate()->format('Y-m-d') . " ";
            }
            $row .= $work->getTask()->getCreateDate()->format('Y-m-d') . " ";

            $row .= $work->getTask()->getName() . " ";
            $row .= "+" . str_replace(' ', '_', $work->getTask()->getProject()->getName()) . " "; //+
            foreach ($work->getTask()->getTags() as $tag) {
                $row .= "@" . $tag->getName() . " "; // @
            }
            if($work->getTask()->getCompletionDate() != null) {
                $row .= "pri:" . $work->getTask()->getPriority() . " ";
            }

            $row .= "due:" . $work->getTask()->getDeadline()->format('Y-m-d') . " ";

            $row .= "\n";
            $text .= "$row";
        }

        return $text;
    }

}