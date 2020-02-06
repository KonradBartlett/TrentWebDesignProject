<?php

$direx = explode("/", getcwd());
DEFINE ('DOCROOT', '/'.$direx[1].'/'.$direx[2].'/'); // home/username/
DEFINE ('WEBROOT', '/'.$direx[1].'/'.$direx[2].'/'.$direx[3].'/'); // home/username/public_html/


function & dbconnect(){
   // Load configuration as an array. Use the actual location of your configuration file
    //$config = parse_ini_file(DOCROOT.'config.ini');
    //Note: on loki, you file should be located in the pwd folder (which should be in your user directory)
    $config = parse_ini_file(DOCROOT.'pwd/config.ini');


    //create connection dsn
   $dsn = "mysql:host={$config['domain']};dbname={$config['dbname']};charset={$config['charset']}";

    //set options array for connection
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    //make database object
    try {
        $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    } catch (\PDOException $e) {
         throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    return $pdo;

}

?>
