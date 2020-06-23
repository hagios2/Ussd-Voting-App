<?php


/* 
class SessionManagerController
{



} */


$request = $_POST; 



$db = $app['database'];

$session_state_count = $db->selectSessionState(['msidn' => $request['msidn']]);


if($session_state_count === 0)
{
    $db->insertSessionMsidn(['msidn' => $request['msidn']]);   


    echo displayWelcomeText();
   

}else {
 

    switch ($session_state_count) {

        case 1:

            if(substr($request['answer'], 1, 5) == '899*9')
            {

                echo displayWelcomeText(); 
            
            
            }elseif($request['answer'] == 1){


                //insert transaction type == register
               $db->updateSessionTransanctionType([
                    
                        'transaction_type' => 'Register_Personal_Pension',
                    
                        'msidn' => $request['msidn']
                        
                    ]);   

                $reply = "Register Personal Pension \r\n 1. New Member \r\n 2. Existing Tier 2 or PF Member";
              
                $type = 1;
              
                $cost = 0;

                echo $reply; 
            
            }elseif($request['answer'] == 2){

                   //insert transaction type == pay

                   $db->updateSessionTransanctionType([
                    
                    'transaction_type' => 'Pay_Personal_Pension',
                
                    'msidn' => $request['msidn']
                    
                ]);  ;   

                $reply = "Pay Personal Pension \r\n Enter Member ID";
              
                $type = 1;
              
                $cost = 0;

                echo $reply;

            }elseif($request['answer'] == 3){

                //insert transaction type == update

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => 'Update_Key_Data',
                
                    'msidn' => $request['msidn']
                    
                ]);  

                $reply = "Update Key Data \r\n Enter Member ID";
            
                $type = 1;
            
                $cost = 0;

                echo $reply;

            }elseif($request['answer'] == 4){

                //insert transaction type == Enquiries

                     $db->updateSessionTransanctionType([
                    
                        'transaction_type' => 'Enquiries',
                    
                        'msidn' => $request['msidn']
                        
                    ]);  

                $reply = "Enquiries \r\n 1. Check Mini Statement \r\n 2. Info About Scheme";
            
                $type = 1;
            
                $cost = 0;

                echo $reply;

            }else{

                $reply = "Invalid input";

                echo $reply;
            
                $type = 1;
            
                $cost = 0;

            }

            break;


        case 2:


           $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);



            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL,
                
                    'msidn' => $request['msidn']
                    
                ]);




                echo displayWelcomeText();
          
            
            }elseif($transactionType == 'Register_Personal_Pension' && $request['answer'] == 1){

                /* 
                    Call function to prompt user to enter surname
                */

                return insertBioData($db, $request);


        
            
            }elseif($transactionType == 'Register_Personal_Pension' && $request['answer'] == 2){

                $db->updateSessionTFields([
                    
                    'T1' => 'Selected_Existing_Tier',
                
                    'msidn' => $request['msidn']
                    
                ]);  

                $reply = "Existing Tier 2/PF Member \r\n Enter Member ID"; //write a 
              
                $type = 1;
              
                $cost = 0;
            
                echo $reply;

            }elseif($transactionType == 'Pay_Personal_Pension'){

                //insert member id 

                //findExistingMember($request);

                $db->findMember([
                    
                    'member_id' => $request['answser'],

                    'msidn' => $request['msidn'],
                    
                ]);



                //if found


                $db->updateSessionTFields([
                    
                    'T1' => 'Entered_Member_id',
                
                    'msidn' => $request['msidn']
                    
                ]); 

                #refactor later

               $reply = "Enter Amount";

               $type = 1;
              
               $cost = 0;  
               
               echo reply;



               #else return invalid input
              
        
            }

            break;

        case 3:

            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);

            $T1 = $db->getTField(['T1','msidn' => $request['msidn']]);

            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL, 'T1' => NULL,
                
                    'msidn' => $request['msidn']
                    
                ]);


                resetbioData($db, $reset); #reset bio data if something has been inseted for this sesion

                
                echo displayWelcomeText();



            }elseif($transactionType == 'Register_Personal_Pension' && $T1 == 'Inserted_Member_id'){


                #call function to display bio dataprompt


               insertBioData($db, $request);


            }elseif($transactionType == 'Register_Personal_Pension' && $T1 == 'Selected_Existing_Tier'){

                //insert member id 

               $member_id =  $db->findMember([
    
                    'member_id' => $request['answer'],

                    'msidn' => $request['msidn'],
                    
                ]);
                
                if(!empty($member_id))
                {
                    $db->updateSessionTFields([
                    
                        'T2' => 'Entered_Member_ID',
                    
                        'msidn' => $request['msidn']
                        
                    ]); 


                    $reply = "1. View Key Data \r\n 2. Cancel";
                
                }else{

                    $reply = "Invalid Member ID! Try again ";

                }
               

                #refactor later


                $type = 1;
                
                $cost = 0;  
                
                echo $reply;



            }elseif($transactionType == 'Pay_Personal_Pension'){


                //persist amount in db


                $db->updateSessionTFields([
                    
                    'T2' => 'Entered_Amount',
                
                    'msidn' => $request['msidn']
                    
                ]); 


               $reply = "Enter Mobile Money Pin";
               
               $type = 1;
              
               $cost = 0;  
               
               echo reply;


            }

            break;
    
        case 4:

            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);


            $T2 = $db->getTField(['T2','msidn' => $request['msidn']]);


            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL,

                    'T2' => NULL,
                
                    'msidn' => $request['msidn']
                    
                ]);

                resetbioData($db, $request);
                
                echo displayWelcomeText();


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T2 == 'Inserted_Surname'){

                /* 
                *      
                *     call function to insert request input in appropirate input
                */

                insertBioData($db, $request);

            }elseif($transactionType == 'Register_Personal_Pension' &&  $T2 == 'Entered_Member_ID'){

                if($request['answer'] == 1)
                {

                    $bio_data = $db->fetchBioData(['msidn' => $request['msidn']]);

                    $data = [
    
                        'member_id' => $bio_data['member_id'],
    
    
                        'name' =>  $bio_data['firstname'].' '.$bio_data['other_names'].' '.$bio_data['surname'],
    
    
                        'dob' => $bio_data['dob'],
    
    
                        'gender' => $bio_data['gender'],
    
    
                        'nationality' => $bio_data['nationality']
    
    
                    ];

    
                    $db->updateSessionTFields([
                        
                        'T3' => 'Viewed_BioData',
                    
                        'msidn' => $request['msidn']
                        
                    ]); 
    
                    
    
                    $reply = sprintf("Personal Pension Details \r\n ------------------------
                    Member ID: \t %s \r\n Name: \t %s  \r\n D.O.B: \t %s \r\n Gender: \t %s \r\n Nationality: \t %s \r\n\r\n 1. Press to Approve", $data['member_id'], $data['name'], $data['dob'], $data['gender'], $data['nationality']);
                    /* implode(',', array_values($data))) */;


                    echo $reply;

                    $cost = 0;

                    $type = 1;

                }else{

                    $db->deleteSession(['msidn' => $request['msidn']]);

                    $cost = 0;

                    $type = 3;
                }

                
             

            }elseif($transactionType == 'Pay_Personal_Pension' &&  $T2 == 'Inserted_Member_id'){

                #process payment 

                $db->updateSessionTFields([
                    
                    'T3' => 'Enter_Mobile_Money_Pin',
                
                    'msidn' => $request['msidn']
                    
                ]); 


                #confirm payment

                $reply = "You have transferred an amount";

            }

         break;

        case 5:


            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);


            $T3 = $db->getTField(['T3','msidn' => $request['msidn']]);


            
            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL,
                
                    'msidn' => $request['msidn']
                    
                ]);

                resetbioData($db, $request);

                
                echo displayWelcomeText();


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T3 == 'Inserted_Firstname'){


                /* 
                *      
                *     call function to insert request input in appropirate input
                */

                insertBioData($db, $request);

            }elseif($transactionType == 'Register_Personal_Pension' &&  $T3 == 'Viewed_BioData'){


                $db->approveBioData([
        
                    'verified_at' => Date('Y-m-d H:i:s'),

                    'msidn' => $request['msidn']

                ]);


                $db->deleteSession(['msidn' => $request['msidn']]);


                $reply = "Approved account Details";

                echo $reply;

                $type = 3;

                $cost = 0;


            }

            break;

        case 6:



            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);


            $T4 = $db->getTField(['T4','msidn' => $request['msidn']]);



            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL, 
                    
                    'T1' => NULL, 'T2' => NULL,
                    
                    'T3' => NULL, 'T4' => NULL,
                
                    'msidn' => $request['msidn']
                    
                ]);


                resetbioData($db, $request);

                
                echo displayWelcomeText();


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T4 == 'Inserted_Other_names'){


                /* 
                *      
                *     call function to insert request input in appropirate input
                */

                insertBioData($db, $request);

            }
            break;

        case 7:


            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);


            $T5 = $db->getTField(['T5','msidn' => $request['msidn']]);


            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL,
                    
                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL,
                    
                    'T4' => NULL,  'T5' => NULL,
                
                    'msidn' => $request['msidn']
                    
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

                    $db->clearIncompleteSessionBioData(['msidn' => $request['msidn']]);
                }


                
                echo displayWelcomeText();


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T5 == 'Inserted_dob'){


                /* 
                *      
                *     call function to insert request input in appropirate input
                */

                insertBioData($db, $request);

            }
            break;
        case 8;


            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);


            $T6 = $db->getTField(['T6','msidn' => $request['msidn']]);


            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL,


                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL, 
                    
                    'T4' => NULL, 'T5' => NULL, 'T6' => NULL, 

                    'msidn' => $request['msidn']
                    
                ]);


                resetbioData($db, $request);

                
               echo  displayWelcomeText();


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T6 == 'Inserted_Gender'){

                /* 
                *      
                *     call function to insert request input in appropirate input
                */

                insertBioData($db, $request);

            }
        break;

        case 9;


            $transactionType = $db->getTransactionType(['msidn' => $request['msidn']]);


            $T7 = $db->getTField(['T7','msidn' => $request['msidn']]);


            if(substr($request['answer'], 1, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the appropirate switch case 
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => NULL,

                    'T1' => NULL, 'T2' => NULL, 'T3' => NULL, 
                    
                    'T4' => NULL, 'T5' => NULL, 'T6' => NULL, 
                    
                    'T7' => NULL, 
                                
                    'msidn' => $request['msidn']
                    
                ]);

                resetbioData($db, $request);

                
                echo displayWelcomeText();


            }elseif($transactionType == 'Register_Personal_Pension' &&  $T7 == 'Inserted_Nationality'){

                /* 
                *      
                *     call function to insert request input in appropirate input
                */

                insertBioData($db, $request);

            }

        break;

    }
}


