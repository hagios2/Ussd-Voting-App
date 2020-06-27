<?php


function addAddressInformation($ussd, $msisdn, $tField, $data)
{

 
    if($tField == '') #
    {

            $reply = 'Enter Email';
    
    }elseif($tField == 'Selected_Update_Address_Information'){

        
        $ussd->insertOtherBioData(['email' => $data, 'msisdn' => $msisdn]);


        $ussd->updateSessionTFields([
                
            'T3' => 'Entered_Email',
        
            'msisdn' => $msisdn
            
        ]); 

        $reply = 'Enter Residential Address';

    }elseif($tField == 'Entered_Email'){


        $ussd->insertOtherBioData(['residential_address' => $data, 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                
            'T4' => 'Entered_Residential_Address',
        
            'msisdn' => $msisdn
            
        ]); 

        $reply = 'Enter Postal Address';

    }elseif($tField == 'Entered_Residential_Address'){

        $ussd->insertOtherBioData(['postal_address' => $data, 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                
            'T5' => 'Entered_Postal_Address',
        
            'msisdn' => $msisdn
            
        ]); 

        $reply = 'Enter Hometown or Region';

    }elseif($tField == 'Entered_Postal_Address'){

        $ussd->insertOtherBioData(['hometown_or_region' => $data, 'msisdn' => $msisdn]);

        $reply = 'Address information saved';

        $ussd->deleteSession(['msisdn' => $msisdn]);

    }


    return $reply;
}



function addEmergencyContanctInformation($ussd, $msisdn, $tField, $data)
{
 
    if($tField == '')
    {

        $reply = "Enter emergency contact name";
    
    
    }elseif($tField == 'Selected_Emergency_Contact_Information'){ 

          $ussd->insertOtherBioData(['emergency_contact_name' => $data, 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                
            'T3' => 'Entered_Emergency_Contant_Name',
        
            'msisdn' => $msisdn
            
        ]);

        $reply = "Enter emergency contact number";

    }elseif($tField == 'Entered_Emergency_Contant_Name'){

        
        $ussd->insertOtherBioData(['emergency_contact_phone' => $data, 'msisdn' => $msisdn]);

        $ussd->deleteSession(['msisdn' => $msisdn]);

        $reply = "Emergency Contact Information Saved";
    }


    return $reply;

}



function addIdentityInformation($ussd, $msisdn, $tField, $data)
{
 
    if($tField == '')
    {

        $reply = "Identity Information \r\n ------------------- \r\n 1. Passport \r\n 2. Ghana Card \r\n 3. Driver License \r\n 4. Voter Id";
    
    
    }elseif($tField == 'Selected_Update_Identity_Information'){
    
        if($data == 1){

            $ussd->insertOtherBioData(['id_card_type' => 'Passport', 'msisdn' => $msisdn]);


            $ussd->updateSessionTFields([
                    
                'T3' => 'Selected_Card_Type',
            
                'msisdn' => $msisdn
                
            ]); 

            $reply = 'Enter ID No.';

        }elseif($data == 2){

            $ussd->insertOtherBioData(['id_card_type' => 'Ghana Card', 'msisdn' => $msisdn]);


            $ussd->updateSessionTFields([
                    
                'T3' => 'Selected_Card_Type',
            
                'msisdn' => $msisdn
                
            ]); 

            $reply = 'Enter ID No.';
                    
        }elseif($data == 3){
                
            $ussd->insertOtherBioData(['id_card_type' => 'Driver\'s License', 'msisdn' => $msisdn]);   


            $ussd->updateSessionTFields([
                    
                'T3' => 'Selected_Card_Type',
            
                'msisdn' => $msisdn
                
            ]); 

            $reply = 'Enter ID No.';
                        
        }elseif($data == 4){

            $ussd->insertOtherBioData(['id_card_type' => 'Voter Id', 'msisdn' => $msisdn]);   

            $ussd->updateSessionTFields([
                    
                'T3' => 'Selected_Card_Type',
            
                'msisdn' => $msisdn
                
            ]); 

            $reply = 'Enter ID No.';
                    
        }else{

            $reply = 'Invalid input! Try agian.';
        }


    }elseif($tField == 'Selected_Card_Type'){

        $ussd->insertOtherBioData(['id_number' => $data, 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                
            'T4' => 'Entered_Id_No',
        
            'msisdn' => $msisdn
            
        ]); 

        $reply = 'Enter Occupation';

    }elseif($tField == 'Entered_Id_No'){

        $ussd->insertOtherBioData(['occupation' => $data, 'msisdn' => $msisdn]);

        $ussd->updateSessionTFields([
                
            'T5' => 'Entered_Occupation',
        
            'msisdn' => $msisdn
            
        ]); 

        $reply = "Marital Status \r\n -------------- \r\n 1. Single \r\n 2. Married";

    }elseif($tField == 'Entered_Occupation'){

        if($data == 1)
        {
            $ussd->insertOtherBioData(['marital_status' => 'Single', 'msisdn' => $msisdn]);

            $ussd->updateSessionTFields([
                    
                'T6' => 'Selected_Marital_status',
            
                'msisdn' => $msisdn
                
            ]);

            $reply = "Identity Information Saved";
        
        }elseif($data == 2){

            $ussd->insertOtherBioData(['marital_status' => 'Married', 'msisdn' => $msisdn]);

            $ussd->updateSessionTFields([
                    
                'T6' => 'Selected_Marital_status',
            
                'msisdn' => $msisdn
                
            ]); 

            $reply = "Identity Information Saved";
        
        }else{

            $reply = "Invalid Input";
        }   

    }

    return $reply;
}



function addBeneficiaryInfomation($ussd, $msisdn, $tField, $data)
{
    
 
    if($tField == '')
    {
        $reply = "Press 1. to Select Schmeme";
    
    }elseif($tField == 'Selected_Update_Benficiaries'){



    }

    return $reply;
}