<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 5/4/18
 * Time: 4:49 PM
 */

namespace App\Model;

use App\Entity\User;
use Symfony\Component\Filesystem\Filesystem;

class ExportModel
{

    public function exportUser(User $user) {
        $myfile = fopen("tmptodo.txt", "w") or die("Unable to open file!");
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
            $row .= "+" . $work->getTask()->getProject()->getName() . " "; //+
            foreach ($work->getTask()->getTags() as $tag) {
                $row .= "@" . $tag->getName() . " "; // @
            }
            if($work->getTask()->getCompletionDate() != null) {
                $row .= "pri:" . $work->getTask()->getPriority() . " ";
            }

            $row .= "due:" . $work->getTask()->getDeadline()->format('Y-m-d') . " ";

            $row .= "\n";
            fwrite($myfile, $row);
        }
        fclose($myfile);
        readfile("tmptodo.txt");

        $fileSystem = new Filesystem();
        $fileSystem->remove("tmptodo.txt");

        header('Content-disposition: attachment; filename=todo.txt');
        header('Content-type: text/plain');

    }

}