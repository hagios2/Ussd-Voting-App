<?php


include 'ApplicationFunctions.php';


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

    $record = $time . "|MTN|" . $msisdn . "|" . $sequence_ID . "|" . $case . "|" . $request . "|" . $response . PHP_EOL;
    file_put_contents('Ussd_access.log', $record, FILE_APPEND);
}


$sess = $ussd->sessionManager($msisdn);


if($sess === 0)
{

    $ussd->IdentifyUser(['msisdn' => $msisdn]);   

    $reply = displayWelcomeText();

    writeLog($msisdn, $sequence_ID, $sess, $data, $reply);
   
}else {
 

    switch ($sess) {

        case 1:

            if(substr($data, 1, 5) == '899*9')
            {

                $reply = displayWelcomeText(); 
                
                $type = 1;
        
            }elseif($data == 1){

                //insert transaction type == register
               $ussd->updateTransanctionType([
                    
                        'transaction_type' => 'Register_Personal_Pension',
                    
                        'msisdn' => $msisdn
                        
                    ]);   

                $reply = "Register Personal Pension \r\n 1. New Member \r\n 2. Existing Tier 2 or PF Member";

                $type = 1;
            
            }elseif($data == 2){

                   //insert transaction type == pay

                   $ussd->updateTransanctionType([
                    
                    'transaction_type' => 'Pay_Personal_Pension',
                
                    'msisdn' => $msisdn
                    
                ]);  ;   

                $reply = "Pay Personal Pension \r\n Enter Member ID";
              
                $type = 1;

            }elseif($data == 3){

                //insert transaction type == update

                $ussd->updateTransanctionType([
                    
                    'transaction_type' => 'Update_Key_Data',
                
                    'msisdn' => $msisdn
                    
                ]);  

                $reply = "Update Key Data \r\n Enter Member ID";

                $type = 1;            
              

            }elseif($data == 4){

                //insert transaction type == Enquiries

                     $ussd->updateTransanctionType([
                    
                        'transaction_type' => 'Enquiries',
                    
                        'msisdn' => $msisdn
                        
                    ]);  

                $reply = "Enquiries \r\n 1. Check Mini Statement \r\n 2. Info About Scheme";
            
                $type = 1;            

            }else{

                $reply = "Invalid input";

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);


            }

            break;

        case 2:


           $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            if(substr($data, 1, 5) == '899*9')
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

    
            }elseif($transactionType == 'Register_Personal_Pension' && $data == 1){

                 
                # Call function to insert member_id, and prompt user to enter surname

               $reply = insertBioData($ussd, $data, $msisdn);

               $type = 1;
                

            }elseif($transactionType == 'Register_Personal_Pension' && $data == 2){

                $ussd->updateSessionTFields([
                    
                    'T1' => 'Selected_Existing_Tier',
                
                    'msisdn' => $msisdn
                    
                ]);  

                $reply = "Existing Tier 2/PF Member \r\n Enter Member ID"; //write a 
              
                $type = 1;
         
            }elseif($transactionType == 'Pay_Personal_Pension'){

                //insert member id 

               $member_id =  $ussd->findMember([
    
                'member_id' => $data,

                'msisdn' => $msisdn,
                    
                ]);
                
                if(!empty($member_id))
                {
                    $ussd->updateSessionTFields([
                    
                        'T2' => 'Entered_Member_ID',
                    
                        'msisdn' => $msisdn
                        
                    ]); 

                    //  $reply = "1. View Key Data \r\n 2. Cancel"; change later
                
                    $reply = "Enter Amount";
      
                }else{

                    $reply = "Invalid Member ID! Try again ";

                }

                $type = 1;

              
            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);

            }

            break;

        case 3:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T1 = $ussd->getTField(['T1','msisdn' => $msisdn]);

            if(substr($data, 1, 5) == '899*9')
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

            }elseif($transactionType == 'Register_Personal_Pension' && $T1 == 'Inserted_Member_id'){


                #call function to insert surname and display prompt

              $reply = insertBioData($ussd, $data, $msisdn);

              $type = 1;


            }elseif($transactionType == 'Register_Personal_Pension' && $T1 == 'Selected_Existing_Tier'){

                //insert member id 

               $member_id =  $ussd->findMember([
    
                    'member_id' => $data,

                    'msisdn' => $msisdn,
                    
                ]);
                
                if(!empty($member_id))
                {
                    $ussd->updateSessionTFields([
                    
                        'T2' => 'Entered_Member_ID',
                    
                        'msisdn' => $msisdn
                        
                    ]); 

                    $reply = "1. View Key Data \r\n 2. Cancel";
                
                }else{

                    $reply = "Invalid Member ID! Try again ";

                }

                $type = 1;
      
            }elseif($transactionType == 'Pay_Personal_Pension'){

                //persist amount in db

                $ussd->updateSessionTFields([
                    
                    'T2' => 'Entered_Amount',
                
                    'msisdn' => $msisdn
                    
                ]); 

               $reply = "Enter Mobile Money Pin";
               
               $type = 1;


            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);

            }


            break;
    
        case 4:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T2 = $ussd->getTField(['T2','msisdn' => $msisdn]);


            if(substr($data, 1, 5) == '899*9')
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


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T2 == 'Inserted_Surname'){

                #   call function to insert firstname and display promt 
                
                $reply = insertBioData($ussd, $data, $msisdn);

                $type = 1;

            }elseif($transactionType == 'Register_Personal_Pension' &&  $T2 == 'Entered_Member_ID'){

                if($data == 1)
                {

                    $bio_data = $ussd->fetchBioData(['msisdn' => $msisdn]);

                    $info = [
    
                        'member_id' => $bio_data['member_id'],
    
    
                        'name' =>  $bio_data['firstname'].' '.$bio_data['other_names'].' '.$bio_data['surname'],
    
    
                        'dob' => $bio_data['dob'],
    
    
                        'gender' => $bio_data['gender'],
    
    
                        'nationality' => $bio_data['nationality']
    
                    ];

    
                    $ussd->updateSessionTFields([
                        
                        'T3' => 'Viewed_BioData',
                    
                        'msisdn' => $msisdn
                        
                    ]); 
                    
    
                    $reply = sprintf("Personal Pension Details \r\n ------------------------
                    Member ID: \t %s \r\n Name: \t %s  \r\n D.O.B: \t %s \r\n Gender: \t %s \r\n Nationality: \t %s \r\n\r\n 1. Press to Approve", $info['member_id'], $info['name'], $info['dob'], $info['gender'], $info['nationality']);

                    $type = 1;

                }else{

                    $ussd->deleteSession(['msisdn' => $msisdn]);

                    $type = 3;

                    $reply = 'Ended Sesion';
                }
                
             

            }elseif($transactionType == 'Pay_Personal_Pension' &&  $T2 == 'Inserted_Member_id'){

                #process payment 

                $ussd->updateSessionTFields([
                    
                    'T3' => 'Enter_Mobile_Money_Pin',
                
                    'msisdn' => $msisdn
                    
                ]); 

                #confirm payment

                $reply = "You have transferred an amount";

                $type= 3;

            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);

            }

            break;

        case 5:


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T3 = $ussd->getTField(['T3','msisdn' => $msisdn]);

            
            if(substr($data, 1, 5) == '899*9')
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

            }elseif($transactionType == 'Register_Personal_Pension' &&  $T3 == 'Inserted_Firstname'){


                # call function to insert othername and display prome
                
               $reply = insertBioData($ussd, $data, $msisdn);

               $type = 1;

            }elseif($transactionType == 'Register_Personal_Pension' &&  $T3 == 'Viewed_BioData'){


                $ussd->approveBioData([
        
                    'verified_at' => Date('Y-m-d H:i:s'),

                    'msisdn' => $msisdn

                ]);


                $ussd->deleteSession(['msisdn' => $msisdn]);

                $reply = "Approved account Details";

                $type = 3;

            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);


            }

            break;

        case 6:

            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);

            $T4 = $ussd->getTField(['T4','msisdn' => $msisdn]);

            if(substr($data, 1, 5) == '899*9')
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
                
            }elseif($transactionType == 'Register_Personal_Pension' &&  $T4 == 'Inserted_Other_names'){


                # call function to insert dob and display prompt

                $reply = insertBioData($ussd, $data, $msisdn);
                
                $type = 1;

            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);


            }

            break;

        case 7:


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);


            $T5 = $ussd->getTField(['T5','msisdn' => $msisdn]);


            if(substr($data, 1, 5) == '899*9')
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
                    
                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL,
                    
                    'T4' => NULL,  'T5' => NULL,
                
                    'msisdn' => $msisdn
                    
                ]);


                /* 
                *     if transaction type == Register Personal Pension 
                *      
                *     clear incomplete biodata for this session
                *
                *     restart the flow   
                */

                if($transactionType == 'Register_Personal_Pension')
                {

                    $ussd->clearIncompleteSessionBioData(['msisdn' => $msisdn]);
                }
               
                $reply = displayWelcomeText();

                $type = 1;


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T5 == 'Inserted_dob'){
               
                # call function to insert gender and display prompt

              $reply = insertBioData($ussd, $data, $msisdn);

              $type = 1;

            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);

            }

            break;

        case 8;


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);


            $T6 = $ussd->getTField(['T6','msisdn' => $msisdn]);


            if(substr($data, 1, 5) == '899*9')
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


                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL, 
                    
                    'T4' => NULL, 'T5' => NULL, 'T6' => NULL, 

                    'msisdn' => $msisdn
                    
                ]);
               
                $reply = displayWelcomeText(); 

                $type = 1;
                
            }elseif($transactionType == 'Register_Personal_Pension' &&  $T6 == 'Inserted_Gender'){

                # call function to insert dob and display prompt

               $reply = insertBioData($ussd, $data, $msisdn);

               $type = 1;

            
            }else{

                $reply = 'Invalid Input';

                $type = 3;

                $ussd->deleteSession(['msisdn' => $msisdn]);

            }

            break;
        case 9;


            $transactionType = $ussd->GetTransactionType(['msisdn' => $msisdn]);


            $T7 = $ussd->getTField(['T7','msisdn' => $msisdn]);


            if(substr($data, 1, 5) == '899*9')
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

                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL, 
                    
                    'T4' => NULL, 'T5' => NULL, 'T6' => NULL, 
                    
                    'T7' => NULL, 
                                
                    'msisdn' => $msisdn
                    
                ]);

                $reply = displayWelcomeText();

                $type = 1;


            }   /* elseif($transactionType == 'Register_Personal_Pension' &&  $T7 == 'Inserted_Nationality'){

                # call function to insert dob and display prompt

                $reply = insertBioData($ussd, $data, $msisdn);

            }
            */


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


