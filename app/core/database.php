<?php
namespace app\core;
use \PDO;
use \PDOException;

Class Database {
    /*
    *
    * This is the database class
    */
     
    public static $con;
    
    public function __construct() {
        try {
            $string = DB_TYPE . ":host=" . DB_HOST_NAME . ";dbname=".DB_NAME;
            self::$con = new PDO($string, DB_USER, DB_PASS);

        } catch (PDOException $err) {
            die("Error: " . $err->getMessage());
        }   
    }

    public static function getInstance() {
        if(self::$con) {
            return self::$con;
        }
        
        return $instance = new self();
        // return $a::$con;
    }
    

    /*
    *
    * Read from database
    */

    // Read function
    public function read(string $query, array $data = []) {
        $stm = self::$con->prepare($query, $data);
        $result = $stm->execute($data);

        // check if executed
        if($result) {
            // get dqta
            $data = $stm->fetchAll(PDO::FETCH_OBJ);
            if(is_array($data) && count($data) > 0) {
                return $data;
            }
        }
        return false;

    }


    /*
    *
    * Write to database
    */

    // Write
    public function write(string $query, array $data = []):bool {
        $stm = self::$con->prepare($query);
        show($data);
        $result = $stm->execute($data);

        // check if executed
        if($result) {
            return true;
        }
        return false;

    }
    
}


?>