<?php



/* 
*   Load application bootstrap file
*/
$query = require 'core/bootstrap.php';



var_dump(Request::url());
/*
*    
*/
require Router::load('routes.php')

    ->direct(Request::url());
