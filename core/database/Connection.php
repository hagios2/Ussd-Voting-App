<?php


class Connection 
{
    
    public static function make($config)
    {

        try {

            return new PDO(

                $config['connection'].';dbname='.$config['dbname'], 
                
                $config['username'],
                
                $config['password'],

                $config['options']
              
            );

            echo 'connection extablished';
       
        } catch(PDOException $e) {

            die($e->getMessage());
        }
    }
}