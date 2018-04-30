<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 4/30/18
 * Time: 3:33 PM
 */

namespace App\Model;

use App\Entity\Notification;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Team;
use App\Repository\NotificationConstants;

class NotificationModel
{

    // mozna posilat jen idcko teamu?
    public function teamAdd(User $user, Team $team)
    {
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::TEAM_ADD);
        $notification = $this->setLinkToTeam($notification, $team->getId());
        save($notification);
    }

    public function teamDelete(User $user, Team $team)
    {
        // TODO Link to where? the team is already deleted, in link name of team?
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::TEAM_DELETE);
        $notification->setLink($team->getName()/*NotificationConstants::TEAMS*/);
        save($notification);
    }

    public function teamRole(User $user, Team $team)
    {
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::TEAM_ROLE);
        $notification = $this->setLinkToTeam($notification, $team->getId());
        save($notification);
    }

    public function commment(User $user, Project $project, Task $task)
    {
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::COMMENT);
        $notification = $this->setLinkToProjectAndTask($notification, $project->getId(), $task->getId());
        save($notification);
    }

    public function work(User $user, Project $project, Task $task)
    {
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::WORK);
        $notification = $this->setLinkToProjectAndTask($notification, $project->getId(), $task->getId());
        save($notification);
    }

    public function close(User $user, Project $project, Task $task)
    {
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::CLOSE);
        $notification = $this->setLinkToProjectAndTask($notification, $project->getId(), $task->getId());
        save($notification);
    }

    public function reOpen(User $user, Project $project, Task $task)
    {
        $notification = $this->prepareNotification($user);
        $notification->setType(NotificationConstants::REOPEN);
        $notification = $this->setLinkToProjectAndTask($notification, $project->getId(), $task->getId());
        save($notification);

    }

    private function setLinkToTeam(Notification $notification, $teamId) {
        $notification->setLink(NotificationConstants::TEAMS . $teamId);
        return $notification;
    }

    private function setLinkToProjectAndTask(Notification $notification, $projectId, $taskId) {
        $notification->setLink(NotificationConstants::PROJECTS . $projectId . NotificationConstants::TASKS . $taskId;
        return $notification;
    }

    private function save(Notification $notification) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($notification);
        $em->flush();
    }

    /**
     * @return \DateTime
     */
    public static function setDateTime()
    {
        $dateTime = new \DateTime('now');;
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $dateTime;
    }

    /**
     * @param User $user
     * @return Notification
     */
    private function prepareNotification(User $user) {
        $notification = new Notification();
        $notification->setDate(NotificationModel::setDateTime());
        $notification->setShow(true);
        $notification->setUser($user);
        return $notification;
    }

}