<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 24/10/2018
 * Time: 11:19
 */

namespace App\Model;

use App\Entity\Task;
use App\Model\ICS;


class DownloadModel
{
    public function downloadIcs(Task $task, $url) {
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename=task.ics');

        $ics = new ICS($task->getDeadline(), $task->getName(), $task->getPriority(), $url);

        echo $ics->getFile();
    }
}