<?php

/* 
*   Create a config.php file
*
*   Copy this content into config.php and modify to suit your database configs
*/


return [

    'database' => [

        'dbname' => 'database',

        'username' => 'your_db_username',

        'password' => 'your_password',

        'connection' => "mysql:host=localhost; port=3306",

        'options' => [

            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]

    ]


];