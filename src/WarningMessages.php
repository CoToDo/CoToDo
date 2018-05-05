<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 4/24/18
 * Time: 7:06 PM
 */

namespace App;


abstract class WarningMessages
{
    const WARNING_USER = "User has is already assigned to this task!";
    const ADMIN_TO_LEADER = "Admin can't promote anyone to leader!";

}