function insertBioData($ussd, $data, $msisdn)
{

    # get the sum of inserted fields on bio data table for rhis session 

   $field_count = $ussd->selectBioDataCount(['msisdn' => $msisdn]);  

    if($field_count === 0)
    {

        $ussd->insertInitialBioData([

            'member_id' => substr(sha1(time()), 0, 5),

            'msisdn' => $msisdn

        ]);
        

        $ussd->updateSessionTFields([
                    
            'T1' => 'Inserted_Member_id',
                
            'msisdn' => $msisdn
            
        ]);


        $reply = "Enter Surname";
    
    }elseif($field_count === 1){
        
        $ussd->insertOtherBioData(['surname' => $data, 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                    
            'T2' => 'Inserted_Surname',
                
            'msisdn' => $msisdn
            
        ]);
        

        $reply = "Enter first name";
    
    }elseif($field_count === 2){


        $ussd->insertOtherBioData(['firstname' => $data,  'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                    
            'T3' => 'Inserted_Firstname',
                
            'msisdn' => $msisdn
            
        ]);

        $reply = "Enter Other names Or press 1. to skip";
    
    }elseif($field_count === 3){


        $ussd->insertOtherBioData([

            'other_names' => $data == 1 ? '' : $data,

            'msisdn' => $msisdn

        ]);


        $ussd->updateSessionTFields([
                    
            'T4' => 'Inserted_Other_names',
                
            'msisdn' => $msisdn
            
        ]);

        $reply = "Enter Date of Birth (YYYY-MM-DD)";

    }elseif($field_count === 4){

        $ussd->insertOtherBioData(['dob' => date_format(date_create($data), "Y-m-d"), 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                    
            'T5' => 'Inserted_dob',
                
            'msisdn' => $msisdn
            
        ]);

        $reply = "Select Gender \r\n 1. Male \r\n 2. Female";


    }elseif($field_count === 5){

        if($data == 1)
        {

            $ussd->insertOtherBioData(['gender' => 'Male', 'msisdn' => $msisdn]);

        }elseif($data == 2){

            $ussd->insertOtherBioData(['gender' => 'Female', 'msisdn' => $msisdn]);

        }

        $ussd->updateSessionTFields([
                    
            'T6' => 'Inserted_Gender',
                
            'msisdn' => $msisdn
            
        ]);

        $reply = "Enter Nationality";
    
    }elseif($field_count === 6){


        $ussd->insertOtherBioData(['nationality' => $data, 'msisdn' => $msisdn]);

            /*      $ussd->updateSessionTFields([
                                
                        'T7' => 'Inserted_Nationality',
                            
                        'msisdn' => $msisdn
                        
                    ]);
            */
        $member_id = $ussd->getMemberID(['msisdn' => $msisdn]);

        $ussd->deleteSession(['msisdn' => $msisdn]);

        $reply = "Congratulations, You've registered with QLAC FINANCIAL TRUST LTD \r\n Your Member ID: {$member_id}";

        $type = 3;
        
    }


    return $reply;

}



function displayWelcomeText()
{
    return $reply = "Welcome to QLAC FINANCIAL TRUST LTD \r\n 1. Register Personal Pension \r\n 2. Pay Personal Pension \r\n 3. Update Key Data \r\n 4. Enquiries";

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