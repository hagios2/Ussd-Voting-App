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

               /*die('Whoops, somthing went wrong'); */

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


   public function IdentifyUser($parameters)
   {
        $sql = sprintf(
            
            "INSERT INTO session_manager_table (msisdn) VALUES (%s)",  ':'.key($parameters)
        );


        $this->insert($sql, $parameters);

  
    }



    public function sessionManager($msisdn)
    {


        $sql = sprintf(
            
                "SELECT count(msisdn), count(transaction_type), count(T1), count(T2), count(T3), count(T4), count(T5), count(T6), count(T7), count(T8), count(T9)
                
                FROM session_manager_table WHERE msisdn = %s", ':msisdn'
                
            );

        $results = $this->select($sql,['msisdn' => $msisdn]);

        return array_sum(array_values((array)$results[0]));

 
    }





    public function GetTransactionType($msisdn)
    {

        $sql = "SELECT transaction_type FROM session_manager_table WHERE msisdn = (:msisdn)";



        $results = $this->select($sql, $msisdn);


        return array_values((array)$results[0])[0];

    }




    public function updateTransanctionType($parameters)
    {

    
        $msisdn = $parameters['msisdn'];

        $field = "";

        unset($parameters['msisdn']);

        foreach($parameters as $key => $value)
        {

            if($value != '')
            {
                $field .=  ','.$key. ' = '.  "'$value'"; 
            }else{

                $field .=  ','.$key. ' = NULL'; 
            }
            
            
        }


        $field = substr($field, 1);

        $sql = "UPDATE session_manager_table SET  $field WHERE msisdn = $msisdn";

      //die($sql);
    


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



    public function clearIncompleteSessionVotes($parameters)
    {

        $sql = sprintf(
            
            "DELETE FROM votes WHERE %s AND %s", 

            implode('', [key($parameters), ' = :'.key($parameters)]));
        
        
        $statement = $this->pdo->prepare($sql);


        $statement->execute($parameters);

    }




    public function deleteSession($parameters)
    {

        $sql = $sql = sprintf(
            
            "DELETE FROM session_manager_table WHERE %s = %s", key($parameters), ':'. key($parameters)
            
        );


        $this->insert($sql, $parameters);
    } 



    public function getStudentID($parameters)
    {

        $sql = sprintf(
            
            "SELECT student_id FROM students WHERE %s ", 
            
            implode('', [key($parameters), ' = :'.key($parameters)])
        );


       $results = $this->select($sql, $parameters);


       return array_values((array)$results[0])[0];
    }



    public function verifyId($parameters)
    {
        $sql = sprintf(
            
            "SELECT * FROM students WHERE %s", 

            implode('', [key($parameters), ' = :'.key($parameters)]),
            

        );


        $results = (array)$this->select($sql, $parameters);

      
        return $results;

 
    }




    public function fetchCandidates($parameters)
    {
        $sql = sprintf(
            
            "SELECT * FROM candidates WHERE %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)])
        );



        $results = $this->select($sql, $parameters);


        return (array)$results;
 
    }


    public function getCandidates($parameters)
    {
        $sql = sprintf(
            
            "SELECT candidates.name, candidates.candidate_type FROM candidates INNER JOIN votes ON votes.candidate_id = candidates.id WHERE %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)])
        );



        $results = $this->select($sql, $parameters);


        return (array)$results;
 
    }



    public function insertVotes($parameters)
    {

        $sql = sprintf(
            
            "INSERT INTO votes (%s) VALUES (%s)", implode(', ', array_keys($parameters)),  ':'.implode(', :', array_keys($parameters))
        );


        $this->insert($sql, $parameters);

    }



    public function confirmVote($parameters)
    {

        $sql = sprintf(
            
            "INSERT INTO confirmed_voter (%s) VALUES (%s)", implode(', ', array_keys($parameters)),  ':'.implode(', :', array_keys($parameters))
        );


        $this->insert($sql, $parameters);

    }


    public function getVotedStudent($parameters)
    {
        $sql = sprintf(
            
            "SELECT * FROM confirmed_voter WHERE %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)])
        );



        $results = $this->select($sql, $parameters);


        return (array)$results;
 
    }




    public function updateVotes($parameters)
    {

        $sql = sprintf(
            
            "UPDATE votes SET %s  WHERE %s AND %s", 
            
            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]), 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );



        $this->insert($sql, $parameters);

    }


    public function findCandidate($parameters)
    {

        $sql = sprintf(
            
            "SELECT * FROM candidates WHERE %s AND %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]),

            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );

        $results = $this->select($sql, $parameters);


        return (array)$results;

    }


}