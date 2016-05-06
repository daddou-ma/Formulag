<?php
require_once('app/functions.php');

/*
* The User Model - > table = users
*/
class User {
    // user Data
    public $_id;
    public $email;
    private $password;
    public $first_name;
    public $last_name;
    public $photo;

    // database table
    private static $table = "users";

    /*
    * Empty Constructor
    */
    public function __construct() {

    }

    /*
    * get user by his id from databse
    */
    public static function get_user_by_id($id) {
      $function_name = "get_user_by_id";
        try {
            // SQL REQUEST PART
            $hdb = db_connect();
            $query = "SELECT * FROM ". self::$table ." WHERE _id = $1";
            $statement = pg_prepare($hdb, $function_name, $query);
            $statement = pg_execute($hdb, $function_name, array($id));
            pg_query("deallocate " . $function_name);
            // verifing number of rows
            $num_rows = pg_num_rows($statement);
            if($num_rows == 0)
            {
                echo "User does not exist";
                return null;
            }
            else if($num_rows > 1) {
                echo "There is many users with this id, Contact the Technical Support";
                return null;
            }
            else {
                // Creating the User Object
                $result = pg_fetch_object($statement);
                $user = new self();
                $user->wrap($result);
                return $user;
            }

            // close db handle & deallocate the query
            pg_close($hdb);

        } catch (Exception $e) {
            die("Internal Error");
        }
    }

    /*
    * Used for Authentication
    */
    public static function get_pass_by_email($email) {
        $function_name = "get_pass_by_email";
        try {
            $hdb = db_connect();
            $query = "SELECT password FROM " . self::$table . " WHERE email = $1";
            $statement = pg_prepare($hdb, $function_name, $query);
            $statement = pg_execute($hdb, $function_name, array($email));

            $num_rows = pg_num_rows($statement);
            if($num_rows == 0)
            {
                echo "User Does not Exist";
                return null;
            }
            else if ($num_rows > 1)
            {
                echo "There is many user with this email : " . $num_rows;
                return null;
            }
            else {
                $result = pg_fetch_object($statement);
                return $result->password;
            }

            // Close db handle
            pg_close($hdb);

        } catch (Exception $e) {
            die("Internal Error");
        }
    }

    /*
    * Verify if the email exist
    */
    public static function email_free($email) {
      $function_name = "email_free";
      try {
          $hdb = db_connect();
          $query = "SELECT password FROM " . self::$table . " WHERE email = $1";
          $statement = pg_prepare($hdb, $function_name, $query);
          $statement = pg_execute($hdb, $function_name, array($email));

          $num_rows = pg_num_rows($statement);
          if($num_rows == 0)
          {
              echo "This email is free";
              return true;
          }
          else {
              return false;
          }
    }
    /*
    * Create new User
    */
    public static function create($email, $password, $first_name, $last_name, $photo) {

        if (self::email_free($email))
        {
            $user = new self();

            $user->_id         = -1;
            $user->email       = $email;
            $user->password    = $password;
            $user->first_name  = $first_name;
            $user->last_name   = $last_name;
            $user->photo       = $photo;

            return $user;
        } else {
           return null;
        }
   }

    /*
    * Function to fill the User object
    */
    public function wrap($user) {
        $this->_id         = $user->_id;
        $this->email       = $user->email;
        $this->password    = $user->password;
        $this->first_name  = $user->first_name;
        $this->last_name   = $user->last_name;
        $this->photo       = $user->photo;
    }

    /*
    * Save function to save or update User in the database
    */
    public function save() {
        $function_name = "save";

        // if this user does not exist in the database
        if ($this->_id == -1 || self::get_user_by_id($this->_id) == null)
        {
            try {
                $hdb = db_connect();
                $query = "INSERT INTO " . self::$table . " (email, password, first_name, last_name, photo) VALUES ($1, $2, $3, $4, $5)";
                $statement = pg_prepare($hdb, $function_name, $query);
                $statement = pg_execute($hdb, $function_name, $this->to_sql_array());
            } catch(Exception $e) {
                die("Internal Erreur");
            }
        }

        // if the user already exist we update it
        else {
            try {
                $hdb = db_connect();
                $query = "UPDATE " . self::$table . " SET (email, password, first_name, last_name, photo) = ($1, $2, $3, $4, $5)" . " WHERE _id = " . $this->_id;
                $statement = pg_prepare($hdb, $function_name, $query);
                $statement = pg_execute($hdb, $function_name, $this->to_sql_array());
            } catch(Exception $e) {
                die("Internal Erreur");
            }
        }
    }

    /*
    *
    */
    public function delete() {
      $function_name = "delete";

      // there is many user or the user did not exist
      if ($this->_id == -1 || self::get_user_by_id($this->_id) == null)
      {
          echo "We can't delete a user that not exist !";
          return false;
      }

      // if the user already exist we delete it
      else {
          try {
              $hdb = db_connect();
              $query = "DELETE FROM " . self::$table . " WHERE _id = " . $this->_id;
              $statement = pg_prepare($hdb, $function_name, $query);
              $statement = pg_execute($hdb, $function_name, array());
          } catch(Exception $e) {
              die("Internal Erreur");
          }
      }
    }

    /*
    * Convert object into array for the sql request
    */
    public function to_sql_array() {
        return array($this->email, $this->password, $this->first_name, $this->last_name, $this->photo);
    }
}

?>