function insertBioData($db, $request)
{

    # get the sum of inserted fields on bio data table for rhis session 


   $field_count = $db->selectBioDataCount(['msidn' => $request['msidn']]);
  

    if($field_count === 0)
    {


        $db->insertInitialBioData([

            'member_id' => substr(sha1(time()), 0, 5),

            'msidn' => $request['msidn']

        ]);



        $db->updateSessionTFields([
                    
            'T1' => 'Inserted_Member_id',
                
            'msidn' => $request['msidn']
            
        ]);


        $reply = "Enter Surname";

        echo $reply;

        $type = 1;
    
        $cost = 0;
    
    }elseif($field_count === 1){

        
        $db->insertOtherBioData(['surname' => $request['answer'], 'msidn' => $request['msidn']]);



        $db->updateSessionTFields([
                    
            'T2' => 'Inserted_Surname',
                
            'msidn' => $request['msidn']
            
        ]);
        

        $reply = "Enter first name";

        $type = 1;
    
        $cost = 0;


        echo $reply;
    
    
    }elseif($field_count === 2){


        $db->insertOtherBioData(['firstname' => $request['answer'],  'msidn' => $request['msidn']]);

        $db->updateSessionTFields([
                    
            'T3' => 'Inserted_Firstname',
                
            'msidn' => $request['msidn']
            
        ]);

        $reply = "Enter Other names Or press 1. to skip";

        echo $reply;

        $type = 1;
    
        $cost = 0;

    
    }elseif($field_count === 3){


        $db->insertOtherBioData([

            'other_names' => $request['answer'] == 1 ? '' : $request['answer'],

            'msidn' => $request['msidn']

        ]);


        $db->updateSessionTFields([
                    
            'T4' => 'Inserted_Other_names',
                
            'msidn' => $request['msidn']
            
        ]);



        $reply = "Enter Date of Birth (YYYY-MM-DD)";

        echo $reply;

        $type = 1;
    
        $cost = 0;


    }elseif($field_count === 4){

      
        
        $db->insertOtherBioData(['dob' => date_format(date_create($request['answer']), "Y-m-d"), 'msidn' => $request['msidn']]);



        $db->updateSessionTFields([
                    
            'T5' => 'Inserted_dob',
                
            'msidn' => $request['msidn']
            
        ]);

        $reply = "Select Gender \r\n 1. Male \r\n 2. Female";

        echo $reply;

        $type = 1;
    
        $cost = 0;


    }elseif($field_count === 5){


        if($request['answer'] == 1)
        {

            $db->insertOtherBioData(['gender' => 'Male', 'msidn' => $request['msidn']]);


        }elseif($request['answer'] == 2){


            $db->insertOtherBioData(['gender' => 'Female', 'msidn' => $request['msidn']]);

        }



        $db->updateSessionTFields([
                    
            'T6' => 'Inserted_Gender',
                
            'msidn' => $request['msidn']
            
        ]);


        $reply = "Enter Nationality";

        echo $reply;

        $type = 1;
    
        $cost = 0;

      

        
    
    }elseif($field_count === 6){


        $db->insertOtherBioData(['nationality' => $request['answer'], 'msidn' => $request['msidn']]);

            /*      $db->updateSessionTFields([
                                
                        'T7' => 'Inserted_Nationality',
                            
                        'msidn' => $request['msidn']
                        
                    ]);
            */
        $member_id = $db->getMemberID(['msidn' => $request['msidn']]);

        $db->deleteSession(['msidn' => $request['msidn']]);


        $reply = "Congratulations, You've registered with QLAC FINANCIAL TRUST LTD \r\n Your Member ID: {$member_id}";

        echo $reply;

        $type = 3;
    
        $cost = 0;

        
    }

    

}



function displayWelcomeText()
{
    return $reply = "Welcome to QLAC FINANCIAL TRUST LTD \r\n 1. Register Personal Pension \r\n 2. Pay Personal Pension \r\n 3. Update Key Data \r\n 4. Enquiries";

    $type = 1;

    $cost = 0;

}



function resetbioData($db, $request)
{
    /* 
    *     if transaction type == Register Personal Pension 
    *      
    *     clear incomplete biodata for this session
    *
    *     restart the flow   
    */

    $T1 = $db->getTField(['T1','msidn' => $request['msidn']]);


    if($transactionType == 'Register_Personal_Pension' && $T1 == 'Inserted_Member_id')
    {

        $db->clearIncompleteSessionBioData(['msidn' => $request['msidn']]);
    }

}