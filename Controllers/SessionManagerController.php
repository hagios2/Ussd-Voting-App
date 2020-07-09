<?php


//include 'ApplicationFunctions.php';

include 'OtherFunctions.php';


$request = $_POST; 

$msisdn = $request['msisdn'];

$sequence_ID = $request['session_id'];

$data = $request['answer'];

$ussd = $app['database'];

$cost = 0;




function writeLog($msisdn, $sequence_ID, $case, $request, $response)
{
    date_default_timezone_set('GMT');
    $time = date('Y-m-d H:i:s');

    $record = $time . "|UG Votes|" . $msisdn . "|" . $sequence_ID . "|" . $case . "|" . $request . "|" . $response . PHP_EOL;
    file_put_contents('Ussd_access.log', $record, FILE_APPEND);
}


$sess = $ussd->sessionManager($msisdn);


if($sess === 0)
{

    $ussd->IdentifyUser(['msisdn' => $msisdn]);   

    $reply = displayWelcomeText();

    echo $reply;

    writeLog($msisdn, $sequence_ID, $sess, $data, $reply);
   
}else {
 

    switch ($sess) {

        case 1:

            if(substr($data, 1, 4) == '025')
            {

                $reply = displayWelcomeText(); 
                
                $type = 1;
        
            }else{

                #verify student id

                $id = $ussd->verifyId(['student_id' => $data]);

                if(!empty($id))
                {

                     //insert transaction type == register
                    $ussd->updateTransanctionType([
                        
                        'transaction_type' => 'Entered_ID',
                    
                        'msisdn' => $msisdn
                        
                    ]);  


                    $reply = getFetchedCandidate('President', $ussd);

                
                }else{

                    $reply = "ID not found in our records";
                }


                $type = 1;

            }

            break;

        case 2:


           $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            if(substr($data, 1, 4) == '025')
            {

                /* 
                *   find the msisdn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                 $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText();

                $type = 1;

    
            }elseif($transactionType == 'Entered_ID'){  
                
                if($data != 0)
                {

                    $tField = ['T1' => 'Presidential_Vote'];
                
                    #call function to insert vote
    
                    insertStudentVote($ussd, $data, $msisdn, $tField);

                }

                 
                # Call function to vice presidential candidates

               $reply = getFetchedCandidate('Vice President', $ussd);

               $type = 1;
                

            }

            break;

        case 3:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T1 = $ussd->getTField(['T1','msisdn' => $msisdn]);

            if(substr($data, 1, 5) == '025')
            {

                /* 
                *   find the msisdn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                resetbioData($ussd, $msisdn); #reset bio data if something has been inseted for this sesion


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 'T1' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

            
                $reply = displayWelcomeText(); 

                $type = 1;

            }elseif($transactionType == 'Entered_ID' && $T1 == 'Presidential_Vote'){

                if($data != 0)
                {

                    $tField = ['T2' => 'Vice_Presidential_Vote'];
                
                    #call function to insert vote
    
                    insertStudentVote($ussd, $data, $msisdn, $tField);

                }
                    

                # Call function to insert member_id, and prompt user to enter surname

                $reply = getFetchedCandidate('Treasurer', $ussd);

                $type = 1;


         

               /*  $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);
                    */
            }
            break;
    
        case 4:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T2 = $ussd->getTField(['T2','msisdn' => $msisdn]);


            if(substr($data, 1, 5) == '025')
            {

                /* 
                *   find the msisdn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */


                resetbioData($ussd, $msisdn);

                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL,

                    'T2' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;


            }elseif($transactionType == 'Entered_ID' &&  $T2 == 'Vice_Presidential_Vote'){


                if($data != 0)
                {

                    $tField = ['T3' => 'Treasurer_Vote'];
                
                    #call function to insert vote
    
                    insertStudentVote($ussd, $data, $msisdn, $tField);

                }
                    

                # Call function to insert member_id, and prompt user to enter surname

                $reply = getFetchedCandidate('Secretary', $ussd);
             

                $type = 1;
            }

            break;

        case 5:


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T3 = $ussd->getTField(['T3','msisdn' => $msisdn]);

            
            if(substr($data, 1, 5) == '025')
            {

                /* 
                *   find the msisdn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return the welcome text   
                */

                resetbioData($ussd, $msisdn);

                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;          

            }elseif($transactionType == 'Entered_ID' &&  $T3 == 'Treasurer_Vote'){


                if($data != 0)
                {

                    $tField = ['T4' => 'Secretary_Vote'];
                
                    #call function to insert vote
    
                    insertStudentVote($ussd, $data, $msisdn, $tField);

                }
                    

                # Call function to insert member_id, and prompt user to enter surname

                $reply = getFetchedCandidate('Organizer', $ussd);
            

                $type = 1;

            
            }

            break;

        case 6:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T4 = $ussd->getTField(['T4','msisdn' => $msisdn]);

            if(substr($data, 1, 5) == '025')
            {

                /* 
                *   find the msisdn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                resetbioData($ussd, $msisdn);


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 
                    
                    'T1' => NULL, 'T2' => NULL,
                    
                    'T3' => NULL, 'T4' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;
                
            }elseif($transactionType == 'Entered_ID' &&  $T4 == 'Secretary_Vote'){

               
                if($data != 0)
                {

                    $tField = ['T5' => 'Organizer_Vote'];
                    
                
                    #call function to insert vote
    
                    insertStudentVote($ussd, $data, $msisdn, $tField);

                }

                $student_id = $ussd->getStudentId(['phone' => $msisdn]);



               $candidates_voted_for = $ussd->getCandidates(['student_id' => $student_id]);


               die($candidates_voted_for);
                    

                # Call function to insert member_id, and prompt user to enter surname

                $reply = "YOUR VOTES --------------- \r\n";
                
                $type = 1;

            }

            break;
        
        default :

            $reply = "Invalid option. Kindly dial *899*100# to continue or contact the provider for assistance";

            $type = 3;

            $ussd->deleteSession(['msisdn' => $msisdn]);
            // $ussd->deleteService($msisdn);

            break;

    }

    echo $reply;

    writeLog($msisdn, $sequence_ID, $sess, $data, $reply);
}


function getFetchedCandidate($candidate_type, $ussd)
{
    $candidate_results = $ussd->fetchCandidates(['candidate_type' => $candidate_type]);


    $text = array_map(function($aspirants){

        return $aspirants->id. '. '. $aspirants->name. "\r\n";
    },  $candidate_results);

    
    if($candidate_type == 'President')
    {
        $reply = "PRESIDENTIAL \r\n ------------- \r\n". implode("\r\n", $text);
    
    }elseif($candidate_type == 'Vice President'){

        $reply = "VICE PRESIDENT \r\n ------------- \r\n". implode("\r\n", $text);   

     
    }elseif($candidate_type == 'Treasurer'){

        $reply = "TREASURER \r\n ------------- \r\n". implode("\r\n", $text);  

        
    }elseif($candidate_type == 'Secretary'){

        $reply = "SECRETARY \r\n ------------- \r\n". implode("\r\n", $text);  

            
    }elseif($candidate_type == 'Organizer'){

        $reply = "ORGANIZER \r\n ------------- \r\n". implode("\r\n", $text);  
    }



    return $reply."\r\n 0. Skip";
}





function insertStudentVote($ussd, $data, $msisdn, $tField)
{

    #get the student id for session

    $student_id = $ussd->getStudentId(['phone' => $msisdn]);


    #insert student id and candidates id into votes table

    $ussd->insertVotes([

        'student_id' => $student_id,

        'candidate_id' => $data

    ]);


    $ussd->updateSessionTFields([
                    
        key($tField) => $tField[key($tField)],
            
        'msisdn' => $msisdn
        
    ]);


}






function displayWelcomeText()
{
    return $reply = "Welcome to UG Votes \r\n Input ID";

}



function resetbioData($ussd, $msisdn)
{
    /* 
    *     if transaction type == Register Personal Pension 
    *      
    *     clear incomplete biodata for this session
    *
    *     restart the flow   
    */

    $T1 = $ussd->getTField(['T1','msisdn' => $msisdn]);

    $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

    if($transactionType == 'Register_Personal_Pension' && $T1 == 'Inserted_Member_id')
    {

        $member_id = $ussd->getMemberID(['msisdn' => $msisdn]);

        $ussd->clearIncompleteSessionBioData(['msisdn' => $msisdn, 'member_id' => $member_id]);
    }

}