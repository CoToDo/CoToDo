<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 5/4/18
 * Time: 4:49 PM
 */

namespace App\Model;


use App\Entity\User;

class ExportModel
{

    public function exportUser(User $user) {

        foreach ($user->getWorks() as $work) {
            $row = "";
            $work->getTask()->getCompletionDate(); // x
            if($work->getTask()->getCompletionDate() != null) {
                $row .= "x ";
            }

            $row .= "(" . $work->getTask()->getPriority() . ") ";

            if($work->getTask()->getCompletionDate() != null) {
                $row .= $work->getTask()->getCompletionDate()->format('Y-m-d H:i:s') . " ";
            }
            $row .= $work->getTask()->getCreateDate()->format('Y-m-d H:i:s') . " ";

            $row .= $work->getTask()->getName() . " ";
            $row .= "+" . $work->getTask()->getProject()->getName() . " "; //+
            foreach ($work->getTask()->getTags() as $tag) {
                $row .= "@" . $tag->getName() . " "; // @
            }

//            echo $row . "\r\n";
        }

    }

}