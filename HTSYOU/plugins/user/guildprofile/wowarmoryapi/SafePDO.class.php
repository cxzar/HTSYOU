<?php
Class SafePDO extends PDO {
 /*
        public static function exception_handler($exception) {
            // Output the exception details
            die('Uncaught exception: '. $exception->getMessage());
        }
 */
        public function __construct() {
/*
            // Temporarily change the PHP exception handler while we . . .
            set_exception_handler(array(__CLASS__, 'exception_handler'));
*/
            // . . . create a PDO object
            $driver = $GLOBALS['wowarmory']['db']['driver'];
            $host = $GLOBALS['wowarmory']['db']['hostname'];
            $dbname = $GLOBALS['wowarmory']['db']['dbname'];
            $username = $GLOBALS['wowarmory']['db']['username'];
            $password = $GLOBALS['wowarmory']['db']['password'];
            $dsn = $driver.':host='.$host.';dbname='.$dbname;
            parent::__construct($dsn, $username, $password);

            // Change the exception handler back to whatever it was before
            restore_exception_handler();
        }

}
?>