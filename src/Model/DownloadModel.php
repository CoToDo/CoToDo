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

        $ics = new ICS(array(
            'description' => "priority " . $task->getPriority(),
            'dtstart' => $task->getCreateDate()->format(('Y-m-d H:i:s')),
            'dtend' => $task->getDeadline()->format(('Y-m-d H:i:s')),
            'summary' => $task->getName(),
            'url' => $url
        ));

        echo $ics->to_string();
    }
}