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

        $sql = sprintf(
            
            "DELETE FROM pensioners_biodata_table WHERE %s AND %s", 

            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]), 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)]));
        
   
        
        $statement = $this->pdo->prepare($sql);


        $statement->execute($parameters);
        #die(var_dump($sql));

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
            
            "SELECT member_id FROM pensioners_biodata_table WHERE %s ", 
            
            implode('', [key($parameters), ' = :'.key($parameters)])
        );


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



    public function insertIBeneficiaryData($parameters)
    {

        $sql = sprintf(
            
            "INSERT INTO beneficiaries (%s) VALUES (%s)", implode(', ', array_keys($parameters)),  ':'.implode(', :', array_keys($parameters))
        );


        $this->insert($sql, $parameters);

    }



    public function insertOtherBeneficiaryField($parameters)
    {

        $sql = sprintf(
            
            "UPDATE beneficiaries SET %s  WHERE %s ", 
            
            implode('', [array_key_first($parameters), ' = :'.array_key_first($parameters)]), 
            
            implode('', [array_key_last($parameters), ' = :'.array_key_last($parameters)])
        );



        $this->insert($sql, $parameters);

    }




}