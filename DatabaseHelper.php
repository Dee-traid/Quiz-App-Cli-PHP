<?php

class DatabaseHelper{
    private static $pdo = null;

    public static function getPDOInstance(){
        if(self::$pdo != null){
            return self::$pdo;
        }


        $host = "localhost";
        $port = "5432";
        $user = "postgres";
        $password = "Traid101";
        $database = "quizapp";

        $dsn = "pgsql:host=$host;port=$port;dbname=$database;"; 

        try{
            self::$pdo = new PDO($dsn, $user, $password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }catch(PDOException $e){
            echo " Connection failed!!" . $e->getMessage() . PHP_EOL;
        }
        return self::$pdo;

    
    }

    
}

?>