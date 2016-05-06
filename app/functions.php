<?php
require_once('config/config.inc.php');

class Functions {

    /*
    * Function to get handle to the database
    * Configuration of database are in the config/config.inc.php file
    */
    function db_connect() {
        $link = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" .DB_NAME . " user=" . DB_USER . " password=" . DB_PASS)
          or die('Could not connect: ' . pg_last_error());
        return $link;
    }

    /*
    * Generate Access Token to login
    */
    function generate_token() {
    	return bin2hex(openssl_random_pseudo_bytes(16));
    }
}
?>
