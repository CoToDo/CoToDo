<?php
/**
 * Created by PhpStorm.
 * User: jenik
 * Date: 4/24/18
 * Time: 7:06 PM
 */

namespace App;


abstract class WarningMessages  {
    const WARNING_USER = "User has is already assigned to this task!";
    const WARNING_PROJECT_NAME_USED = "A project with this name already exists! Choose different one.";
    const ADMIN_TO_LEADER = "Admin can't promote anyone to leader!";
    const WARNING_PROJECT_UNDERSCORE = "Underscres are disabled!";

}