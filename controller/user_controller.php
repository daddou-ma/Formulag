<?php
require_once('app/functions.php');
require_once('model/user.php');
require_once('controller/session_controller.php');

class UserController {
    public static function authenticate($email, $pass) {
        $user = User::get_user_by_email($email);

        if ($pass == $user->password)
        {
            User::set_token($user->_id, Functions::generate_token());
            Session::create_session($user->_id, User::get_token($user->_id));
        }
    }

    public static function is_auth() {
        if ((Session::get_id() != null) && (Session::get_token() != null))
        {
            if (User::get_token(Session::get_id()) == Session::get_token())
            {
                return true;
            }
            else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function logout() {
        Session::delete_session();
    }
}
?>
