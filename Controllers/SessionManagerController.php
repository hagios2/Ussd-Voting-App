<?php


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

            if(substr($data, 1, 3) == '025')
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

                    
                    #check if student has already voted
                   $voted = $ussd->getVotedStudent(['student_id' => $data]);

                   if(!empty($voted))
                   {
                       $reply = 'You have already voted!';


                       $ussd->deleteSession(['msisdn' => $msisdn]);

                       $type = 3;
                   
                    }else{

                        #display presidential candidates

                        $reply = getFetchedCandidate('President', $ussd);

                        $type = 1;

                    }

                
                }else{

                    $reply = "ID not found in our records. Try Again";

                    $type = 1;

                }

            }

            break;

        case 2:


           $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            if(substr($data, 1, 3) == '025')
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

                    $candidate = $ussd->findCandidate([

                        'id' => $data,

                        'candidate_type' => 'President'
                    ]);

                    if(!empty($candidate))
                    { # for valid input 

                        $tField = ['T1' => 'Presidential_Vote'];
                
                        #call function to insert vote
        
                        insertStudentVote($ussd, $data, $msisdn, $tField);
    
                        # display vice presidential candidates
    
                        $reply = getFetchedCandidate('Vice President', $ussd);
                    
                    }else{

                        #for invalid input

                        $reply = getFetchedCandidate('President', $ussd);


                        $reply = "Invalid Input \r\n\r\n". $reply;

                    }

                   
                }else{

                    $tField = ['T1' => 'Skipped_Presidential_Vote'];

                    skipVote($ussd, $msisdn, $tField);
                    
                    # Call function to fetch vice presidential candidates

                    $reply = getFetchedCandidate('Vice President', $ussd);
                }


               $type = 1;
                

            }

            break;

        case 3:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T1 = $ussd->getTField(['T1','msisdn' => $msisdn]);

            if(substr($data, 1, 3) == '025')
            {

                resetSessionVotes($ussd, $msisdn); #reset bio data if something has been inseted for this sesion


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 'T1' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

            
                $reply = displayWelcomeText(); 

                $type = 1;

            }elseif($transactionType == 'Entered_ID' && ($T1 == 'Presidential_Vote' || 'Skipped_Presidential_Vote')){

                if($data != 0)
                {

                    $candidate = $ussd->findCandidate([

                        'id' => $data,

                        'candidate_type' => 'Vice President'
                    ]);

                    if(!empty($candidate))
                    {
                       
                        $tField = ['T2' => 'Vice_Presidential_Vote'];
                
                        #call function to insert vote
        
                        insertStudentVote($ussd, $data, $msisdn, $tField);
    
                        # Call function to treasurer
    
                        $reply = getFetchedCandidate('Treasurer', $ussd);
                    
                    }else{


                        $reply = getFetchedCandidate('Vice President', $ussd);

                        $reply = "Invalid Input \r\n\r\n". $reply;

                    }
                
            

                }else{

                    $tField = ['T2' => 'Skipped_Vice_Presidential_Vote'];

                    skipVote($ussd, $msisdn, $tField);

                    # Call function to fetch candidates prompt user 

                    $reply = getFetchedCandidate('Treasurer', $ussd);

                }

                $type = 1;

            }

            break;
    
        case 4:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T2 = $ussd->getTField(['T2','msisdn' => $msisdn]);


            if(substr($data, 1, 3) == '025')
            {

                /* 
                *   find the msisdn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */


                resetSessionVotes($ussd, $msisdn);

                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL,

                    'T2' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;


            }elseif($transactionType == 'Entered_ID' && ($T2 == 'Vice_Presidential_Vote' || 'Skipped_Vice_Presidential_Vote')){


                if($data != 0)
                {

                    $candidate = $ussd->findCandidate([

                        'id' => $data,

                        'candidate_type' => 'Treasurer'
                    ]);

                    if(!empty($candidate))
                    {
                       
                        $tField = ['T3' => 'Treasurer_Vote'];
                
                        #call function to insert vote
        
                        insertStudentVote($ussd, $data, $msisdn, $tField);

                        # display Secretary candidates
    
                        $reply = getFetchedCandidate('Secretary', $ussd);
                    
                    }else{


                        $reply = getFetchedCandidate('Treasurer', $ussd);



                        $reply = "Invalid Input \r\n\r\n". $reply;

                    }
                
            

                }else{

                    $tField = ['T3' => 'Skipped_Treasurer_Vote'];


                    skipVote($ussd, $msisdn, $tField);

                    # Call function to fetch candidates to prompt user 

                    $reply = getFetchedCandidate('Secretary', $ussd);

                }

                $type = 1;
            }

            break;

        case 5:


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T3 = $ussd->getTField(['T3','msisdn' => $msisdn]);

            
            if(substr($data, 1, 3) == '025')
            {

                resetSessionVotes($ussd, $msisdn);

                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;          

            }elseif($transactionType == 'Entered_ID' &&  ($T3 == 'Treasurer_Vote' || $T3 == 'Skipped_Treasurer_Vote')){


                if($data != 0)
                {

                    $candidate = $ussd->findCandidate([

                        'id' => $data,

                        'candidate_type' => 'Secretary'
                    ]);

                    if(!empty($candidate))
                    {
                       
                        $tField = ['T4' => 'Secretary_Vote'];
                
                        #call function to insert vote
        
                        insertStudentVote($ussd, $data, $msisdn, $tField);

                        # Call function to Organizer
    
                        $reply = getFetchedCandidate('Organizer', $ussd);
                    
                    }else{


                        $reply = getFetchedCandidate('Secretary', $ussd);


                        $reply = "Invalid Input \r\n\r\n". $reply;

                    }

                }else{

                    $tField = ['T4' => 'Skipped_Secretary_Vote'];


                    skipVote($ussd, $msisdn, $tField);

                    # Call function to fetch candidates prompt user 


                    $reply = getFetchedCandidate('Organizer', $ussd);
                }


                $type = 1;
            
            }

            break;

        case 6:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T4 = $ussd->getTField(['T4','msisdn' => $msisdn]);

            if(substr($data, 1, 3) == '025')
            {
                #reset the session

                resetSessionVotes($ussd, $msisdn);


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 
                    
                    'T1' => NULL, 'T2' => NULL,
                    
                    'T3' => NULL, 'T4' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;
                
            }elseif($transactionType == 'Entered_ID' &&  ($T4 == 'Secretary_Vote' || $T4 == 'Skipped_Secretary_Vote')){

                if($data != 0)
                {

                    $candidate = $ussd->findCandidate([

                        'id' => $data,

                        'candidate_type' => 'Organizer'
                    ]);

                    if(!empty($candidate))
                    {
                       
                        $tField = ['T5' => 'Organizer_Vote'];
                    
                        #call function to insert vote
        
                        insertStudentVote($ussd, $data, $msisdn, $tField);


                        #display confirm votes
                        $reply = confirmSessionVotes($msisdn, $ussd);
                    
                    }else{


                        $reply = getFetchedCandidate('Organizer', $ussd);


                        $reply = "Invalid Input \r\n\r\n". $reply;

                    }
                
            

                }else{


                    $tField = ['T5' => 'Skipped_Organizer_Vote'];

                    skipVote($ussd, $msisdn, $tField);

                    # Call function to fetch candidates prompt user 


                    $reply = confirmSessionVotes($msisdn, $ussd);
                }

            }

            break;
        
        case 7:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T5 = $ussd->getTField(['T5','msisdn' => $msisdn]);

            if(substr($data, 1, 3) == '025')
            {

                resetSessionVotes($ussd, $msisdn);


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 
                    
                    'T1' => NULL, 'T2' => NULL,
                    
                    'T3' => NULL, 'T4' => NULL, 'T5' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;
                
            }elseif($transactionType == 'Entered_ID' &&  ($T5 == 'Organizer_Vote' || $T5 == 'Skipped_Organizer_Vote' )){

                if($data == 1)
                {
                    $student_id = $ussd->getStudentId(['phone' => $msisdn]);


                    $ussd->confirmVote([
                        
                        'student_id' => $student_id, 

                        'voted' => true
                
                    ]);

                    $ussd->deleteSession(['msisdn' => $msisdn]);


                    $reply = 'Voting completed';

                    $type = 3;

                }elseif($data == 2){

                    $ussd->updateSessionTFields([
                    
                        'T6' => 'Make_Changes',
                            
                        'msisdn' => $msisdn
                        
                    ]);


                    $reply = displayPortfolio();

                    $type = 1;

                }else{

                    $reply = 'Invalid Input! Try again';

                    $reply .= " \r\n\r\n" . confirmSessionVotes($msisdn, $ussd);

                    $type = 1;
                }

            }

        break;

        case 8:


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T6 = $ussd->getTField(['T6','msisdn' => $msisdn]);

            if(substr($data, 1, 3) == '025')
            {

                resetSessionVotes($ussd, $msisdn);


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 
                    
                    'T1' => NULL, 'T2' => NULL,
                    
                    'T3' => NULL, 'T4' => NULL, 
                    
                    'T5' => NULL, 'T6' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;

            }elseif($transactionType == 'Entered_ID' && $T6 == 'Make_Changes'){

                switch($data)
                {
                    case 1:

                        $ussd->updateSessionTFields([
                    
                            'T7' => 'Update_President',
                                
                            'msisdn' => $msisdn
                            
                        ]);

                        $reply = getFetchedCandidate('President', $ussd);

                        $type = 1;

                    break;

                    case 2:

                        $ussd->updateSessionTFields([
                    
                            'T7' => 'Update_Vice_President',
                                
                            'msisdn' => $msisdn
                            
                        ]);

                        $reply = getFetchedCandidate('Vice President', $ussd);

                        $type = 1;

                    break;

                    case 3: 

                        $ussd->updateSessionTFields([
                    
                            'T7' => 'Update_Treasurer',
                                
                            'msisdn' => $msisdn
                            
                        ]);

                        $reply = getFetchedCandidate('Treasurer', $ussd);

                        $type = 1;

                    break;

                    case 4:

                        $ussd->updateSessionTFields([
                    
                            'T7' => 'Update_Secretary',
                                
                            'msisdn' => $msisdn
                            
                        ]);

                        $reply = getFetchedCandidate('Secretary', $ussd);

                        $type = 1;

                    break;

                    case 5:

                        $ussd->updateSessionTFields([
                    
                            'T7' => 'Update_Organizer',
                                
                            'msisdn' => $msisdn
                            
                        ]);

                        $reply = getFetchedCandidate('Organizer', $ussd);

                        $type = 1;

                    break;

                    default:

                        $reply = "Invalid input \r\n\r\n" . displayPortfolio();

                        $type = 1;
            

                    break;
                
                }

            }

        break;

        case 9:


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T7 = $ussd->getTField(['T7','msisdn' => $msisdn]);

            if(substr($data, 1, 3) == '025')
            {

                resetSessionVotes($ussd, $msisdn);


                $ussd->updateTransanctionType([
                    
                    'transaction_type' => NULL, 
                    
                    'T1' => NULL, 'T2' => NULL,
                    
                    'T3' => NULL, 'T4' => NULL, 
                    
                    'T5' => NULL, 'T6' => NULL,

                    'T7' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText(); 

                $type = 1;

            }elseif($transactionType == 'Entered_ID' && $T7 == 'Update_President'){

                $reply = updateSessionVote($msisdn, $ussd, 'President', $data, 'Presidential_Vote');

                $type = 1;

            }elseif($transactionType == 'Entered_ID' && $T7 == 'Update_Vice_President'){

                $reply = updateSessionVote($msisdn, $ussd, 'Vice President', $data, 'Vice_Presidential_Vote');

                $type = 1;
                
            }elseif($transactionType == 'Entered_ID' && $T7 == 'Update_Treasurer'){

                $reply = updateSessionVote($msisdn, $ussd, 'Treasurer', $data, 'Treasurer_Vote');

                $type = 1;

            }elseif($transactionType == 'Entered_ID' && $T7 == 'Update_Secretary'){

                $reply = updateSessionVote($msisdn, $ussd, 'Secretary', $data, 'Secretary_Vote');

                $type = 1;
                    
            }elseif($transactionType == 'Entered_ID' && $T7 == 'Update_Organizer'){

                $reply = updateSessionVote($msisdn, $ussd, 'Organizer', $data, 'Organizer_Vote');

                $type = 1;

            }

        break;

        default :

            $reply = "Invalid option. Kindly dial *025# to continue or contact the provider for assistance";

            $type = 3;

            $ussd->deleteSession(['msisdn' => $msisdn]);

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



function insertStudentVote($ussd, $data,  $msisdn, $tField)
{

    #get the student id for session

    $student_id = $ussd->getStudentId(['phone' => $msisdn]);


    #insert student id, candidates id and vote type into votes table

    $ussd->insertVotes([

        'student_id' => $student_id,

        'candidate_id' => $data,

        'vote_type' => $tField[key($tField)]

    ]);


    $ussd->updateSessionTFields([
                    
        key($tField) => $tField[key($tField)],
            
        'msisdn' => $msisdn
        
    ]);


}



function displayWelcomeText()
{
    return $reply = "Welcome to UG Votes \r\n --------------------- \r\n Input ID";

}



function resetSessionVotes($ussd, $msisdn)
{
    /* 
    *     if transaction type == Entered id 
    *      
    *     clear incomplete votesfor this session
    *
    *     restart the flow   
    */

    $T1 = $ussd->getTField(['T1','msisdn' => $msisdn]);

    $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

    if($transactionType == 'Entered_ID' && $T1 == 'Presidential_Vote' || $T1 == 'Skipped_Presidential_Vote')
    {

        $student_id = $ussd->getStudentId(['phone' => $msisdn]);

        $ussd->clearIncompleteSessionVotes(['student_id' => $student_id]);
    }

}



function skipVote($ussd, $msisdn, $tField)
{


    $ussd->updateSessionTFields([
                    
        key($tField) => $tField[key($tField)],
            
        'msisdn' => $msisdn
        
    ]);

}


function displayPortfolio()
{
    $reply = "1. President \r\n 2. Vice Presidential \r\n 3. Treasurer \r\n 4. Secretary \r\n 5. Organizer";

    
    return $reply;
}



function confirmSessionVotes($msisdn, $ussd)
{
    $student_id = $ussd->getStudentId(['phone' => $msisdn]);

    $candidates_voted_for = $ussd->getCandidates(['student_id' => $student_id]);

    $text = array_map(function($candidates){

        return $candidates->candidate_type. ":\t\t". $candidates->name. "\r\n";
        }, $candidates_voted_for
    );


    $text = implode("\r\n", $text). "\r\n 1. Confirm Vote \r\n 2. Make Change";

    $reply = "YOUR VOTES \r\n ------------ \r\n" . $text;
     
    return $reply;
}


function updateSessionVote($msisdn, $ussd, $candidate_type, $data, $vote_type)
{

    if($data != 0)
    {

        $candidate = $ussd->findCandidate([

            'id' => $data,
    
            'candidate_type' => $candidate_type
        ]);

        if(!empty($candidate))
        {
            $student_id = $ussd->getStudentId(['phone' => $msisdn]);


            $ussd->updateVotes([

                'candidate_id' => $data,

                'student_id' => $student_id,

                'vote_type' => $vote_type

            ]);


            $ussd->updateTransanctionType([
                
                'T6' => NULL, 'T7' => NULL,
            
                'msisdn' => $msisdn
                
            ]);


            $reply = confirmSessionVotes($msisdn, $ussd);

        }else{

            $reply = getFetchedCandidate($candidate_type, $ussd);

            $reply = "Invalid Input \r\n\r\n". $reply;

        }


    }else{

        $ussd->updateTransanctionType([
                
            'T6' => NULL, 'T7' => NULL,
        
            'msisdn' => $msisdn
            
        ]);

        $reply = confirmSessionVotes($msisdn, $ussd);
      
    }


    return $reply;

}