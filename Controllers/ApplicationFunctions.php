<?php
Require 'Database.php';
/**
*This class contains core logic of the USSD application
*
**/

class ApplicationFunctions{
	
	public function __construct(){

	}

	public function __destruct(){

	}

	/**
	* Method to verify a user PIN.
	*@param msisdn,pin
	*@return boolean
	*/
	public function verifyPin($msisdn,$pin){
		$netCode = substr($msisdn, 0, 5);
		$exec = "java -jar /opt/lampp/htdocs/ecoussd/EcoClient.jar 1 ".$msisdn." ".$netCode." ".$pin;
		$shell = shell_exec($exec);

		$xml = $this->get_string($shell, 'postXMLMessageResponse{rppostXMLMessage=', '; }');

		$pinStatus = $this->xml_to_array($xml,'RESPONSEMESSAGE');

		if($pinStatus == "PINVALIDATION SUCCESSFUL"){
			return TRUE;
		}
		else{
			return $pinStatus;
		}

		// return ("1234"==$pin)? TRUE:FALSE;
	}

	function xml_to_array($xml,$main_heading = '') {
	    $deXml = simplexml_load_string($xml);
	    $deJson = json_encode($deXml);
	    $xml_array = json_decode($deJson,TRUE);
	    if (! empty($main_heading)) {
	        $returned = $xml_array[$main_heading];
	        return $returned;
	    } else {
	        return $xml_array;
	    }
	}

	function get_string($string, $start, $end) {
		$string = " " . $string;
		$pos = strpos($string, $start);
		if ($pos == 0)
			return "";
		$pos += strlen($start);
		$len = strpos($string, $end, $pos) - $pos;
		return substr($string, $pos, $len);
	}

    /**
     *Method to start new USSD session
     *@param msisdn
     *@return Boolean
     */
    public function IdentifyUserStartimes($sessionId)
    {
        $db = Database::getInstance();
        try

        {
            $stmt = $db->prepare("insert into startimes(sessionId) values (:sessionId)");
            $stmt->bindParam(":sessionId",$sessionId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                return TRUE;
            }
        } catch (PDOException $e) {
            #$e->getMessage();
            return FALSE;
        }
    }



