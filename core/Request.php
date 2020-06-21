<?php


class Request
{


    public static function url()
    {

        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    }



    public static function method()
    {

        return $_SERVER['REQUEST_METHOD'];


    }



    public function has($key, $request_array)
    {

        if(array_key_exists($key, $request_array))
        {

            return $request_array[$key];

        }


        return [];
    }

}