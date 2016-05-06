<?php
session_start();

class Session {

    public static function create_session($id, $access_token) {
        $_SESSION['_id'] = $id;
        $_SESSION['access_token'] = $access_token;
    }

    public static function get_id() {
        if(self::is_session()) {
            return $_SESSION['_id'];
        } else {
            return null;
        }
    }

    public static function get_token() {
        if (self::is_session()) {
            return $_SESSION['access_token'];
        } else {
            return false;
        }
    }

    public static function delete_session() {
        $_SESSION['_id'] = "";
        $_SESSION['access_token'] = "";
        unset($_SESSION['_id']);
        unset($_SESSION['access_token']);
        session_destroy();
        //TODO: header('Location: index.php');
    }

    public static function is_session() {
        if(isset($_SESSION['_id']) && isset($_SESSION['access_token']))
        {
            return true;
        } else {
            return false;
        }
    }
}
?>
