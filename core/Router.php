<?php


class Router
{


    protected $routes = [

        'GET' => [],


        'POST' => []

    ];



    public function get($uri, $controller)
    {


        $this->routes['GET'][$url] = $controller;
    
    
    }


    public function post($uri, $controller)
    {


        $this->routes['POST'][$uri] = $controller;
    
    
    }



    public static function load($file)
    {

        $router = new static;
        
        
        require $file;


        return $router;
    }



    public function direct($url,  $requestType)
    {

        /* 
        *   Search through the defined routes 
        *
        *   if uri exists direct it to the coresponding controller 
        *
        */


        if(array_key_exists($url, $this->routes[$requestType]))
        {


            return $this->routes[$requestType][$url];

        }


        Throw new Exception('Route not defined for this URI');
    }





}