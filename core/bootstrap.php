<?php


$app = [];


$app['config'] = require 'config.php'; 



require 'core/Router.php';

require 'core/Request.php';

require 'core/database/Connection.php';


$app['database'] = require 'core/database/QueryBuilder.php';


 
return new QueryBuilder(

    Connection::make($app['config']['database'])

);