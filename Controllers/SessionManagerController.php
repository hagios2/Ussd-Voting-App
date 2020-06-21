<?php


/* 
class SessionManagerController
{



} */


$request = $_POST; 



$db = $app['database'];

$seesion_state_count = $db->selectSessionState(['msidn' => $_POST['msidn']]);


if($seesion_state_count === 0)
{
    $db->insertSessionMsidn(['msidn' => $_POST['msidn']]);   


    return displayWelcomeText();
   

}else {


    switch ($seesion_state_count) {

        case 1:

            if(substr($request['short_code'], 0, 5) == '899*9')
            {

                return displayWelcomeText();
            
            
            }elseif($request['answer'] == 1){

                //insert transaction type == register
                $db->updateSessionTransanctionType([
                    
                        'transaction_type' => 'Register_Personal_Pension',
                    
                        'msidn' => $_POST['msidn']
                        
                    ]);   

                $reply = "Register Personal Pension \r\n 1. New Member \r\n 2. Existing Tier 2 or PF Member";
              
                $type = 1;
              
                $cost = 0;
            
            }elseif($request['answer'] == 2){

                   //insert transaction type == pay

                   $db->updateSessionTransanctionType([
                    
                    'transaction_type' => 'Pay_Personal_Pension',
                
                    'msidn' => $_POST['msidn']
                    
                ]);  ;   

                $reply = "Pay Personal Pension \r\n Enter Member ID";
              
                $type = 1;
              
                $cost = 0;

            }elseif($request['answer'] == 3){

                //insert transaction type == update

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => 'Update_Key_Data',
                
                    'msidn' => $_POST['msidn']
                    
                ]);  

                $reply = "Update Key Data \r\n Enter Member ID";
            
                $type = 1;
            
                $cost = 0;

            }elseif($request['answer'] == 4){

                //insert transaction type == Enquiries

                     $db->updateSessionTransanctionType([
                    
                        'transaction_type' => 'Enquiries',
                    
                        'msidn' => $_POST['msidn']
                        
                    ]);  

                $reply = "Enquiries \r\n 1. Check Mini Statement \r\n 2. Info About Scheme";
            
                $type = 1;
            
                $cost = 0;

            }

            break;


        case 2:

            $transactionType = $db->getTransactionType(['msidn' => $_POST['msidn']]);


            if(substr($request['short_code'], 0, 5) == '899*9')
            {

                /* 
                *   find the msidn and clear its transaction type 
                *
                *   for the proceeding request to be handled by the *   appropirate switch case
                *
                *   return then welcome text   
                */

                $db->updateSessionTransanctionType([
                    
                    'transaction_type' => null,
                
                    'msidn' => $_POST['msidn']
                    
                ]);



                return displayWelcomeText();
          
            
            }elseif($transactionType == 'Register_Personal_Pension' && $request['answer'] == 1){


                $db->updateSessionTFields([
                    
                    'transaction_type' => null,
                
                    'msidn' => $_POST['msidn']
                    
                ]);


                //transaction type already exists in db
          

                $reply = "Register Personal Pension \r\n Enter Key bio data"; //write a function to the bio data
              
                $type = 1;
              
                $cost = 0;

            
            }elseif($transactionType == 'Register_Personal_Pension' && $request['answer'] == 2){


                //transaction type already exists in db
               // $db->insertSessionMsidn(['msidn' => $_POST['msidn']]);   

                $reply = "Existing Tier 2/PF Member \r\n Enter Member ID"; //write a 
              
                $type = 1;
              
                $cost = 0;
            


            }elseif($transactionType == 'Pay_Personal_Pension'){

                //insert member id 


                //transaction type already exists in db
               // $db->insertSessionMsidn(['msidn' => $_POST['msidn']]);   

               $reply = "Enter Amount";
              
                $type = 1;
              
                $cost = 0;    

            }/* elseif($request['answer'] == 2){

                //transaction type already exits in db

               // $db->insertSessionMsidn(['msidn' => $_POST['msidn']]);   

                $reply = "Pay Personal Pension \r\n Enter Key Bio data Information";
              
                $type = 1;
              
                $cost = 0;

            } */

            break;

        case 3:

            $transactionType = $db->getTransactionType(['msidn' => $_POST['msidn']]);

            if(substr($request['short_code'], 0, 5) == '899*9')
            {

                //find the msidn and clear its transaction type
                
                $reply = "Welcome to QLAC FINANCIAL TRUST LTD \r\n 1. Register Personal Pension \r\n 2. Pay Personal Pension 3. Update Key Data \r\n 4. Enquiries";

                $type = 1;
            
                $cost = 0;
            
        /*     
            }elseif($){

 */

            }



            break;
        default:
    }
}


function insertBioData($request)
{

    /* if()
    {

    } */

}



function displayWelcomeText()
{
    $reply = "Welcome to QLAC FINANCIAL TRUST LTD \r\n 1. Register Personal Pension \r\n 2. Pay Personal Pension 3. Update Key Data \r\n 4. Enquiries";

    $type = 1;

    $cost = 0;

}





