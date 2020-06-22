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

         /*    die('Whoops, somthing went wrong'); */

            die($e->getMessage());

        }

    }



    public function insert($sql, $parameters)
    {
        
        try{

            $statement = $this->pdo->prepare($sql);


            $statement->execute($parameters);

        
        }catch(Exception $e) {


           die($e->getMessage());

        }

    

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
            
                "SELECT count(msidn), count(transaction_type), count(T1), count(T2), count(T3), count(T4), count(T5), count(T6), count(T7), count(T8), count(T9)
                
                FROM session_manager_table WHERE %s = %s", implode(', ', array_keys($parameters)), ':'.implode(', :', array_keys($parameters))
                
            );

        $results = $this->select($sql, $parameters);

        return array_sum(array_values((array)$results[0]));

 
    }





    public function getTransactionType($msidn)
    {

        $sql = "SELECT transaction_type FROM session_manager_table WHERE msidn = (:msidn)";



        $results = $this->select($sql, $msidn);


        return array_values((array)$results[0])[0];

    }






    public function updateSessionTransanctionType($parameters)
    {
        $sql = sprintf(
            
            "UPDATE session_manager_table SET transaction_type = %s WHERE msidn = %s", ':transaction_type', ':msidn'
        );  
  



       $this->insert($sql, $parameters);


       
    }





    public function updateSessionTFields($parameters)
    {
   

        $sql = sprintf(
            
            "UPDATE session_manager_table SET %s  WHERE %s ", 
            
            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]), 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );


        $this->insert($sql, $parameters);

       
    }



    public function getTField($parameters)
    {
   

        $sql = sprintf(
            
            "SELECT %s FROM session_manager_table WHERE %s ", $parameters[0], 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );

        unset($parameters[0]);


       $results = $this->select($sql, $parameters);


       return array_values((array)$results[0])[0];

       
    }



    public function insertInitialBioData($parameters)
    {

        $sql = sprintf(
            
            "INSERT INTO pensioners_biodata_table (%s) VALUES (%s)", implode(', ', array_keys($parameters)),  ':'.implode(', :', array_keys($parameters))
        );



        $this->insert($sql, $parameters);

    }

    public function insertOtherBioData($parameters)
    {

        $sql = sprintf(
            
            "UPDATE pensioners_biodata_table SET %s  WHERE %s ", 
            
            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]), 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );



        $this->insert($sql, $parameters);

    }



     public function selectBioDataCount($parameters)
    {

        $sql = sprintf(
            
            "SELECT count(member_id), count(surname), count(firstname), count(other_names), count(dob), count(gender), count(nationality) FROM pensioners_biodata_table WHERE %s = %s", key($parameters), ':'. key($parameters)
            
        );



       $result = $this->select($sql, $parameters);

       return array_sum(array_values((array)$result[0]));

    }



    public function clearIncompleteSessionBioData($parameters)
    {

        $sql = $sql = sprintf(
            
            "DELETE FROM pensioners_biodata_table WHERE %s = %s", key($parameters), ':'. key($parameters)
            
        );


        $this->insert($parameters);

    }




    public function deleteSession($parameters)
    {

        $sql = $sql = sprintf(
            
            "DELETE FROM session_manager_table WHERE %s = %s", key($parameters), ':'. key($parameters)
            
        );


        $this->insert($sql, $parameters);
    } 



    public function getMemberID($parameters)
    {

        $sql = sprintf(
            
            "SELECT %s FROM pensioners_biodata_table WHERE %s ", $parameters[0], 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );

        unset($parameters[0]);


       $results = $this->select($sql, $parameters);


       return array_values((array)$results[0])[0];
    }



    public function findMember($parameters)
    {
        $sql = sprintf(
            
            "SELECT * FROM pensioners_biodata_table WHERE %s AND %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]),
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );



        $results = (array)$this->select($sql, $parameters);

      
        return $results[0];

 
    }




    public function fetchBioData($parameters)
    {
        $sql = sprintf(
            
            "SELECT * FROM pensioners_biodata_table WHERE %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)])
        );



        $results = $this->select($sql, $parameters);


        return (array)$results[0];

 
    }


    public function approveBioData($parameters)
    {
        $sql = sprintf(
            
            "UPDATE pensioners_biodata_table SET %s  WHERE %s ", 
            
            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]), 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );


        $this->insert($sql, $parameters);
    }




}