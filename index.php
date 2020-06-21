<?php



/* 
*   Load application bootstrap file
*/
$query = require 'core/bootstrap.php';



require Router::load('routes.php')

    ->direct(Request::url(), Request::method());
