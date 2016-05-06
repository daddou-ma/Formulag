<?php
require_once('app/functions.php');
require_once('controller/user_controller.php');
require_once('model/user.php');


//UserController::authenticate("Guest","guest");
UserController::logout();
if (UserController::is_auth())
{
    echo User::get_user_by_id(Session::get_id())->email;
}
else {
     echo "You're not connected";
}

?>
