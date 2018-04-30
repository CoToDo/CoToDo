<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 4/30/18
 * Time: 3:33 PM
 */

namespace App\Model;


use App\Entity\Notification;

class NotificationModel
{

    /**
     * @param User $user
     */
    public function teamAdd(User $user)
    {
        $notification = new Notification();
        $notification->setDate(NotificationModel::setDateTime());
        $notification->setShow(true);
        $notification->setUser($user);

    }

    /**
     * @param User $user
     */
    public function teamDelete(User $user)
    {

    }

    /**
     * @param User $user
     */
    public function teamRole(User $user)
    {

    }

    /**
     * @param User $user
     */
    public function commment(User $user)
    {

    }

    /**
     * @param User $user
     */
    public function work(User $user)
    {

    }

    /**
     * @param User $user
     */
    public function close(User $user)
    {

    }

    /**
     * @param User $user
     */
    public function reOpen(User $user)
    {

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

}