<?php


class Router
{


    protected $routes = [];



    public static function load($file)
    {

        $router = new static;
        
        
        require $file;


        return $router;
    }


    public function define($routes)
    {

        $this->routes = $routes;
    }




    public function direct($url)
    {

        /* 
        *   Search through the defined routes 
        *
        *   if uri exists direct it to the coresponding controller 
        *
        */


        if(array_key_exists($url, $this->routes))
        {


            return $this->routes[$url];

        }


        Throw new Exception('Route not found');
    }





}