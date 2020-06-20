<?php


class Router
{


    protected $routes = [];



    public static function load($file)
    {

        $router = require $file;


        //


        return $router;
    }


    public function define($routes)
    {

        $this->routes = $routes;
    }


    public function function($url)
    {


        if(array_key_exists($url, $this->routes))
        {
            

            return $this->routes[$url];

        }


        Throw new Exception('Route not found');
    }





}