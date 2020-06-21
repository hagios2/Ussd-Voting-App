<?php


class QueryBuilder 
{

    protected $pdo; 


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    
    
    public function select($sql, $parameters)
    {

        try
        {
            $statement = $this->pdo->prepare($sql);


            $statement->execute($parameters);


            return $statement->fetchAll(PDO::FETCH_CLASS);


        }catch(Exception $e){

            die('Whoops, somthing went wrong');

        }

    }



    public function insert($sql, $parameters)
    {
        
        try{

            $statement = $this->pdo->prepare($sql);


            $statement->execute($parameters);

        
        }catch(Exception $e) {


           die('Whoops, somthing went wrong');

        }

    

     /*    return $statement->fetchAll(PDO::FETCH_CLASS); */

    }


   public function insertSessionMsidn($parameters)
    {
        $sql = sprintf(
            
            "INSERT INTO session_manager_table (msidn) VALUES (%s)",  ':'.key($parameters)
        );


        $this->insert($sql, $parameters);


       
    }



    public function selectSessionState($parameters)
    {


        $sql = sprintf(
            
                "SELECT count(msidn), count(transaction_type), count(T1), count(T2), count(T3), count(T4)
                
                FROM session_manager_table WHERE %s = %s", implode(', ', array_keys($parameters)), ':'.implode(', :', array_keys($parameters))
                
            );

        $results = $this->select($sql, $parameters);

       $sum = 0;

       foreach($results[0] as $key => $value)
       {
            $sum += $value;
       }

       return $sum;

    }





    public function getTransactionType($msidn)
    {

        $sql = "SELECT transaction_type FROM session_manager_table WHERE msidn = (:msidn)";



        return $this->select($sql, $msidn);

    }




    public function updateSessionTransanctionType($parameters)
    {
        $sql = sprintf(
            
            "UPDATE session_manager_table SET %s = %s WHERE %s = %s", implode(',', array_keys($parameters)), ':'.implode(', :', array_keys($parameters))
        );
        


        $this->insert($sql, $parameters);


       
    }





    public function updateSessionTFields($parameters)
    {
        $sql = sprintf(
            
            "UPDATE session_manager_table SET %s = %s WHERE msidn = %s", implode(', :', array_keys($parameters)), ':'.implode(', :', array_keys($parameters))
        );


        $this->insert($sql, $parameters);


       
    }








/*     public function updateSessionState($parameters)
    {

        array_keys();

        array_v        
        sprintf(INSERT INTO )
        $statement = $this->pdo->prepare("INSERT INTO  * FROM {$table}");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);

    } */

}