    /**
     *Method to start new USSD session
     *@param msisdn
     *@return Boolean
     */
    public function IdentifyUserInitial($msisdn)
    {
        $db = Database::getInstance();
        try

        {
            $stmt = $db->prepare("insert into servicemanager(msisdn) values (:msisdn)");
            $stmt->bindParam(":msisdn",$msisdn);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                return TRUE;
            }
        } catch (PDOException $e) {
            #$e->getMessage();
            return FALSE;
        }
    }




    public function RegisterVoter($msisdn,$sessionID)
    {
        file_put_contents("/var/www/html/ussd/899/mtn/musicawards_access.log",'Touch Down'.PHP_EOL,FILE_APPEND);
        try

        {
            $connection = mysqli_connect("95.216.10.33", "stealBars_of_Jerico", "H3dFzp2DSa9S@9i", "ussd", "3327");

            $stmt ="insert into vote (msisdn,sessionID) values ($msisdn , $sessionID)";
            $execute=mysqli_query($connection,$stmt);


            if($execute)
            {
                file_put_contents("/var/www/html/ussd/899/mtn/musicawards_access.log",'Insert worked'.PHP_EOL,FILE_APPEND);
                return TRUE;
            }
        }
        catch (PDOException $e) {
            file_put_contents("/var/www/html/ussd/899/mtn/musicawards_access.log","Insert failed $stmt | $e".PHP_EOL,FILE_APPEND);
            #$e->getMessage();
            return FALSE;
        }
    }



	/**
	*Method to start new USSD session
	*@param msisdn
	*@return Boolean
	*/	
	public function IdentifyUser($msisdn)
	{
		$db = Database::getInstance(); 
		try

		{
			$stmt = $db->prepare("insert into sessionmanager(msisdn) values (:msisdn)");
			$stmt->bindParam(":msisdn",$msisdn);
			$stmt->execute();
			
			if($stmt->rowCount() > 0)
			{ 
				return TRUE;
			}
		} catch (PDOException $e) {
			#$e->getMessage();
			return FALSE;
		}		  
	}

    /**
     *Method to delete a user session
     *@param msisdn
     *@return Boolean
     */
    public function deleteService($msisdn)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("Delete FROM servicemanager where msisdn= :msisdn");
            $stmt->bindParam(":msisdn",$msisdn);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                return TRUE;
            }

        } catch (PDOException $e) {
            #echo $e->getMessage();
            return FALSE;
        }
    }

	/**
	*Method to delete a user session 
	*@param msisdn
	*@return Boolean
	*/
	public function deleteSession($msisdn)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("Delete FROM sessionmanager where msisdn= :msisdn");
			$stmt->bindParam(":msisdn",$msisdn);
			$stmt->execute(); 
			
			if($stmt->rowCount() > 0)
			{ 
				return TRUE;
			} 
			
		} catch (PDOException $e) {
			#echo $e->getMessage();
			return FALSE;
		}
	}

	public function deleteAllSession($msisdn)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("UPDATE sessionmanager SET transaction_type = NULL, T1 = NULL, T2 = NULL, T3 = NULL, T4 = NULL, T5 = NULL, T6 = NULL, T7 = NULL,T8 = NULL where msisdn= :msisdn");
			$stmt->bindParam(":msisdn",$msisdn);
			$stmt->execute(); 
			
			if($stmt->rowCount() > 0)
			{ 
				return TRUE;
			} 
			
		} catch (PDOException $e) {
			#echo $e->getMessage();
			return FALSE;
		}
	}

    /**
     *Method to update user session with the actual type of transaction or details of the transaction *currently being held
     *@param msisdn, collumn, transaction type
     *@param Boolean
     **/
    public function UpdateServiceType($msisdn, $col, $trans_type)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("update servicemanager set " .$col. " = :trans_type where msisdn = :msisdn");
            $params = array(":msisdn"=>$msisdn,":trans_type"=>$trans_type);
            $stmt->execute($params);


            if($stmt->rowCount() > 0)
            {
                return true;
            }

        } catch (PDOException $e) {
            #echo $e->getMessage();
            return FALSE;
        }
    }

	/**
	 *Method to update user session with the actual type of transaction or details of the transaction *currently being held
	 *@param msisdn, collumn, transaction type
	 *@param Boolean
	 **/
	public function UpdateTransactionTypeStartimes($sessionId, $col, $trans_type)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("update startimes set " .$col. " = :trans_type where sessionId = :sessionId");
			$params = array(":sessionId"=>$sessionId,":trans_type"=>$trans_type);
			$stmt->execute($params);


			if($stmt->rowCount() > 0)
			{
				return true;
			}

		} catch (PDOException $e) {
			$error= $e->getMessage();
			$write = "ERROR|Startimes_Request|" . $error. PHP_EOL;
			file_put_contents('../../ussd_access.log', $write, FILE_APPEND);
			return FALSE;
		}
	}

	/**
	 *Method to update user session with the actual type of transaction or details of the transaction *currently being held
	 *@param msisdn, collumn, transaction type
	 *@param Boolean
	 **/
	public function UpdateTransactionStartimes($transactionId, $col, $trans_type)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("update startimes set " .$col. " = :trans_type where transactionId = :transId");
			$params = array(":transId"=>$transactionId,":trans_type"=>$trans_type);
			$stmt->execute($params);


			if($stmt->rowCount() > 0)
			{
				return true;
			}

		} catch (PDOException $e) {
			$error= $e->getMessage();
			$write = "ERROR|Startimes_Request|" . $error. PHP_EOL;
			file_put_contents('../../ussd_access.log', $write, FILE_APPEND);
			return FALSE;
		}
	}

	/**
	*Method to update user session with the actual type of transaction or details of the transaction *currently being held
	*@param msisdn, collumn, transaction type
	*@param Boolean
	**/	
	public function UpdateTransactionType($msisdn, $col, $trans_type)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("update sessionmanager set " .$col. " = :trans_type where msisdn = :msisdn");
			$params = array(":msisdn"=>$msisdn,":trans_type"=>$trans_type); 
			$stmt->execute($params);
			

			if($stmt->rowCount() > 0)
			{ 
				return true;
			}
			
		} catch (PDOException $e) {
			#echo $e->getMessage();
			return FALSE;
		}
	}

    /**
     *Method to query specific details from the session manage
     *@param msisdn, specific column to query
     *@return string
     */
    public function GetServiceType($extension)
    {
        $db = Database::getInstance();
        try
        {
			$write ="APP|" . $extension . "|SELECT service FROM  services WHERE  extension = '$extension'"  . PHP_EOL;
			file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

            $stmt = $db->query("SELECT service FROM  services WHERE  extension = '$extension'");
//            $stmt->bindParam("i",$extension);
//            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res['service'];
//                return $res[service];
            }else{
				$write ="APP|query failed". PHP_EOL;
				file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

			}

        } catch (PDOException $e) {
            $RE= $e->getMessage();
			$write ="APP|" . $RE  . PHP_EOL;
			file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

			return NULL;
        }
    }

    /**
     *Method to query specific details from the session manage
     *@param msisdn, specific column to query
     *@return string
     */
    public function GetDynamicType($extension)
    {
        $db = Database::getInstance();
        try
        {
            $write ="APP|" . $extension . "|SELECT dynamic FROM  services WHERE  extension = '$extension'"  . PHP_EOL;
            file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

            $stmt = $db->query("SELECT dynamic FROM  services WHERE  extension = '$extension'");

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res['dynamic'];
//                return $res[service];
            }else{
                $write ="APP|query failed". PHP_EOL;
                file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

            }

        } catch (PDOException $e) {
            $RE= $e->getMessage();
            $write ="APP|" . $RE  . PHP_EOL;
            file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

            return NULL;
        }
    }

    /**
     *Method to query specific details from the session manage
     *@param msisdn, specific column to query
     *@return string
     */
    public function GetTransactionService($msisdn, $col)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("SELECT " .$col. " FROM  servicemanager WHERE  msisdn = :msisdn");
            $stmt->bindParam(":msisdn",$msisdn);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res[$col];
            }

        } catch (PDOException $e) {
            #echo $e->getMessage();
            return NULL;
        }
    }

	/**
	 *Method to query specific details from the session manage
	 *@param msisdn, specific column to query
	 *@return string
	 */
	public function GetTransactionTypeStartimes($transactionId, $col)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("SELECT " .$col. " FROM  sessionmanager WHERE  transactionId = :transactionId");
			$stmt->bindParam(":transactionId",$transactionId);
			$stmt->execute();

			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if($res !== FALSE)
			{
				return $res[$col];
			}

		} catch (PDOException $e) {
			#echo $e->getMessage();
			return NULL;
		}
	}

	/**
	 *Method to query specific details from the session manage
	 *@param msisdn, specific column to query
	 *@return string
	 */
	public function GetTransactionStartimes($transactionId, $col)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("SELECT " .$col. " FROM  startimes WHERE  transactionId = :transactionId");
			$stmt->bindParam(":transactionId",$transactionId);
			$stmt->execute();

			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if($res !== FALSE)
			{
				return $res[$col];
			}

		} catch (PDOException $e) {
			#echo $e->getMessage();
			return NULL;
		}
	}

	/**
	*Method to query specific details from the session manage
	*@param msisdn, specific column to query
	*@return string
	*/	
	public function GetTransactionType($msisdn, $col)
	{
		$db = Database::getInstance();
		try
		{
//			$stmt = $db->prepare("SELECT " .$col. " FROM  sessionmanager WHERE  msisdn = :msisdn");
//			$stmt->bindParam(":msisdn",$msisdn);
//			$stmt->execute();
//
//			$res = $stmt->fetch(PDO::FETCH_ASSOC);
//
//			if($res !== FALSE)
//			{
//				return $res[$col];
//			}


            $stmt = $db->query("SELECT $col FROM  sessionmanager WHERE  msisdn = '$msisdn'");
//            $stmt->bindParam("i",$extension);
//            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res[$col];
//                return $res[service];
            }else{
                $write ="APP|query failed". PHP_EOL;
                file_put_contents('../../ussd_access.log', $write, FILE_APPEND);

            }

        } catch (PDOException $e) {
			#echo $e->getMessage();
			return NULL;
		}
	}

	public function GetAccData($msisdn, $col, $type)
	{
		$db = Database::getInstance();
		try
		{
			$stmt = $db->prepare("SELECT " .$col. " FROM  accounts WHERE  msisdn = :msisdn AND accountType = :type");
			$stmt->bindParam(":msisdn",$msisdn);
			$stmt->bindParam(":type",$msisdn);
			$stmt->execute();

			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if($res !== FALSE)
			{
				return $res[$col];
			}
			
		} catch (PDOException $e) {
			#echo $e->getMessage();
			return NULL;
		}
	}

	/**
	*Method to get the balance of the user
	*@param msisdn
	*@return string
	*/

    public function  serviceManager($msisdn)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("SELECT (COUNT(msisdn)+ COUNT(transaction_type)+ COUNT(T1)+ COUNT(T2)+ COUNT(T3)+ COUNT(T4)+ COUNT(T5)+ COUNT(T6)+ COUNT(T7)) AS counter FROM servicemanager WHERE msisdn = :msisdn");
            $stmt->bindParam(":msisdn",$msisdn);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res['counter'];
            }

        } catch (PDOException $e) {
            #echo $e->getMessage();
            return NULL;
        }
    }

    /**
     *Method to get the current state of the user
     *@param msisdn
     *@return integer
     */
    public function  sessionManagerStartimes($transactionId)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("SELECT (COUNT(id)+ COUNT(msisdn)+ COUNT(smartcard)+ COUNT(amount)+ COUNT(created)+ COUNT(network)+ COUNT(transactionId)+ COUNT(sessionId)+ COUNT(smartcardStatus)+ COUNT(smartcardName)+ COUNT(initialPaymentReply)+ COUNT(callbackPaymentReply)+ COUNT(rechargeReply)) AS counter FROM startimes WHERE transactionId = :transactionId");
            $stmt->bindParam(":transactionId",$transactionId);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res['counter'];
            }

        } catch (PDOException $e) {
            #echo $e->getMessage();
            return NULL;
        }
    }

	/**
  	*Method to get the current state of the user
  	*@param msisdn
  	*@return integer
  	*/
  	public function  sessionManager($msisdn)
  	{		
  		$db = Database::getInstance();
  		try
  		{
  			$stmt = $db->prepare("SELECT (COUNT(msisdn)+ COUNT(transaction_type)+ COUNT(T1)+ COUNT(T2)+ COUNT(T3)+ COUNT(T4)+ COUNT(T5)+ COUNT(T6)+ COUNT(T7)+ COUNT(T8)+ COUNT(T9)+ COUNT(T10)+ COUNT(T11)+ COUNT(T12)) AS counter FROM sessionmanager WHERE msisdn = :msisdn");
  			$stmt->bindParam(":msisdn",$msisdn);
  			$stmt->execute();

  			$res = $stmt->fetch(PDO::FETCH_ASSOC);

  			if($res !== FALSE)
  			{
  				return $res['counter'];
  			}
  			
  		} catch (PDOException $e) {
			#echo $e->getMessage();
  			return NULL;
  		}
  	}

  	public function getAccts($msisdn)
  	{
  		$db = Database::getInstance();

  		try
  		{
  			$stmt = $db->prepare("SELECT customerID FROM users WHERE msisdn = :msisdn");
  			$stmt->bindParam(":msisdn",$msisdn);
  			$stmt->execute();
  			$customerAssoc = $stmt->fetch(PDO::FETCH_ASSOC);
  			$customerID = $customerAssoc['customerID'];

  			$acctArray = array();

  			$stmt2 = $db->prepare("SELECT accountType, accountAlias FROM accounts WHERE customerID = :customerID AND accountType <> 'IACCOUNT'");
  			$stmt2->bindParam(":customerID",$customerID);
  			$stmt2->execute();

  			while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){
  				
  				$acctType = $row['accountType'];
  				$acctAlias = $row['accountAlias'];
  				$acctDetails = $acctType."|".$acctAlias;
  				array_push($acctArray, $acctDetails);
  			}

  			return $acctArray;
  			
  		} catch (PDOException $e) {
			#echo $e->getMessage();
  			return NULL;
  		}


  	}

  	public function getAllAccts($msisdn)
  	{
  		$db = Database::getInstance();

  		try
  		{
  			$stmt = $db->prepare("SELECT customerID FROM users WHERE msisdn = :msisdn");
  			$stmt->bindParam(":msisdn",$msisdn);
  			$stmt->execute();
  			$customerAssoc = $stmt->fetch(PDO::FETCH_ASSOC);
  			$customerID = $customerAssoc['customerID'];

  			$acctArray = array();

  			$stmt2 = $db->prepare("SELECT accountType, accountAlias FROM accounts WHERE customerID = :customerID");
  			$stmt2->bindParam(":customerID",$customerID);
  			$stmt2->execute();

  			while($row = $stmt2->fetch(PDO::FETCH_ASSOC)){
  				
  				$acctType = $row['accountType'];
  				$acctAlias = $row['accountAlias'];
  				$acctDetails = $acctType."|".$acctAlias;
  				array_push($acctArray, $acctDetails);
  			}

  			return $acctArray;
  			
  		} catch (PDOException $e) {
			#echo $e->getMessage();
  			return NULL;
  		}


  	}

    public function write_log($content){
  $log_folder = dirname(__FILE__).DIRECTORY_SEPARATOR."LOG";
  if(!is_dir($log_folder))
    {
      mkdir($log_folder);
    }
  $date= date("d m Y");
  $time = date("H:i:s");
  $myfile = $log_folder.DIRECTORY_SEPARATOR.$date.".txt";
  if($resource = (file_exists($myfile) && is_file($myfile))?fopen($myfile,"ab"): fopen($myfile, "wb")){
    fwrite($resource, $time." | ".$content.PHP_EOL);
    fclose($resource);
  }
}

    /**
     *Method to get the current state of the user
     *@param msisdn
     *@return integer
     */
    public function newPayment($sessionId)
    {
        $db = Database::getInstance();
        try

        {
            $stmt = $db->prepare("insert into payments(sessionId) values (:sessionId)");
            $stmt->bindParam(":sessionId",$sessionId);
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                return TRUE;
            }
        } catch (PDOException $e) {
            #$e->getMessage();
            return FALSE;
        }
    }


    /**
     *Method to update user session with the actual type of transaction or details of the transaction *currently being held
     *@param msisdn, collumn, transaction type
     *@param Boolean
     **/
    public function UpdatePayments($sessionId, $col, $trans_type)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("update payments set " .$col. " = :trans_type where sessionId = :sessionId");
            $params = array(":sessionId"=>$sessionId,":trans_type"=>$trans_type);
            $stmt->execute($params);


            if($stmt->rowCount() > 0)
            {
                return true;
            }

        } catch (PDOException $e) {
            $error= $e->getMessage();
            $write = "ERROR|Payment update|" . $error. PHP_EOL;
            file_put_contents('../../ussd_access.log', $write, FILE_APPEND);
            return FALSE;
        }
    }

    public function UpdatePaymentsCallback($transactionId, $col, $trans_type)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("update payments set " .$col. " = :trans_type where transactionId = :sessionId");
            $params = array(":transactionId"=>$transactionId,":trans_type"=>$trans_type);
            $stmt->execute($params);


            if($stmt->rowCount() > 0)
            {
                return true;
            }

        } catch (PDOException $e) {
            $error= $e->getMessage();
            $write = "ERROR|Payment update|" . $error. PHP_EOL;
            file_put_contents('../../ussd_access.log', $write, FILE_APPEND);
            return FALSE;
        }
    }

    public function GetTransactionPayments($transactionId, $col)
    {
        $db = Database::getInstance();
        try
        {
            $stmt = $db->prepare("SELECT " .$col. " FROM  payments WHERE  transactionId = :transactionId");
            $stmt->bindParam(":transactionId",$transactionId);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            if($res !== FALSE)
            {
                return $res[$col];
            }

        } catch (PDOException $e) {
            #echo $e->getMessage();
            return NULL;
        }
    }







    /*
     * VOTING FUNCTIONS
     */



  }


//  $call=new ApplicationFunctions();
//
//echo $est=$call->RegisterVoter('233249430715','12344242424');