<?php 

	include("conciergemessagehelper.php");
	
$FromDate = '';
$ConnLink = '';


$type ='';
$data ='';

$loginName='';
$password='';

if(ISSET($_REQUEST['u']))
	$loginName = $_REQUEST['u'];
	
if(ISSET($_REQUEST['p']))
	$password = $_REQUEST['p'];	
	
$gSendMessageURL = "http://beta.mobikontech.com/konekt/service/konektapi.asmx/";
//$gSendMessageURL = "http://getkonekt.com/konekt/service/konektapi.asmx/";

if(ISSET($_REQUEST['tp']))
		$type = $_REQUEST['tp'];

if(ISSET($_REQUEST['d']))
		$data = $_REQUEST['d'];
			
		
	switch (strtoupper($type)) {
		case 'GAD':
			GetAccountDetails($xml); /* Not in use */
			break;
		case 'GBT':	
			//Get booked tables						
			GetBookings($data);
			break;
		case 'GCI':	
			//Get customer details					
			GetCustomerInfo();
			break;
		case 'GCHK':	
			//Get check in 
			GetCheckins($data);
			break;	
		case 'SCHKIOM':	
			//Set check in/out messages
			SetCheckinoutMessages($data);
			break;			
		case 'GAOFF':	
			//Get all offers
			GetAllOffers($data);
			break;
		case 'GBBOFF':	
			//Get offers based on booking criteria
			GetBookingBasedOffers($data);
			break;						
		case 'GCHKMSG':	
			//Get check in/out messages
			GetCheckinoutMessages($data);
			break;	
		case 'SCHKTBL':	
			//Set checkin
			SetCheckinTable($data);
			break;			
		case 'SRELTBL':	
			//Set Release
			SetReleaseTable($data);
			break;		
		case 'SCANTBL':	
			//Set cancel 
			SetCancelTablebooking($data);
			break;	
		case 'SBT':	
			//book table
			SetBookTable($data);
			break;
		
		case 'GSPREF':	
			//get seating preferences
			GetSeatingPreferences($data);
			break;			
		case 'GBN':	
			//get seating preferences Get booking notification other than source concierge.
			GetBookingNotification($data);
			break;
		case 'CS':	
			//get cover stats
			GetCoverStats($data);
			break;			
		case 'GRELTBL':	
			//get tables to release
			GetTablesToRelease($data);
			break;
		case 'GPREFTBL':	
			//get tables based on preference
			GetPreferencebasedTables($data);
			break;		
		case 'AUTH':	
			//authenticate login
			Authenticate($loginName,$password);
			break;
		case 'GCANBT':	
			//get cancelled booking details
			GetCancelledBookings($data);
			break;
		
		/* 		---------------------------------------------------------------------------------------		*/
			
		/*case 'AMOLSBT':	
			//book table
			AmolSetBookTable($data);
			break; */
		case 'PR': 
		   //Set Preferenses
		   SetPreferenses($data);
		   break;
		  case 'CO': 
		   //Set Concierge Offers
		   SetConciergeOffers($data);
		   break; 
		  case 'CU': 
		   //Set Concierge Users
		   SetConciergeUsers($data);
		   break;
		  case 'CTS': 
		   //Set Table Settings
		   SetTableSettings($data);
		   break;
		  case 'TAT': 
		   //Set Table turnaround time
		   SetTableTATSettings($data);
		   break;
		  case 'SCS': 
		   //Set Concierge Settings
		   SetConciergeSettings($data);
		   break;			
		  case 'SCSD': 
		   //Set Concierge Server Details
		   SetConciergeServerDetails($data);
		   break;
		case 'GCSD':	
			//Get Server Details
			GetServerDetails($data);
			break;		   
		   
	}
		
/*if($data != '' )
{
	//echo $data;
	//json_decode($json);
	

	
	$xml = LoadXml($data); 
	
	switch ($xml->tp) {
		case 'GAD':
			GetAccountDetails($xml);
			break;
		case 'BT':	
			//book table
			BookTable($xml);
			break;
		case 'GBT':	
			//Get booked tables						
			GetBookings($xml);
			break;
			
			
	}
}
else
{

	if(ISSET($_REQUEST['tp']))
		$type = $_REQUEST['tp'];
			
			if($type=='GCI')
			{				//Get customer details				
				GetCustomerInfo();
			}
			
}*/


function ConnectToMySQL() {
    $con = mysql_connect("192.168.1.52","conci","Mobikontech");			//Local
	//$con = mysql_connect("localhost:3307","root","Marijuana@77");		//Beta
	//$con = mysql_connect("localhost:3306","root","");					//Prod
    if (!$con)
    {
      die('Could not connect: ' . mysql_error());
    }

    return $con;
}

function ConnectToMySQLi() {
    
	$mysqli = new mysqli("192.168.1.52","conci","Mobikontech");			//Local
	//$mysqli =new mysqli("localhost:3307","root","Marijuana@77");		//Beta
	//$mysqli = new mysqli("localhost:3306","root","");					//Prod	
    if (mysqli_connect_errno())
    {
      die('Could not connect: ' . mysqli_connect_error());
    }

    return $mysqli;
}

  function ConnectToMssql()
  {
     $dsn="Driver={SQL Server Native Client 10.0};Server=64.15.155.142;Database=KonektApp;";		//Beta
     $username="demodb";
     $password="Marijuana@77";
	 try
	 {
		$SqlLink=odbc_connect($dsn,$username,$password);  //or die ("could not connect");
			
		if(!$SqlLink) 			 //$SqlLink === false
		{			
			//throw new Exception(odbc_error());				
			die('Could not connect');
		}
	 }
	catch (Exception $e) {											
			LogErr($e->getMessage());	

			$file = 'data.txt';				
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'con  '." ".$e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);
			
	}     
	
	  return $SqlLink;
   }
   


function LoadXml($xml_str)
{	
	$xml = simplexml_load_string($xml_str);
	return $xml;
	
}


function Authenticate($loginName,$password)
{
	$ConnLink=ConnectToMssql();
	
	try
	{	
		$sql = "EXEC dbo.spConciergeAppAuthUser '".str_replace("'","''",$loginName)."','".str_replace("'","''",$password)."'";  
		$hasRS = "0";
		$result=odbc_exec($ConnLink,$sql) or die(odbc_error());
		
		while ($row = odbc_fetch_array($result)) 
		{	
			$hasRS = "1";
			 $aResults[] = array( "oid"=>"","csid"=>$row['ConciergeSettingId'],"acid"=>$row['AccountId'] ,"eid"=>$row['EntityId'],"bkfs"=>$row['BreakfastStart'],"bkfe"=>$row['BreakfastEnd'],"ls"=>$row['LunchStart'],"le"=>$row['LunchEnd'],"ds"=>$row['DinnerStart'],"de"=>$row['DinnerEnd'],"cc"=>$row['CountryCallingCode'],"Success"=>1);			
		}
		
		if($hasRS === "0")
			$aResults[] = array("Success"=>0);
			
    	odbc_close($ConnLink);
	}
	catch(Exception $e)
	{
		$aResults[] = array("Success"=>0);
		odbc_close($ConnLink);
	}

	$json_response = json_encode($aResults);
    
	# Optionally: Wrap the response in a callback function for JSONP cross-domain support
	if(ISSET($_REQUEST["callback"])) {
	$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
	}
	# Return the response
	echo $json_response;
	
}

function GetAccountDetails($xml)
{		
		$oid = $xml->oid;
		
		$ConnLink = ConnectToMySQL();
		
		mysql_select_db("konekt", $ConnLink);
		
		$sql = "SELECT
				OID,
				OutletId,
				AccountId,
				EntityId,
				BreakfastEnd,
				BreakfastStart,
				LunchEnd,
				LunchStart,
				DinnerEnd,
				DinnerStart
				FROM konektconci.outlets
				WHERE OutletId = '".$oid."'";
		
		$result = mysql_query($sql);
		
		
		if (mysql_affected_rows()<=0) 
		{
			$Executing = 'true';			
		}
		else
		{
			while($row = mysql_fetch_array($result))
			{
				  $aResults[] = array( "oid"=>$row['OID'],"OutletId"=>$row['OutletId'] ,"AccountId"=>$row['AccountId'] ,"EntityId"=>$row['EntityId'] ,"BreakfastStart"=>$row['BreakfastStart'],"BreakfastEnd"=>$row['BreakfastEnd'],"LunchStart"=>$row['LunchStart'],"LunchEnd"=>$row['LunchEnd'],"DinnerStart"=>$row['DinnerStart'],"DinnerEnd"=>$row['DinnerEnd']);			  
			}
			
			/*while($row = mysql_fetch_array($result1))
				{
					$aResults[] = array( "custid"=>$row['custid'] ,"ctype"=>$row['ctype'],"name"=>$row['name'],"mobile"=>$row['mobile'],"email"=>$row['email'],"pax"=>$row['pax'],"btime"=>$row['btime'],"pref"=>$row['pref'],"tables"=>$row['tables'],"bdate"=>$row['bdate']);	
				} */	
					$json_response = json_encode($aResults);
				# Optionally: Wrap the response in a callback function for JSONP cross-domain support
					if(ISSET($_REQUEST["callback"])) {
					$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
					}
				# Return the response
				echo $json_response;
		}


		mysql_close($ConnLink);		
}


function GetCancelledBookings($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$BookingDate = $inputDecode->{'dt'};	

		$aResults ='';
		$ConnLink ='';

		try
		{
			$hasRS = "0";
			$ConnLink = ConnectToMssql();
		
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d');	
			

			$SQL = "EXEC spConciergeAppGetCancelledBookingDetails ".$ConciergeSettingId.",'".$cresult."'";
			$result = odbc_exec($ConnLink,$SQL);
		
			//$SQL = "EXEC spConciergeAppGetCancelledBookingDetails @pConciergeSettingId = ? ,@pBookingDate = ? ";
			
			//$params = array($ConciergeSettingId, $cresult);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
				if(!$result)
					throw new Exception(odbc_error($ConnLink));
								
					$cbdtls = '';
					
					while($row = odbc_fetch_array($result)){
						//echo $row['CID']. " - ". $row['ConciergeTableBookingRequestId'];
						$hasRS = "1";
						$cbdtls[] = array("CBId"=>$row['CBId']
										, "CSId"=>$row['CSId']
										#, "CustomerId"=>$row['CustomerId']
										#, "AccountId"=>$row['AccountId']
										#, "EntityId"=>$row['EntityId']
										, "CustomerName"=>$row['Name']
										, "Gender"=>$row['Gender']
										, "EmailId"=>$row['EmailId']
										, "CountryCallingCode" => $row['CountryCallingCode']
										, "Cell_Number"=>$row['Cell_Number']
										, "BookingDate"=>$row['BookingDate']
										, "BookingTime"=>$row['BookingTime']
										, "BookingUTCDateTime"=>$row['BookingUTCDateTime']
										, "DisplayDate"=>$row['DisplayDate']
										, "DisplayTime"=>$row['DisplayTime']												
										, "Pax"=>$row['Pax']
										, "SeatingPreferenceIDs"=>$row['SeatingPreferenceIDs']
										, "SeatingPrefNames"=>$row['SeatingPrefNames']											
										, "TableIDs"=>$row['TableIDs']
										, "TableNos" =>$row['TableNos']
										, "BookingType"=>$row['BookingType']											
										, "RequestNote"=>$row['RequestNote']
										, "CheckedIn"=>$row['CheckedIn']
										, "BookingStatus"=>$row['BookingStatus']
										, "BookingSource"=>$row['BookingSource']											
										, "Category"=>$row['Category']
										, "CheckOutStatus"=>$row['CheckOutStatus']
										);	
											
					}	
					
					if($hasRS === "0")
						$aResults[] = array("Success"=>0);						
					else					
						$aResults[] = array("Success"=>1, "CBDtls" =>$cbdtls);
															
					odbc_close($ConnLink);		
		}
		catch(Exception $e){
	
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
				
			$aResults[] = array("Success"=>-1);
				odbc_close($ConnLink);		
		}
				
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;		
		
}



function GetBookings($input)
{
		//http://192.168.1.52:8090/ConciergeAPI.php?d={"csid": 4,"dt":"02/06/2013","st":"","dit":""}&tp=GBT
		//tp->GBT, concierge setting id, date, searchtext,dinetype
		
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$BookingDate = $inputDecode->{'dt'};
		$SearchText = str_replace("'","''",trim($inputDecode->{'st'}));
		$DineType = $inputDecode->{'dit'};	//optional
			
		/* Parse xml 
		//$Otid = $xml->Otid;
		$EntityId = $xml->EntityId;
		//$AccountId = $xml->AccountId;
		$BookingDate = $xml->BookingDate;
		$SearchText = $xml->SearchText;*/
	
		/* Parse xml */
		
		$aResults ='';
		$ConnLink ='';
		$hasRS = "0";			
		try
		{

			$ConnLink = ConnectToMssql();			
		
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "EXEC spConciergeAppGetBookingDetails ".$ConciergeSettingId.",'".$cresult."','".$SearchText."','".$DineType."'";
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));
		
			//$SQL = "EXEC spConciergeAppGetBookingDetails @pConciergeSettingId = ? ,@pBookingDate = ?, @pSearchText = ?,@pDineCategory =?";
			
			//$params = array($ConciergeSettingId, $cresult,$SearchText,$DineType);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
					throw new Exception(odbc_error($ConnLink));
						
					$bdtls = '';
					
					while($row = odbc_fetch_array($result)){
						//echo $row['CID']. " - ". $row['ConciergeTableBookingRequestId'];
						//echo "<br />";
						$hasRS = "1";
						$bdtls[] = array("CBId"=>$row['CBId']
										, "CSId"=>$row['CSId']
										//, "CustomerId"=>$row['CustomerId']
										//, "AccountId"=>$row['AccountId']
										//, "EntityId"=>$row['EntityId']
										, "CustomerName"=>$row['Name']
										, "Gender"=>$row['Gender']
										, "EmailId"=>$row['EmailId']
										, "CountryCallingCode" => $row['CountryCallingCode']
										, "Cell_Number"=>$row['Cell_Number']
										, "BookingDate"=>$row['BookingDate']
										, "BookingTime"=>$row['BookingTime']
										, "BookingUTCDateTime"=>$row['BookingUTCDateTime']
										, "DisplayDate"=>$row['DisplayDate']
										, "DisplayTime"=>$row['DisplayTime']												
										, "Pax"=>$row['Pax']
										, "SeatingPreferenceIDs"=>$row['SeatingPreferenceIDs']
										, "SeatingPrefNames"=>$row['SeatingPrefNames']											
										, "TableIDs"=>$row['TableIDs']
										, "TableNos" =>$row['TableNos']
										, "BookingType"=>$row['BookingType']											
										, "RequestNote"=>$row['RequestNote']
										, "CheckedIn"=>$row['CheckedIn']
										, "BookingStatus"=>$row['BookingStatus']
										, "BookingSource"=>$row['BookingSource']											
										, "Category"=>$row['Category']
										, "CheckOutStatus"=>$row['CheckOutStatus']
										, "CheckInTime"=>$row['CheckInTime']											
										);	
											
					}		
					
					if($hasRS === "0")
						$aResults[] = array("Success"=>0);
					else	
						$aResults[] = array("Success"=>1, "BDtls" =>$bdtls);

				odbc_close($ConnLink);
		}
		catch(Exception $e){
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
				
			$aResults[] = array("Success"=>-1);
			odbc_close($ConnLink);
			//$GLOBALS['glog']->error($e);
		}
						
		$json_response = json_encode($aResults);
		//# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		//# Return the response
		echo $json_response;
}

function GetCheckins($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
		
		$hasRS = "0";			
		try
		{

			$ConnLink = ConnectToMssql();			
		
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d');	
					
			$SQL = "EXEC spConciergeAppGetCheckinCount ".$ConciergeSettingId.",'".$cresult."'"; 		
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));			
						
			if(!$result)
					throw new Exception(odbc_error($ConnLink));
					
					$bdtls = '';
					
					while($row = odbc_fetch_array($result)){
						
						$hasRS = "1";
						$aResults[] = array("TotChk"=>$row['TotChk'], "TotPax"=>$row['TotPax']);	
											
					}						
					//$aResults[] = array("Success"=>1, "BDtls" =>$bdtls);
					
					if($hasRS === "0")
					{
						$aResults[] = array("TotChk"=>"0","TotPax"=>"0");
					}						
		}
		catch(Exception $e){
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
			
			odbc_close($ConnLink);	
			$aResults[] = array("TotChk"=>"0","TotPax"=>"0");
		}
				
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}

function SetCheckinoutMessages($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		//$CheckInSms = str_replace("'","''",trim($inputDecode->{'chkisms'}));
		//$CheckInEmail = str_replace("'","''",trim($inputDecode->{'chkiemail'}));
		//$CheckOutSms = str_replace("'","''",trim($inputDecode->{'chkosms'}));
		//$CheckOutEmail = str_replace("'","''",trim($inputDecode->{'chkoemail'}));		
		$CheckInSms = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($inputDecode->{'chkisms'})),null,'UTF-8') ));  
		$CheckInEmail = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($inputDecode->{'chkiemail'})),null,'UTF-8') ));  
		$CheckOutSms = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($inputDecode->{'chkosms'})),null,'UTF-8') ));  
		$CheckOutEmail = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($inputDecode->{'chkoemail'})),null,'UTF-8') )); 
		#$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
		
		
		try
		{

			$ConnLink = ConnectToMssql();			
							
			$SQL = "EXEC spConciergeAppSetCheckinoutMessages ".$ConciergeSettingId.",'".$CheckInSms."','".$CheckInEmail."','".$CheckOutSms."','".$CheckOutEmail."'"; 			
			$result = odbc_exec($ConnLink,$SQL);	// or die(odbc_error($ConnLink));
					
			//$SQL = "EXEC spConciergeAppSetCheckinoutMessages @pConciergeSettingId = ? ,@pCheckinSms = ?,@pCheckinEmail = ?,@pCheckoutSms = ?,@pCheckoutEmail = ?";
			
			//$params = array($ConciergeSettingId, $CheckInSms,$CheckInEmail,$CheckOutSms,$CheckOutEmail);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;

			if(!$result)
			{
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . print_r(odbc_error($ConnLink), true). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);			
			}
									
				$UpdateStatus ='';
				while($row = odbc_fetch_array($result)){							
					$UpdateStatus = $row['UStatus'];													
				}
				
				if($UpdateStatus =='1')
				{
						#Call asmx method

						#send same data to sql server
					#	set_time_limit(0);
					#	$konektUrl = "http://192.168.1.168/KonektSolution/Service/KonektConci.asmx/";

					#	$konektUrl .= "UpdateCheckInOutMessage";

						/* Calling  web service using SOAP 	
						$client = new SoapClient($konektUrl);
						$params = array('Data'=>$input) ;
						$result = $client->UpdateCheckInOutMessage($params);
						print_r( $result);*/
														
					#	$data = "Data=".$input;

					#	$ch = curl_init();

						//Set the URL
					#	curl_setopt($ch, CURLOPT_URL, $konektUrl);
					#	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

						//Enable POST data
					#	curl_setopt($ch, CURLOPT_POST, true);
						//Use the $pData array as the POST data
					#	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

						//curl_exec automatically writes the data returned
					#	$retVal=curl_exec($ch);
						
						// close cURL resource, and free up system resources
					#	curl_close($ch); 
																					
					#	if($retVal)
					#	{
							$aResults[] = array("Success"=>1); //messages changed successfully.
					#	}	
					#	else
					#	{
					#		$aResults[] = array("Success"=>0); //passed setting id does not exist on sql server.
					#	}														
				}
				else
				{
					$aResults[] = array("Success"=>0);	//no updation happened on my sql or record does not exist. or no record exist.
				}												

			odbc_close($ConnLink);	
		}
		catch(Exception $e){

				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
				odbc_close($ConnLink);	
			$aResults[] = array("Success"=>-1); // some error occoured
		}

		
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}

function GetCustomerInfo()
{
		$action ='';
		$accountid ='';

		if(ISSET($_REQUEST['q']))
			$action = $_REQUEST['q'];
		
		if(ISSET($_REQUEST['accountid']))
			$accountid = $_REQUEST['accountid'];
		
		$aResults = array();
		$ConnLink ='';
		
		try
		{		
			$ConnLink = ConnectToMssql();
							
			$SQL = "EXEC spConciergeAppGetCustomerInfo '".$accountid."','".$action."'";			
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));		
			
			//$SQL = "EXEC spConciergeAppGetCustomerInfo @pAccountId = ?, @pSearchText = ? ";
			
			//$params = array($accountid,$action);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
					throw new Exception(odbc_error($ConnLink));		
							
			while ($row = odbc_fetch_array($result)) 
			{	
				$name="";
				$cell="";
				$email="";
				
				$name=htmlspecialchars($row['CustomerName']);
				$cell=$row['Cell_Number'];
				$email=$row['EmailId'];
				
				if($name=="")
					$name="N/A";
				
					$name .= " - [".$cell.", ".$email."]";
				
					$aResults[] = array( "id"=>$row['CustomerId'] ,"name"=>$name);						
			}
			
			odbc_close($ConnLink);
														
		}
		catch(Exception $e){
		
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, $action. 'Error message is : ' . $e ->__toString() . " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
			odbc_close($ConnLink);	
			$aResults[] = array("Success"=>-1);
		}
						
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
		
}

function GetAllOffers($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		//$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';

		$hasRS = "0";	
		
		try
		{			
			$ConnLink = ConnectToMssql();			
		
			//$BookingDate = new DateTime($BookingDate);
			//$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "EXEC spConciergeAppGetAllOffersDetails '".$ConciergeSettingId."'";			
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));
			
			//$SQL = "EXEC spConciergeAppGetAllOffersDetails @pConciergeSettingId = ? ";
			
			//$params = array($ConciergeSettingId);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));			
		
					$offdtls = '';

					while($row = odbc_fetch_array($result)){
						$hasRS = "1";
						$offdtls[] = array("COffId"=>$row['ConciergeOfferId']
										, "CSId"=>$row['ConciergeSettingId']											
										, "Title"=>$row['OfferTitle']
										, "IsActive"=>$row['IsActive']
										, "ValidFromDate"=>$row['ValidFrom']
										, "ValidToDate"=>$row['ValidTo']
										, "ValidFromTime"=>$row['ValidFromTime']
										, "ValidToTime"=>$row['ValidToTime']
										, "ValidOnWeekDays"=>$row['ValidOnWeekDays']
										, "ValidOnWeekDayNames"=>$row['ValidOnWeekDayNames']
										, "NoOfOffers"=>$row['NoOfOffers']
										, "Criteria"=>$row['Criteria']												
										, "OfferValidFor"=>$row['VoucherValidForSources']
										, "OfferDescription"=>$row['AboutThisOffer']											
										
										);													
					}	
					if($hasRS === "0")
					{
						$aResults[] = array("Success"=>0);				
					}
					else						
					{
						$aResults[] = array("Success"=>1, "OffDtls" =>$offdtls);
					}
					
					odbc_close($ConnLink);
		}
		catch(Exception $e){
		
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Query is : '. $SQL ."\r\n" . 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
				
			odbc_close($ConnLink);
			$aResults[] = array("Success"=>-1);
		}
			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}


function GetCheckinoutMessages($input)
{
		
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		//$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
		
		$CheckInSMSMessage = "";
		$CheckOutSMSMessage ="";
		$CheckInEmailMessage ="";
		$CheckOutEmailMessage ="";
		
		$hasRS = "0";	
		
		$ConnLink = ConnectToMssql();	
		
		try
		{			
			$chkmsg = '';
			$row = '';			

			//$BookingDate = new DateTime($BookingDate);
			//$cresult = $BookingDate->format('Y-m-d');	

			//$SQL = "EXEC spConciergeAppGetCheckinoutMessages @pConciergeSettingId = ? ";		
			//$params = array($ConciergeSettingId);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;


			/*if(!$result)
			{
				throw new Exception(odbc_error($ConnLink));
			}	*/	
			
			$SQL = "EXEC spConciergeAppGetCheckinoutMessages '".$ConciergeSettingId . "','cios' ";
			$result = odbc_exec($ConnLink,$SQL); 
									
			while($row = odbc_fetch_array($result)){
				$CheckInSMSMessage  = $row['CheckInSMSMessage'];
				$CheckOutSMSMessage = $row['CheckOutSMSMessage'];
			}	
			//echo $CheckInSMSMessage;
			//echo $CheckOutSMSMessage;
			
			odbc_free_result($result);		

			$SQL = "EXEC spConciergeAppGetCheckinoutMessages '".$ConciergeSettingId . "','ciem' ";
			$result = odbc_exec($ConnLink,$SQL); 
									
			while($row = odbc_fetch_array($result)){
				$CheckInEmailMessage  = $row['CheckInEmailMessage'];				
			}	
			//echo $CheckInEmailMessage;
			odbc_free_result($result);

			$SQL = "EXEC spConciergeAppGetCheckinoutMessages '".$ConciergeSettingId . "','coem' ";
			$result = odbc_exec($ConnLink,$SQL); 
									
			while($row = odbc_fetch_array($result)){
				$CheckOutEmailMessage  = $row['CheckOutEmailMessage'];				
			}	
			//echo $CheckOutEmailMessage;
			odbc_free_result($result);
			
				//while($row = odbc_fetch_array($result)){
															
					//$hasRS = "1";
					$chkmsg[] = array("ChkISms"=>$CheckInSMSMessage
									, "ChkIEmail"=>$CheckInEmailMessage
									, "ChkOSms"=>$CheckOutSMSMessage
									, "ChkOEmail"=>$CheckOutEmailMessage
								);					
				//}	

				/*if($hasRS === "0")		
				{
					$aResults[] = array("Success"=>0);
				}
				else
				{*/
					$aResults[] = array("Success"=>1, "ChkMsg" =>$chkmsg);
				//}
			
			odbc_close($ConnLink);			
		}
		catch(Exception $e){

				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Query is : '. $SQL ."\r\n" . 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);
			
			odbc_close($ConnLink);			
			$aResults[] = array("Success"=>-1);
		}

		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
	
}


function SetCheckinTable($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$ConciergeBookingId = $inputDecode->{'cbid'};
		$CheckinDeviceDateTime = $inputDecode->{'chkdt'};
		$ModifiedTime ='';
		
		$CheckinDeviceDateTime = new DateTime($CheckinDeviceDateTime);
		$cresult = $CheckinDeviceDateTime->format('Y-m-d H:i:s');	
					
		$aResults ='';
		$ConnLink ='';
		
		$hasRS = "0";	
		try
		{

			$ConnLink = ConnectToMssql();
				
			$SQL = "EXEC spConciergeAppSetCheckinTable ".$ConciergeSettingId.",'".$ConciergeBookingId."','".$cresult."'";
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));
						
			//$SQL = "EXEC spConciergeAppSetCheckinTable @pConciergeSettingId = ? ,@pConciergeBookingId =?, @pCheckinDate =?";			
			//$params = array($ConciergeSettingId,$ConciergeBookingId,$cresult);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));			

					if(odbc_num_rows($result))
					{
						$UpdateStatus ='';
						#$CheckInDeviceTime='';
						#$CheckInUTCDateTime='';
						$ActiveCheckins=''; $ActivePax='';
												
						//while($row = mysql_fetch_array($result)){
						$row = odbc_fetch_array($result);
						
							$UpdateStatus = $row['ChkStatus'];
							#$CheckInDeviceTime=$row['DeviceTime'];
							#$CheckInUTCDateTime=$row['CheckInUTCDateTime'];	
							#$ModifiedTime=$row['ModifiedOn'];	
							$ActiveCheckins=$row['ActiveCheckins'];		
							$ActivePax=$row['ActivePax'];							
						//}
												
						odbc_free_result($result); 
					
						if($UpdateStatus =='1')
						{						
							$sql = "EXEC spConciergeAppGetManagerDetails ".$ConciergeSettingId;							

							$OuletDetails = odbc_exec($ConnLink,$sql); //or die(odbc_error($ConnLink));  
							$outrow =  odbc_fetch_array($OuletDetails);							

							//$sql = "EXEC spConciergeAppGetManagerDetails @pConciergeSettingId = ? ";
							
							//$params = array($ConciergeSettingId);		
							//$OuletDetails = sqlsrv_query($ConnLink,$sql,$params) ;
							//$outrow = sqlsrv_fetch_array($OuletDetails);
							
							//if(!$OuletDetails)
							//		throw new Exception(print_r( sqlsrv_errors(), true));
							
							
							odbc_free_result($OuletDetails);
							//sqlsrv_next_result($ConnLink);
							
							/* Code Added by Rahul 23-04-2013 */							
							//$ReturnResult = GenerateResponse($row,$outrow,'Checkin');	

							/*$file = 'data.txt';				
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------");
						fwrite($handle,"\r\n");
						fwrite($handle, $cresult ."\r\n". 'mailing rows are : ' . print_r($row)."\r\n" .print_r($outrow). " "   ."\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
						fclose($handle);*/
				
							//SEND MESSAGE ONE BY ONE		
							$ObjConciergeMessage = new ConciergeMessageSender();								
							$ObjConciergeMessage->PostMessage($row,$outrow,'Checkin');							


							/*# Based on type send message.
							
							$Response = "<?xml version=\"1.0\"?><Request type=\"simple\">"; 
								
							$Response .= $ReturnResult;
							$Response .= "</Request>";						
							$Response = "data=" . $Response;

							
							set_time_limit(0);							
							
							$url = $GLOBALS['gSendMessageURL'] . "SendMessage";
							
							$ch = curl_init();					
							//Set the URL
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							//Enable POST data
							curl_setopt($ch, CURLOPT_POST, true);
							//Use the $pData array as the POST data
							curl_setopt($ch, CURLOPT_POSTFIELDS, $Response);

							//curl_exec automatically writes the data returned
							$retVal=curl_exec($ch);
							
							// close cURL resource, and free up system resources
							curl_close($ch); 

							//*********************************************************
							*/
							
							/* Code Added by Rahul 23-04-2013 Complete */							
							
								#Call asmx method

								#send same data to sql server
							#	set_time_limit(0);
							#	$konektUrl = "http://192.168.1.168/KonektSolution/Service/KonektConci.asmx/";

							#	$konektUrl .= "UpdateCheckInStatus";

								/* Calling  web service using SOAP 	
								$client = new SoapClient($konektUrl);
								$params = array('Data'=>$input) ;
								$result = $client->UpdateCheckInOutMessage($params);
								print_r( $result);*/

							#	$data = "Data={'chksts':'".$UpdateStatus."','csid':".$ConciergeSettingId.",'cbid':".$ConciergeBookingId.",'chkitime':'".$CheckInDeviceTime."','chkinutc':'".$CheckInUTCDateTime."','chkmodtime':'".$ModifiedTime."'}";

							#	$ch = curl_init();

								//Set the URL
							#	curl_setopt($ch, CURLOPT_URL, $konektUrl);
							#	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

								//Enable POST data
							#	curl_setopt($ch, CURLOPT_POST, true);
								//Use the $pData array as the POST data
							#	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

								//curl_exec automatically writes the data returned
							#	$retVal=curl_exec($ch);
								
								// close cURL resource, and free up system resources
							#	curl_close($ch); 
																							
							#	if($retVal)
							#	{
									$aResults[] = array("Success"=>1, "ActChkins" =>$ActiveCheckins,"Actpx" =>$ActivePax); // Checkin done on both servers
									//echo 'Checkin done on both servers<br/>'; 
							#	}	
							#	else
							#	{
							#		$aResults[] = array("Success"=>0); //Checkin failed on sql server.									
							#	}														
						}
						else if($UpdateStatus !='-1' &&  $UpdateStatus !='-2' && $UpdateStatus=='')
						{
							$aResults[] = array("Success"=>0);	//Checkin failed on mysql. or no records exists
						}						
						else
						{
							$aResults[] = array("Success"=>$UpdateStatus);	//-1 and -2 and -3 
						}
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}										
					
					odbc_close($ConnLink);
		}
		catch(Exception $e){
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, $cresult ."\r\n". 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);		
				
			odbc_close($ConnLink);	
			$aResults[] = array("Success"=>-1); // some error occoured
		}
			
			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}



function SetBookTable($input)
{
		/* Parse xml */
		//$Otid = $xml->Otid;
		//$EntityId = $xml->EntityId;
		//$AccountId = $xml->AccountId;
		//$CustomerId = $xml->CustomerId;
		//$CustomerName = str_replace("'","''",trim($xml->CustomerName));
		
		/* Parse xml */
		
		$inputDecode =json_decode($input);
		
		//echo $input;
		
		$BookingMode = $inputDecode->{'bmod'};		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$ConciergeBookingId = $inputDecode->{'cbid'}; //may be empty in new case
		
		$CustomerId = $inputDecode->{'custid'};	//may be empty in new case
		$CustomerName = str_replace("'","''",trim($inputDecode->{'custnm'}));
		$CallingCode = str_replace("'","''",trim($inputDecode->{'callco'}));
		$CustomerNumber = str_replace("'","''",trim($inputDecode->{'custno'}));
		$CustomerEmail = str_replace("'","''",trim($inputDecode->{'custem'}));
		$NoOfPeople = $inputDecode->{'px'};
		$BookingDate = $inputDecode->{'bdt'};
		$BookingTime = $inputDecode->{'bt'};
		$BookingType = $inputDecode->{'dinetp'};
		$RequestNote = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($inputDecode->{'reqnt'})),null,'UTF-8') ));
		$Source = $inputDecode->{'source'};
		$Gender = $inputDecode->{'gen'};
		$SeatingPreferenceIDs = str_replace("'","''",trim($inputDecode->{'spref'}));	//may be empty in new case
		$TableIDs = str_replace("'","''",trim($inputDecode->{'tabid'}));
		$TableNos = str_replace("'","''",trim($inputDecode->{'tabno'}));
		
		$DeviceDateTime = $inputDecode->{'sysdt'};
		$OfferId =  $inputDecode->{'offid'};
	
			
		$aResults ='';
		$ConnLink ='';
		
		try
		{
			$ConnLink = ConnectToMssql();
	
			//booking date and time
			$cdate = new DateTime($BookingDate);
			$cresult = $cdate->format('Y-m-d');			
			$BookingDateTime = $cresult . ' ' . $BookingTime ; 
						
			//$SysDatetime = String.Format("{0:g}", DateTime.Now);
			//$SysDatetime = date("m/d/y H:i:s");
						
			$DeviceDateTime = new DateTime($DeviceDateTime);
			$SysDatetime = $DeviceDateTime->format('Y-m-d H:i:s');		
		
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time
			
$sql = "EXEC dbo.spConciergeAppSetBookTable '".$BookingMode."','".$SysDatetime."','".$BookingDateTime."','".$ConciergeBookingId."'
,'".$ConciergeSettingId."','".$CustomerId."','".$NoOfPeople."','".$CallingCode."','".$CustomerNumber."','".$CustomerEmail."','".$CustomerName."'
,'".$RequestNote."','".$cresult."','".$BookingTime."','".$BookingType."','".$Source."','".$Gender."','".$SeatingPreferenceIDs."'
,'".$TableIDs."','".$TableNos."','".$OfferId."'";
										
			$result = odbc_exec($ConnLink,$sql); //or die(odbc_error($ConnLink));
				
		//$sql = "EXEC dbo.spConciergeAppSetBookTable @pMode = ?,@pSysDatetime = ?, @pBookingDateTime = ?, @pConciergeBookingId = ?
		//, @pConciergeSettingId = ?, @pCustomerId = ?, @pNoOfPeople = ?,@pCountryCallingCode = ?
		//, @pCustomerNumber = ?, @pCustomerEmail = ?, @pCustomerName = ?
		//, @pRequestNote = ?, @pBookingDate = ?, @pBookingTime = ?, @pBookingType = ?,@pSource = ?,@pGender = ?
		//, @pSeatingPreferenceIDs = ?,@pTableIDs = ?,@pTableNos = ?, @pOfferId = ? ";

			//$params = array($BookingMode,$SysDatetime,$BookingDateTime,$ConciergeBookingId
			//	,$ConciergeSettingId,$CustomerId,$NoOfPeople,$CallingCode,$CustomerNumber,$CustomerEmail,$CustomerName
			//	,$RequestNote,$cresult,$BookingTime,$BookingType,$Source,$Gender,$SeatingPreferenceIDs,$TableIDs,$TableNos,$OfferId);		

			//$result = sqlsrv_query($ConnLink,$sql,$params) ;
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));	
					

					if(odbc_num_rows($result))
					{
						$ReturnResult = '';
						$Response = '';
						$row = '';
						$ActiveCheckins = ''; 
						$ActivePax= '';		
						$AlreadyBooked ='';								
						
						$row = odbc_fetch_array($result);
						
						if(ISSET($row['BkStatus']))
							$AlreadyBooked = $row['BkStatus'];
																				
						if($AlreadyBooked == '-1')
						{
							//means someone already booked that table,so please select differenct table and then book again.
							$aResults[] = array("Success"=>-1);	
						}
						else if($AlreadyBooked == '-3')
						{
							//consecutive entry
							$aResults[] = array("Success"=>-3);	
						}
						else if($AlreadyBooked == '-4')
						{
							//same section entry
							$aResults[] = array("Success"=>-4);	
						}						
						else
						{ 
						
							$ActiveCheckins=$row['ActiveCheckins'];		
							$ActivePax=$row['ActivePax'];	
							
							odbc_free_result($result); 
							//sqlsrv_next_result($ConnLink); 							
				
							$sql = "EXEC spConciergeAppGetManagerDetails ".$ConciergeSettingId;
						
							$OuletDetails = odbc_exec($ConnLink,$sql); //or die(odbc_error($ConnLink));  
							$outrow =  odbc_fetch_array($OuletDetails);							
							
							//$sql = "EXEC spConciergeAppGetManagerDetails @pConciergeSettingId = ? ";
							
							//$params = array($ConciergeSettingId);		
							
							//$OuletDetails = sqlsrv_query($ConnLink,$sql,$params) ;
							//$outrow = sqlsrv_fetch_array($OuletDetails);
							
							if(!$OuletDetails)
							{	$file = 'data.txt';				
								$handle = fopen($file, 'a');
								$logTime = new DateTime();
								$logTime= $logTime->format('Y-m-d H:i:s');
								fwrite($handle, "--------------------------------------------------------------------------------------------------");
								fwrite($handle,"\r\n");
								fwrite($handle, 'Error message is : ' . print_r( odbc_error($ConnLink), true). " "   ."\r\n" . $logTime);
								fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
								fclose($handle);
							}
							//		throw new Exception(print_r( sqlsrv_errors(), true));
							
						
							
				
							odbc_free_result($OuletDetails);
							//sqlsrv_next_result($ConnLink);						

							/* Code Added by Rahul 23-04-2013 */	
							
							//SEND MESSAGE ONE BY ONE		
							$ObjConciergeMessage = new ConciergeMessageSender();								

							
							if($row['pmode'] == "n")
							{
								//$ReturnResult = GenerateResponse($row,$outrow,'Book');	
								$ObjConciergeMessage->PostMessage($row,$outrow,'Book');
							}
							else if($row['pmode'] == "e" || $row['pmode'] == "ep" )	
							{
								//$ReturnResult = GenerateResponse($row,$outrow,'Amend');	
								$ObjConciergeMessage->PostMessage($row,$outrow,'Amend');
							}

							/* This code is commented as it is sending checkin mail twice 07-06-2013
							if($row['pmode'] == "n" || $row['pmode'] == "e" )	
							{
								if(strtolower($row['CheckedIn']) == strtolower("yes"))
								{
									//$ReturnResult = GenerateResponse($row,$outrow,'Checkin');
									$ObjConciergeMessage->PostMessage($row,$outrow,'Checkin');
								}									
							}
							This code is commented as it is sending checkin mail twice 07-06-2013 complete
							*/			

						/*	# Based on type send message.
							
							$Response = "<?xml version=\"1.0\"?><Request type=\"simple\">"; 
								
							$Response .= $ReturnResult;
							$Response .= "</Request>";						
							$Response = "data=" . $Response;
																							
								$file = 'concibkdata.txt';
								$handle = fopen($file, 'a');
								$logTime = new DateTime();
								$logTime= $logTime->format('Y-m-d H:i:s');
								fwrite($handle, "--------------------------------------------------------------------------------------------------");
								fwrite($handle,"\r\n");								
								fwrite($handle, $Response. "\r\n" . $logTime);
								fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
								fclose($handle);

							set_time_limit(0);							
							$url = $GLOBALS['gSendMessageURL'] . "SendMessage";
							
							
							$ch = curl_init();					
							//Set the URL
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							//Enable POST data
							curl_setopt($ch, CURLOPT_POST, true);
							//Use the $pData array as the POST data
							curl_setopt($ch, CURLOPT_POSTFIELDS, $Response);

							//curl_exec automatically writes the data returned
							$retVal=curl_exec($ch);
							
							// close cURL resource, and free up system resources
							curl_close($ch);      												
							
							//async_call($url, $Response);
							//http://codeissue.com/issues/i64e175d21ea182/how-to-make-asynchronous-http-calls-using-php						
							*/							
							
							/* Code Added by Rahul 23-04-2013 Complete */
							
							$aResults[] = array("Success"=>1, "ActChkins" =>$ActiveCheckins,"Actpx" =>$ActivePax); // Table release done on both servers
								
						}										
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}
				
			odbc_close($ConnLink);
				
		}
		catch(Exception $e){
		
				$file = 'data.txt';				
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);	
			
			odbc_close($ConnLink);			
			$aResults[] = array("Success"=>-1);			
		}

	
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}







function GetTablesToRelease($input)
{
	//get requestids with multiple tables	GetReleaseTable
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};		
		$BookingDate = $inputDecode->{'bdt'};
		
		$aResults ='';
		$ConnLink ='';
		$SQL ='';		

		$hasRS = "0";
		
		try
		{
			if($ConciergeSettingId !='')
			{
					$ConnLink = ConnectToMssql();
				
					$BookingDate = new DateTime($BookingDate);
					$cresult = $BookingDate->format('Y-m-d');	


				$SQL = "EXEC spConciergeAppGetReleaseTable ".$ConciergeSettingId . ",'". $cresult . "'";
			
				$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));  
						
				//$SQL = "EXEC spConciergeAppGetReleaseTable @pConciergeSettingId = ?,  @pBookingDate = ?";
				
				//$params = array($ConciergeSettingId,$cresult);		
				//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
				
				if(!$result)
					throw new Exception(odbc_error($ConnLink));
										
					$reltbls = '';
					
					//while($row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC)){
					while($row = odbc_fetch_array($result)){
						$hasRS = "1";
						$reltbls[] = array(
										"cbid"=>$row['ConciergeBookingId']
										, "tid"=>$row['TableIDs'] 
										, "tnm"=>$row['TableNames'] 
										, "tcap"=>$row['TableCapacity'] 													
										, "cnm"=>$row['CustomerName'] 	
										,"chin"=>$row['CheckInTime']
										,"mtat"=>$row['MaxTurnAround']
										,"amtat"=>$row['ApproxTurnAroundTime']
									);													
					}	

					if($hasRS === "0")		
					{
						$aResults[] = array("Success"=>0);
					}
					else
					{
						$aResults[] = array("Success"=>1, "RelTbls" =>$reltbls);
					}
				odbc_close($ConnLink);		
			}
			else
			{
				$aResults[] = array("Success"=>0);
			}
		}
		catch(Exception $e){
			$file = 'data.txt';				
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Passed Date: ' . $cresult."\r\n" . ' Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);
			
			odbc_close($ConnLink);	
			$aResults[] = array("Success"=>-1);
		}
	
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;					
}

function SetReleaseTable($input)
{
	//release or checkout tables
	//pass requestid + multiple tables	
		
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$ConciergeBookingId = $inputDecode->{'cbid'};
		$CheckoutDeviceDateTime = $inputDecode->{'chkodt'};
		$ModifiedTime ='';
	
		$CheckoutDeviceDateTime = new DateTime($CheckoutDeviceDateTime);
		$cresult = $CheckoutDeviceDateTime->format('Y-m-d H:i:s');	
					
		$aResults ='';
		$ConnLink ='';
			
		try
		{
			
			$ConnLink = ConnectToMssql();
			
			$SQL = "EXEC spConciergeAppSetReleaseTable ".$ConciergeSettingId.",'".$ConciergeBookingId."','".$cresult."'"; 
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));

			//$SQL = "EXEC spConciergeAppSetReleaseTable @pConciergeSettingId = ?,  @pConciergeBookingId = ?, @pCheckoutDate = ?";
			
			//$params = array($ConciergeSettingId,$ConciergeBookingId,$cresult);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
				throw new Exception(print_r( sqlsrv_errors(), true));			

					if(odbc_num_rows($result))					
					{
						$UpdateStatus ='';
						#$CheckOutDeviceTime='';
						#$CheckOutUTCDateTime='';
						#while($row = mysql_fetch_array($result)){
						
						$row = odbc_fetch_array($result);
						
							$UpdateStatus = $row['ChkOtStatus'];
							#$CheckOutDeviceTime=$row['DeviceTime'];
							#$CheckOutUTCDateTime=$row['CheckOutUTCDateTime'];
							#$ModifiedTime=$row['ModifiedOn'];									
						#}

						odbc_free_result($result);
					
						if($UpdateStatus =='1')
						{							
							$sql = "EXEC spConciergeAppGetManagerDetails ".$ConciergeSettingId;
												
							$OuletDetails = odbc_exec($ConnLink,$sql); //or die(odbc_error($ConnLink));  
							$outrow =  odbc_fetch_array($OuletDetails);							
									
							//$sql = "EXEC spConciergeAppGetManagerDetails @pConciergeSettingId = ? ";
								
							//$params = array($ConciergeSettingId);		
							//$OuletDetails = sqlsrv_query($ConnLink,$sql,$params) ;
							//$outrow = sqlsrv_fetch_array($OuletDetails);
						
							odbc_free_result($OuletDetails);
							//odbc_next_result($ConnLink);							

							/* Code Added by Rahul 23-04-2013 */							
							//$ReturnResult = GenerateResponse($row,$outrow,'Checkout');	

							//SEND MESSAGE ONE BY ONE		
							$ObjConciergeMessage = new ConciergeMessageSender();								
							$ObjConciergeMessage->PostMessage($row,$outrow,'Checkout');
								
						/*	# Based on type send message.
							
							$Response = "<?xml version=\"1.0\"?><Request type=\"simple\">"; 
								
							$Response .= $ReturnResult;
							$Response .= "</Request>";						
							$Response = "data=" . $Response;

							
							set_time_limit(0);							
							
							$url = $GLOBALS['gSendMessageURL'] . "SendMessage";
							
							$ch = curl_init();					
							//Set the URL
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							//Enable POST data
							curl_setopt($ch, CURLOPT_POST, true);
							//Use the $pData array as the POST data
							curl_setopt($ch, CURLOPT_POSTFIELDS, $Response);

							//curl_exec automatically writes the data returned
							$retVal=curl_exec($ch);
							
							// close cURL resource, and free up system resources
							curl_close($ch); 

							//*********************************************************
							
							*/
							/* Code Added by Rahul 23-04-2013 Complete */
							
								#Call asmx method

								#send same data to sql server
							#	set_time_limit(0);
							#	$konektUrl = "http://192.168.1.168/KonektSolution/Service/KonektConci.asmx/";

							#	$konektUrl .= "UpdateCheckOutStatus";

								/* Calling  web service using SOAP 	
								$client = new SoapClient($konektUrl);
								$params = array('Data'=>$input) ;
								$result = $client->UpdateCheckInOutMessage($params);
								print_r( $result);*/

							#	$data = "Data={'chksts':'".$UpdateStatus."','csid':".$ConciergeSettingId.",'cbid':".$ConciergeBookingId.",'chkotime':'".$CheckOutDeviceTime."','chkoutc':'".$CheckOutUTCDateTime."','chkmodtime':'".$ModifiedTime."'}";

							#	$ch = curl_init();

								//Set the URL
							#	curl_setopt($ch, CURLOPT_URL, $konektUrl);
							#	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

								//Enable POST data
							#	curl_setopt($ch, CURLOPT_POST, true);
								//Use the $pData array as the POST data
							#	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

								//curl_exec automatically writes the data returned
							#	$retVal=curl_exec($ch);
								
								// close cURL resource, and free up system resources
							#	curl_close($ch); 
																							
							#	if($retVal)
							#	{
									$aResults[] = array("Success"=>1); // Table release done on both servers
									//echo 'release done on both servers<br/>'; 
							#	}	
							#	else
							#	{
							#		$aResults[] = array("Success"=>0); //release failed on sql server.									
							#	}														
						}
						else if($UpdateStatus =='-3')
						{
							$aResults[] = array("Success"=>$UpdateStatus);	// -3 multiple time click
						}						
						else
						{
							$aResults[] = array("Success"=>0);	//release failed on mysql.
						}												
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}	
					odbc_close($ConnLink);						
						
		}
		catch(Exception $e){

			$file = 'data.txt';				
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);

			odbc_close($ConnLink);				
			$aResults[] = array("Success"=>-1); // some error occoured
		}
		
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;		
	
	
}


function SetCancelTablebooking($input)
{		
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$ConciergeBookingId = $inputDecode->{'cbid'};		
		$ModifiedTime ='';
	
		$aResults ='';
		$ConnLink ='';
			
		try
		{
			$ConnLink = ConnectToMssql();			

			$SQL = "EXEC spConciergeAppSetCancelTable ".$ConciergeSettingId.",'".$ConciergeBookingId."'";
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));
			
			//$SQL = "EXEC spConciergeAppSetCancelTable @pConciergeSettingId = ?,  @pConciergeBookingId = ?";
			
			//$params = array($ConciergeSettingId,$ConciergeBookingId);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));					

					if(odbc_num_rows($result))
					{
						$UpdateStatus ='';
						#$CheckOutDeviceTime='';
						#$CheckOutUTCDateTime='';
						
						//while($row = mysql_fetch_array($result)){
							

							$row = odbc_fetch_array($result);							
							
						
							$UpdateStatus = $row['ChkCanStatus'];
							#$CheckOutDeviceTime=$row['DeviceTime'];
							#$CheckOutUTCDateTime=$row['CheckOutUTCDateTime'];	
							#$ModifiedTime=$row['ModifiedOn'];							
						//}
						
						
						odbc_free_result($result);
						//odbc_next_result($ConnLink); 
						
						if($UpdateStatus =='1')
						{

							$sql = "EXEC spConciergeAppGetManagerDetails ".$ConciergeSettingId;
												
							$OuletDetails = odbc_exec($ConnLink,$sql); //or die(odbc_error($ConnLink));  
							$outrow =  odbc_fetch_array($OuletDetails);	

							//$sql = "EXEC spConciergeAppGetManagerDetails @pConciergeSettingId = ? ";
								
							//$params = array($ConciergeSettingId);		
							//$OuletDetails = sqlsrv_query($ConnLink,$sql,$params) ;
							//$outrow = sqlsrv_fetch_array($OuletDetails);
							
													
							odbc_free_result($OuletDetails);
							//odbc_next_result($ConnLink);						

							//$ReturnResult = GenerateResponse($row,$outrow,'Cancel');	

								/* Code Added by Rahul 23-04-2013 */						
							//SEND MESSAGE ONE BY ONE		
							$ObjConciergeMessage = new ConciergeMessageSender();								
							$ObjConciergeMessage->PostMessage($row,$outrow,'Cancel');


						/*# Based on type send message.
						
						$Response = "<?xml version=\"1.0\"?><Request type=\"simple\">"; 
							
						$Response .= $ReturnResult;
						$Response .= "</Request>";						
						$Response = "data=" . $Response;

						
						set_time_limit(0);							
						
						$url = $GLOBALS['gSendMessageURL'] . "SendMessage";
						
						$ch = curl_init();					
						//Set the URL
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						//Enable POST data
						curl_setopt($ch, CURLOPT_POST, true);
						//Use the $pData array as the POST data
						curl_setopt($ch, CURLOPT_POSTFIELDS, $Response);

						//curl_exec automatically writes the data returned
						$retVal=curl_exec($ch);
						
						// close cURL resource, and free up system resources
						curl_close($ch);  */

						//********************************************************* 
							/* Code Added by Rahul 23-04-2013 Complete */						
							
							$aResults[] = array("Success"=>1); // Table release done on both servers
							//echo 'release done on both servers<br/>'; 
						
						}
						else if($UpdateStatus =='-3')
						{
							$aResults[] = array("Success"=>$UpdateStatus);	// -3 multiple time click
						}	
						else 
						{
							$aResults[] = array("Success"=>0);	//cancel failed on mysql.
						}	

	
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}										
			odbc_close($ConnLink);							
		}
		catch(Exception $e){
		
			$file = 'data.txt';				
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);

			odbc_close($ConnLink);					
			$aResults[] = array("Success"=>-1); // some error occoured
		}
		
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
	
}


function GetSeatingPreferences($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		//$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
						
		$hasRS = "0";
		
		try
		{
			$ConnLink = ConnectToMssql();	
			if($ConciergeSettingId !='')
			{													
					//$BookingDate = new DateTime($BookingDate);
					//$cresult = $BookingDate->format('Y-m-d');	
					
				$SQL = "EXEC spConciergeAppGetSeatingPreferences ".$ConciergeSettingId ; 
				$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));
					
				//$SQL = "EXEC spConciergeAppGetSeatingPreferences @pConciergeSettingId = ? ";
				
				//$params = array($ConciergeSettingId);		
				//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
				
				if(!$result)
					throw new Exception(odbc_error($ConnLink));
					
					$prefdtls = '';

					while($row = odbc_fetch_array($result)){
						$hasRS = "1";		
						$prefdtls[] = array("StPrefId"=>$row['ConciergeSeatingPreferenceId']
										, "Pref"=>$row['Preference'] 											
									);													
					}	
					
					if($hasRS === "0")
					{
						$aResults[] = array("Success"=>0);
					}
					else
					{
						$aResults[] = array("Success"=>1, "PrefDtls" =>$prefdtls);
					}

			}
			else
			{
				$aResults[] = array("Success"=>0);
			}
			odbc_close($ConnLink);
		}
		catch(Exception $e){
				
			$file = 'data.txt';				
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Query is : '. $SQL ."\r\n" . 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);		

			odbc_close($ConnLink);	
			$aResults[] = array("Success"=>-1);
		}
		
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;	
}


function GetPreferencebasedTables($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$Preferences = $inputDecode->{'prefs'};
		$BookingDate = $inputDecode->{'bdt'};
		$BookingTime = $inputDecode->{'bt'};
		
		$aResults ='';
		$ConnLink ='';$cresult='';
		
		$hasRS = "0";
		
		#$CheckinDeviceDateTime = new DateTime($CheckinDeviceDateTime);		
		#$cresult = $CheckinDeviceDateTime->format('Y-m-d H:i:s');	
		
		try
		{
			$ConnLink = ConnectToMssql();
			
			if($ConciergeSettingId !='')
			{				
					$BookingDate = new DateTime($BookingDate);
					$cresult = $BookingDate->format('Y-m-d');	

	$SQL = "EXEC spConciergeAppGetPreferencewiseTables '".$ConciergeSettingId . "','" . $cresult . "','". $BookingTime . "','" . $Preferences. "'"; 	
	$result = odbc_exec($ConnLink,$SQL);
							
				if(!$result)
					throw new Exception(odbc_error($ConnLink));

					 											
				$preftbldtls = '';

				while($row = odbc_fetch_array($result)){
					$hasRS = "1";				
					$preftbldtls[] = array(
									"tblno"=>$row['TableNo']
									,"ias"=>$row['IsAvailableStatus'] 
									,"tnm"=>$row['TableName']
									,"scap"=>$row['SeatingCapacity'] 
									,"mcap"=>$row['MaxCapacity'] 											
								);													
				}
					
				if($hasRS === "0")
				{
					$aResults[] = array("Success"=>0);
				}
				else
				{
					$aResults[] = array("Success"=>1, "PrefTblDtls" =>$preftbldtls);
				}
			}
			else
			{
				$aResults[] = array("Success"=>0);
			}
			odbc_close($ConnLink);
		}
		catch(Exception $e){
		
			$file = 'data.txt';				
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);		
		
			odbc_close($ConnLink);	
			$aResults[] = array("Success"=>-1);
		}

		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;		
}

function GetBookingNotification($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};		
		$SysDateTime = $inputDecode->{'sysdt'};
				
		$SysDateTime = new DateTime($SysDateTime);
		$cresult = $SysDateTime->format('Y-m-d H:i:s');	
					
		$aResults ='';
		$ConnLink ='';
		$SQL = '';
		$lastTime= '';
		
		try
		{
			$ConnLink = ConnectToMssql();
						
			$SQL = "EXEC spConciergeAppGetBookingNotification ".$ConciergeSettingId.",'".$cresult."'";			
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink)); 			

			//$SQL = "EXEC spConciergeAppGetBookingNotification @pConciergeSettingId = ?,@pSysDate = ? ";
			
			//$params = array($ConciergeSettingId,$cresult);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));
									
			$lastTime=$cresult;	//$SysDateTime;
			
			$bdtls = '';

			if(odbc_num_rows($result))
			{	
				$i=0;						
				while($row = odbc_fetch_array($result)){
					//echo $row['CID']. " - ". $row['ConciergeTableBookingRequestId'];
					//echo "<br />";
					
					if($i==0)
						$lastTime=$row['CreatedOn'];
					$i++;
					
					$bdtls[] = array("CBId"=>$row['CBId']
									, "CSId"=>$row['CSId']
									#, "CustomerId"=>$row['CustomerId']
									#, "AccountId"=>$row['AccountId']
									#, "EntityId"=>$row['EntityId']
									, "CustomerName"=>$row['Name']
									, "Gender"=>$row['Gender']
									, "EmailId"=>$row['EmailId']
									, "CountryCallingCode" => $row['CountryCallingCode']
									, "Cell_Number"=>$row['Cell_Number']
									, "BookingDate"=>$row['BookingDate']
									, "BookingTime"=>$row['BookingTime']
									, "BookingUTCDateTime"=>$row['BookingUTCDateTime']
									, "DisplayDate"=>$row['DisplayDate']
									, "DisplayTime"=>$row['DisplayTime']												
									, "Pax"=>$row['Pax']
									, "SeatingPreferenceIDs"=>$row['SeatingPreferenceIDs']
									, "SeatingPrefNames"=>$row['SeatingPrefNames']											
									, "TableIDs"=>$row['TableIDs']
									, "TableNos" =>$row['TableNos']
									, "BookingType"=>$row['BookingType']											
									, "RequestNote"=>$row['RequestNote']
									, "CheckedIn"=>$row['CheckedIn']
									, "BookingStatus"=>$row['BookingStatus']
									, "BookingSource"=>$row['BookingSource']											
									, "Category"=>$row['Category']
									, "CreatedOn"=>$row['CreatedOn']	
									);											
				}	
		
				$aResults[] = array("Success"=>1,"LastTimeExecutedOn"=>$lastTime,"NCnt"=>(string)$i, "BDtls" =>$bdtls);
			}
			else
			{
				$aResults[] = array("Success"=>0,"LastTimeExecutedOn"=>$lastTime);
			}										
				
			odbc_close($ConnLink);			
		}
		catch(Exception $e){
		
			$file = 'data.txt';	
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);
			
			odbc_close($ConnLink);
			$aResults[] = array("Success"=>-1,"LastTimeExecutedOn"=>$lastTime);
		}
		
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response  //,"LastTimeExecutedOn"=>$lastTime
		echo $json_response;
}

function GetCoverStats($input)
{
	//CALL GetCoverStats('651908e1-72d0-4260-81a2-1e498dd3cc96');
	
		$inputDecode =json_decode($input);
		
		$EntityId = $inputDecode->{'entid'};
		//$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
		$hasRS = "0";				
		try
		{
			$ConnLink = ConnectToMssql();
			if($EntityId !='')
			{										
					//$BookingDate = new DateTime($BookingDate);
					//$cresult = $BookingDate->format('Y-m-d');	

			$SQL = "EXEC spConciergeAppGetCoverStats '". $EntityId ."'";			
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink)); 	
			
			//$SQL = "EXEC spConciergeAppGetCoverStats @pEntityId = ? ";							
			//$params = array($EntityId);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));
					
			$coverstats = '';

			while($row = odbc_fetch_array($result)){
				$hasRS = "1";
			
				$coverstats[] = array("BrkAdvPAX"=>$row['BrkAdvPAX']
								, "BrkAdvBooking"=>$row['BrkAdvBooking']
								, "LunchAdvPAX"=>$row['LunchAdvPAX']
								, "LunchAdvBooking"=>$row['LunchAdvBooking']
								, "DnnrAdvPAX"=>$row['DnnrAdvPAX']
								, "DnnrAdvBooking"=>$row['DnnrAdvBooking']
								, "BrkPAXToDine"=>$row['BrkPAXToDine']
								, "BrkBookings"=>$row['BrkBookings']
								, "LunchPAXToDine"=>$row['LunchPAXToDine']
								, "LunchBookings"=>$row['LunchBookings']
								, "DinnerPAXToDine"=>$row['DinnerPAXToDine']
								, "DinnerBookings"=>$row['DinnerBookings']
								, "BrkPAXDined"=>$row['BrkPAXDined']
								, "BrkCheckedIn"=>$row['BrkCheckedIn']
								, "LunchPAXDined"=>$row['LunchPAXDined']
								, "LunchCheckedIn"=>$row['LunchCheckedIn']
								, "DinnerPAXDined"=>$row['DinnerPAXDined']
								, "DinnerCheckedIn"=>$row['DinnerCheckedIn']
							);													
			}	
			
			if($hasRS === "0")
			{
				$aResults[] = array("Success"=>0);
			}	
			else
			{
				$aResults[] = array("Success"=>1, "CoverStats" =>$coverstats);
			}					
			}
			else
			{
				$aResults[] = array("Success"=>0);
			}
			odbc_close($ConnLink);
		}
		catch(Exception $e){
		
			$file = 'data.txt';	
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);
			
			odbc_close($ConnLink);
			$aResults[] = array("Success"=>-1);
		}
		
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;	
}


function GetBookingBasedOffers($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};		
		$BookingDate = $inputDecode->{'bdt'};
		$Paxs = $inputDecode->{'px'};
		$Source = $inputDecode->{'src'};	
		
		$aResults ='';
		$ConnLink ='';$cresult='';
				
		#$CheckinDeviceDateTime = new DateTime($CheckinDeviceDateTime);		
		#$cresult = $CheckinDeviceDateTime->format('Y-m-d H:i:s');	
		$hasRS = "0";	
		
		$ConnLink = ConnectToMssql();
		
		try
		{								
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d H:i:s');	

			$SQL = "EXEC spConciergeAppGetBookingbasedOffers ".$ConciergeSettingId.",'".$cresult."',".$Paxs.",'".$Source."'"; 	
			$result = odbc_exec($ConnLink,$SQL); //or die(odbc_error($ConnLink));			

			//$SQL = "EXEC spConciergeAppGetBookingbasedOffers @pConciergeSettingId = ?, @pBookingDateTime =?,@pPax =?,@pSource=? ";
							
			//$params = array($ConciergeSettingId,$cresult,$Paxs,$Source);		
			//$result = sqlsrv_query($ConnLink,$SQL,$params) ;			
			
			if(!$result)
				throw new Exception(odbc_error($ConnLink));
					
				$offdtls = '';

				while($row = odbc_fetch_array($result)){
					$hasRS = "1";
					$offdtls[] = array("COffId"=>$row['ConciergeOfferId']
									, "CSId"=>$row['ConciergeSettingId']											
									, "Title"=>$row['OfferTitle']
									, "IsActive"=>$row['IsActive']
									, "ValidFromDate"=>$row['ValidFrom']
									, "ValidToDate"=>$row['ValidTo']
									, "ValidFromTime"=>$row['ValidFromTime']
									, "ValidToTime"=>$row['ValidToTime']
									, "ValidOnWeekDays"=>$row['ValidOnWeekDays']
									, "ValidOnWeekDayNames"=>$row['ValidOnWeekDayNames']
									, "NoOfOffers"=>$row['NoOfOffers']
									, "Criteria"=>$row['Criteria']												
									, "OfferValidFor"=>$row['VoucherValidForSources']
									, "OfferDescription"=>$row['AboutThisOffer']											
									
									);													
				}	
				
				if($hasRS === "0")
				{
					$aResults[] = array("Success"=>0);	
				}	
				else
				{
					$aResults[] = array("Success"=>1, "OffDtls" =>$offdtls);
				}	
				
				odbc_close($ConnLink);
				
		}
		catch(Exception $e){
			
			$file = 'data.txt';	
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
			fclose($handle);
			
			odbc_close($ConnLink);
			$aResults[] = array("Success"=>-1);
		}
				
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}


function GetCustomerProfile($xml)
{
		/* Parse xml */		
		$AccountId = $xml->AccId;
		$CustomerId = $xml->CustId;		
		$SearchText = str_replace("'","''",trim($xml->SearchText));
		/* Parse xml */
		
		$aResults ='';
		$ConnLink ='';
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
				
			if($SearchText !='')
			{
				$sql = "CALL GetCustomerProfile('".$AccountId."','".$CustomerId."');";
				
																				
				$result = mysql_query($sql) or die(mysql_error());  

			
				if (mysql_affected_rows()<=0) {					
					$aResults[] = array("Success"=>0);
				}
				else{		
						$cdtls = '';
						
						if(mysql_num_rows($result) >0)
						{
							while($row = mysql_fetch_array($result)){

								$cdtls[] = array("CId"=>$row['CId']
													, "CustomerId"=>$row['CustomerId']
													, "CustomerName"=>$row['CustomerName']
													, "Gender"=>$row['Gender']
													, "AccountId"=>$row['AccountId']
													, "EmailId"=>$row['EmailId']
													, "Cell_Number"=>$row['Cell_Number']
													, "CountryCallingCode"=>$row['CountryCallingCode']
													, "City"=>$row['City']
													, "DOB"=>$row['DOB']
													, "Annv"=>$row['Annv']
													, "Zip"=>$row['Zip']												
												);						
							}						
							$aResults[] = array("Success"=>1, "CDtls" =>$cdtls);
						}
						else
						{
							$aResults[] = array("Success"=>0);
						}										
				}
			}
			else
			{
				$aResults[] = array("Success"=>0);
			}
			
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}



/* Amol's method to set master tables */

/* Amol's method to set master tables */
function SetPreferenses($input)
{		
		/* Parse xml */
		
		$inputDecode =json_decode($input);
		
		//echo $input;
		

		$PreferenseId = $inputDecode->{'pid'};		
		$EntityId = $inputDecode->{'eid'};
		$PreferenseName = str_replace("'","''",trim($inputDecode->{'pnm'}));	
		$IsSynced = $inputDecode->{'issyn'};
		$CreatedOnDateTime =  $inputDecode->{'crdt'};
				
		$aResults ='';
		$ConnLink ='';
		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);						
			$CreatedOnDateTime = new DateTime($CreatedOnDateTime);
			$SysDatetime = $CreatedOnDateTime->format('Y-m-d H:i:s');		
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time			
			$sql = "CALL SetSeatingPreferenses('".$PreferenseId."','".$EntityId."','".$PreferenseName."','".$IsSynced."','".$SysDatetime."');";										
			//echo $sql;
			$result = mysql_query($sql) or die(mysql_error()); 
			if (mysql_affected_rows()<=0) 
			{
				$aResults[] = array("Success"=>0);			
			}
			else
			{
						if(mysql_num_rows($result) >0)
						{											
							$aResults[] = array("Success"=>1);
						}
			}
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			echo 'some error' . $aResults ;
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}


function SetConciergeOffers($input)
{		
		
		 //echo $input;
		  $inputDecode =json_decode($input);
		  
		  
		  $ConciergeOfferId = $inputDecode->{'coid'}; 		
		  $ConciergeSettingId = $inputDecode->{'csid'};  
		  //$OfferTitle = str_replace("'","''",trim($inputDecode->{'ot'}));
		  //$OfferTitle =trim($inputDecode->{'ot'});
		  $OfferTitle = str_replace("'","''",str_replace("XXXpppXXX","\"",str_replace("XXXAMPXX","&",trim($inputDecode->{'ot'})))); 	
		  $IsActive = str_replace("'","''",trim($inputDecode->{'isact'})); 
		  $ValidFromDate = str_replace("'","''",trim($inputDecode->{'frdt'})); 
		  $ValidToDate = str_replace("'","''",trim($inputDecode->{'todt'})); 
		  $ValidFromTime = str_replace("'","''",trim($inputDecode->{'frtm'}));
		  $ValidToTime = str_replace("'","''",trim($inputDecode->{'totm'})); 
		  $ValidOnWeekDays = str_replace("'","''",trim($inputDecode->{'wkday'})); 
		  $NoOfOffers = str_replace("'","''",trim($inputDecode->{'totoffers'}));   
		  $Criteria = str_replace("'","''",trim($inputDecode->{'cr'}));  
		  $VoucherValidForSources = str_replace("'","''",trim($inputDecode->{'srcs'})); 
		  //$SMSText = trim($inputDecode->{'smstxt'}); 
		$SMSText = str_replace("'","''",str_replace("XXXpppXXX","\"",str_replace("XXXAMPXX","&",trim($inputDecode->{'smstxt'})))); 		  
		  //$EmailText =trim($inputDecode->{'emtxt'});  
		   $EmailText = str_replace("'","''",str_replace("XXXpppXXX","\"",str_replace("XXXAMPXX","&",trim($inputDecode->{'emtxt'})))); 			
		  $CreatedOn =  $inputDecode->{'crdt'};
		 // $AboutThisOffer = trim($inputDecode->{'offerdesc'}); 
		  $AboutThisOffer = str_replace("'","''",str_replace("XXXpppXXX","\"",str_replace("XXXAMPXX","&",trim($inputDecode->{'offerdesc'})))); 	
		  $NoOfPAX = str_replace("'","''",trim($inputDecode->{'pax'}));   
		  $PAXCriteria = str_replace("'","''",trim($inputDecode->{'paxcr'}));  
		  $IsSynced =  $inputDecode->{'issyn'};   

		  $aResults ='';
		  $ConnLink ='';
		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
						
			$CreatedOn = new DateTime($CreatedOn);
			$SysDatetime = $CreatedOn->format('Y-m-d H:i:s');
			$SysValidFromDate ='';
			$SysValidToDate ='';
				if($ValidFromDate!='')
				{
			$ValidFromDate = new DateTime($ValidFromDate);		
			$SysValidFromDate = $ValidFromDate->format('Y-m-d');
				}			
			if($ValidFromDate!='')
				{
			$ValidToDate = new DateTime($ValidToDate);
			$SysValidToDate = $ValidToDate->format('Y-m-d');				
			}
			
			$AboutThisOffer=escapeJsonString($AboutThisOffer);
			$SMSText=escapeJsonString($SMSText);
			$EmailText=escapeJsonString($EmailText);
			
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time
			$sql = "CALL SetConciergeAllOffers('".$ConciergeOfferId."','".$ConciergeSettingId."','".$OfferTitle."',
			'".$IsActive."','".$SysValidFromDate."','".$SysValidToDate."','".$ValidFromTime."','".$ValidToTime."','".$ValidOnWeekDays."',
			'".$NoOfOffers."','".$Criteria."','".$VoucherValidForSources."','".$SMSText."','".$EmailText."','".$SysDatetime."',
			'".$AboutThisOffer."','".$NoOfPAX."','".$PAXCriteria."','".$IsSynced."');";
										
			echo $sql;
			$result = mysql_query($sql) or die(mysql_error());  
			
		
			if (mysql_affected_rows()<=0) 
			{
				$aResults[] = array("Success"=>0);			
			}
			else
			{
						if(mysql_num_rows($result) >0)
						{											
							$aResults[] = array("Success"=>1);
						}
			}
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			echo 'some error' . $aResults ;
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}


function SetConciergeUsers($input)
{		
		/* Parse xml */
		
		$inputDecode =json_decode($input);
		
		//echo $input;
		$ConciergeUserId = $inputDecode->{'cuid'};		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$LoginName = str_replace("'","''",trim($inputDecode->{'lnm'}));	
		$Password = str_replace("'","''",trim($inputDecode->{'pwd'}));	
		$Name = str_replace("'","''",trim($inputDecode->{'nm'}));	
		$Cell_Number = str_replace("'","''",trim($inputDecode->{'cl'}));
		$EmailId = str_replace("'","''",trim($inputDecode->{'em'}));	
		$IsActive = str_replace("'","''",trim($inputDecode->{'isact'}));	
		$UserType = str_replace("'","''",trim($inputDecode->{'utp'}));			
		$IsSynced = $inputDecode->{'issyn'};
		$CreatedOnDateTime =  $inputDecode->{'crdt'};			

		$aResults ='';
		$ConnLink ='';
		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
						
			$CreatedOnDateTime = new DateTime($CreatedOnDateTime);
			$SysDatetime = $CreatedOnDateTime->format('Y-m-d H:i:s');		
			
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time
			$sql = "CALL SetConciergeUsers('".$ConciergeUserId."','".$ConciergeSettingId."','".$LoginName."',
			'".$Password."','".$Name."','".$Cell_Number."','".$EmailId."','".$IsActive."','".$UserType."',
			'".$IsSynced."','".$SysDatetime."');";
										
			//echo $sql;
			$result = mysql_query($sql) or die(mysql_error());  
			
		
			if (mysql_affected_rows()<=0) 
			{
				$aResults[] = array("Success"=>0);			
			}
			else
			{
						if(mysql_num_rows($result) >0)
						{											
							$aResults[] = array("Success"=>1);
						}
			}
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			echo 'some error' . $aResults ;
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}


function SetTableSettings($input)
{		
		/* Parse xml */
		
		$inputDecode =json_decode($input);
		
		//echo $input;
		
		$ConciergeTableSettingId = $inputDecode->{'ctid'};		
		$EntityId = $inputDecode->{'eid'};
		$MatchingSeatingPreferenceIds = str_replace("'","''",trim($inputDecode->{'pid'}));	
		$TableNo = str_replace("'","''",trim($inputDecode->{'tbnm'}));	
		$SeatingCapacity = str_replace("'","''",trim($inputDecode->{'sc'}));	
		$MaxCapacity = str_replace("'","''",trim($inputDecode->{'mc'}));
		$CreatedOn =  $inputDecode->{'crdt'};
		$IsSynced = $inputDecode->{'issyn'};
		$aResults ='';
		$ConnLink ='';		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
						
			$CreatedOn = new DateTime($CreatedOn);
			$SysDatetime = $CreatedOn->format('Y-m-d H:i:s');		
			
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time
			$sql = "CALL SetConciergeTableSettings('".$ConciergeTableSettingId."','".$EntityId."',
			'".$MatchingSeatingPreferenceIds."','".$TableNo."','".$SeatingCapacity."','".$MaxCapacity."','".$SysDatetime."','".$IsSynced."');";
										
			//echo $sql;
			$result = mysql_query($sql) or die(mysql_error());  
			
		
			if (mysql_affected_rows()<=0) 
			{
				$aResults[] = array("Success"=>0);			
			}
			else
			{
						if(mysql_num_rows($result) >0)
						{											
							$aResults[] = array("Success"=>1);
						}
			}
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			echo 'some error' . $aResults ;
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}

function SetTableTATSettings($input)
{		
		/* Parse xml */
		
		$inputDecode =json_decode($input);
		
		//echo $input;	

		$ConciergeTableTurnaroundTimeId = $inputDecode->{'tatid'};		
		$ConciergeTableSettingId = $inputDecode->{'tableid'};
		$TurnaroundTimeSun = str_replace("'","''",trim($inputDecode->{'sun'}));	
		$TurnaroundTimeMon = str_replace("'","''",trim($inputDecode->{'mon'}));	
		$TurnaroundTimeTue = str_replace("'","''",trim($inputDecode->{'tue'}));	
		$TurnaroundTimeWed = str_replace("'","''",trim($inputDecode->{'wed'}));
		$TurnaroundTimeThu = str_replace("'","''",trim($inputDecode->{'thu'}));	
		$TurnaroundTimeFri = str_replace("'","''",trim($inputDecode->{'fri'}));	
		$TurnaroundTimeSat = str_replace("'","''",trim($inputDecode->{'sat'}));	
		$ApplicableFor = str_replace("'","''",trim($inputDecode->{'dntp'}));
		$CreatedOn =  $inputDecode->{'crdt'};
		$IsSynced = $inputDecode->{'issyn'};
		$aResults ='';
		$ConnLink ='';		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
						
			$CreatedOn = new DateTime($CreatedOn);
			$SysDatetime = $CreatedOn->format('Y-m-d H:i:s');		
			
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time
			$sql = "CALL SetConciergeTableTAT('".$ConciergeTableTurnaroundTimeId."','".$ConciergeTableSettingId."',
			'".$TurnaroundTimeSun."','".$TurnaroundTimeMon."','".$TurnaroundTimeTue."','".$TurnaroundTimeWed."',
			'".$TurnaroundTimeThu."','".$TurnaroundTimeFri."','".$TurnaroundTimeSat."','".$ApplicableFor."',
			'".$SysDatetime."','".$IsSynced."');";
										
			//echo $sql;
			$result = mysql_query($sql) or die(mysql_error());  
			
		
			if (mysql_affected_rows()<=0) 
			{
				$aResults[] = array("Success"=>0);			
			}
			else
			{
						if(mysql_num_rows($result) >0)
						{											
							$aResults[] = array("Success"=>1);
						}
			}
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			echo 'some error' . $aResults ;
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}

function SetConciergeSettings($input)
{  
  /* Parse xml */
  
  $inputDecode =json_decode($input);
  //echo $input; 
  $ConciergeSettingId = $inputDecode->{'csid'};  
  $EntityId = $inputDecode->{'eid'};
  $AccountId = str_replace("'","''",trim($inputDecode->{'acid'})); 
  $SettingExpiresOn = str_replace("'","''",trim($inputDecode->{'exdt'})); 
  $CheckInSMSMessage = str_replace("'","''",trim($inputDecode->{'ckinsms'})); 
  $CheckInEmailMessage = str_replace("'","''",trim($inputDecode->{'ckinem'}));
  $CheckOutSMSMessage = str_replace("'","''",trim($inputDecode->{'ckoutsms'})); 
  $CheckOutEmailMessage = str_replace("'","''",trim($inputDecode->{'ckoutem'}));
  $LogoPath = str_replace("'","''",trim($inputDecode->{'lgpath'})); 
  $CreatedOn =  $inputDecode->{'crdt'}; 
  $IsSynced = $inputDecode->{'issyn'};  
  $EntityName = str_replace("'","''",trim($inputDecode->{'enm'}));
  $TimeZone = str_replace("'","''",trim($inputDecode->{'tmzone'})); 
  $ManagerName = str_replace("'","''",trim($inputDecode->{'mnm'}));
  $Emailid = str_replace("'","''",trim($inputDecode->{'em'}));
  $Cell_Number = str_replace("'","''",trim($inputDecode->{'cno'})); 
  $CountryCallingCode = str_replace("'","''",trim($inputDecode->{'cccode'}));
  $BreakfastStart = str_replace("'","''",trim($inputDecode->{'bfstart'}));
	$BreakfastEnd = str_replace("'","''",trim($inputDecode->{'bfend'}));
	$LunchStart = str_replace("'","''",trim($inputDecode->{'lstart'}));
	$LunchEnd = str_replace("'","''",trim($inputDecode->{'lend'}));
	$DinnerStart = str_replace("'","''",trim($inputDecode->{'dstart'}));
	$DinnerEnd = str_replace("'","''",trim($inputDecode->{'dend'}));
	$SmsSenderName = str_replace("'","''",trim($inputDecode->{'ssnm'}));
	$EmailSenderName = str_replace("'","''",trim($inputDecode->{'esnm'}));
	$EmailSenderEmailId = str_replace("'","''",trim($inputDecode->{'esem'}));
	$City = str_replace("'","''",trim($inputDecode->{'city'}));
	$Area = str_replace("'","''",trim($inputDecode->{'area'}));
		
	$CstSendBookMsg = str_replace("'","''",trim($inputDecode->{'csbm'}));
	$CstSendAmendMsg = str_replace("'","''",trim($inputDecode->{'csam'}));
	$CstSendCancelMsg = str_replace("'","''",trim($inputDecode->{'cscm'}));
	$CstSendCheckinMsg = str_replace("'","''",trim($inputDecode->{'cscim'}));
	$CstSendCheckoutMsg = str_replace("'","''",trim($inputDecode->{'cscom'}));
	$CstSendOfferMsg = str_replace("'","''",trim($inputDecode->{'csom'}));
	$MngSendBookMsg = str_replace("'","''",trim($inputDecode->{'msbm'}));
	$MngSendAmendMsg = str_replace("'","''",trim($inputDecode->{'msam'}));
	$MngSendCancelMsg = str_replace("'","''",trim($inputDecode->{'mscm'})); 

	$EntGroupName = str_replace("'","''",trim($inputDecode->{'gnm'})); 
  	$EntGroupId = str_replace("'","''",trim($inputDecode->{'gi'})); 
  
  $aResults ='';
  $ConnLink ='';  
  try
  {
   $ConnLink = ConnectToMySQL();
   mysql_select_db("konektconci", $ConnLink);
   //mysql_select_db("dummy_konektconci", $ConnLink);
      
   $CreatedOn = new DateTime($CreatedOn);
   $SysDatetime = $CreatedOn->format('Y-m-d H:i:s'); 

   $SettingExpiresOn = new DateTime($SettingExpiresOn);  
   $SysExpiresOn = $SettingExpiresOn->format('Y-m-d H:i:s'); 
   
   	$CheckInSMSMessage=escapeJsonString($CheckInSMSMessage);
	$CheckInEmailMessage=escapeJsonString($CheckInEmailMessage);
	$CheckOutSMSMessage=escapeJsonString($CheckOutSMSMessage);
	$CheckOutEmailMessage=escapeJsonString($CheckOutEmailMessage);
	
  
   //$SysDatetime = date("Y-m-d H:i(worry)", time());  // local device time
   $sql = "CALL SetConciergeSettings('".$ConciergeSettingId."','".$EntityId."',
   '".$AccountId."','".$SysExpiresOn."','".$CheckInSMSMessage."','".$CheckInEmailMessage."',
   '".$CheckOutSMSMessage."','".$CheckOutEmailMessage."','".$LogoPath."',
   '".$SysDatetime."','".$EntityName."','".$TimeZone."','".$ManagerName."',
   '".$Emailid."','".$Cell_Number."','".$CountryCallingCode."','".$IsSynced."',
   '".$BreakfastStart."','".$BreakfastEnd."','".$LunchStart."','".$LunchEnd."','".$DinnerStart."',
   '".$DinnerEnd."','".$SmsSenderName."','".$EmailSenderName."','".$EmailSenderEmailId."','".$City."','".$Area."',
   '".$CstSendBookMsg."','".$CstSendAmendMsg."','".$CstSendCancelMsg."','".$CstSendCheckinMsg."','".$CstSendCheckoutMsg."','".$CstSendOfferMsg."','".$MngSendBookMsg."','".$MngSendAmendMsg."','".$MngSendCancelMsg."','".$EntGroupName."','".$EntGroupId."');";
          
   //echo $sql;
   $result = mysql_query($sql) or die(mysql_error());    
  
   if (mysql_affected_rows()<=0) 
   {
    $aResults[] = array("Success"=>0);   
   }
   else
   {
      if(mysql_num_rows($result) >0)
      {           
       $aResults[] = array("Success"=>1);
      }
   }
  }
  catch(Exception $e){
   $aResults[] = array("Success"=>-1);
   echo 'some error' . $aResults ;
  }

  mysql_close($ConnLink);   
  $json_response = json_encode($aResults);
  # Optionally: Wrap the response in a callback function for JSONP cross-domain support
  if(ISSET($_REQUEST["callback"])) {
   $json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
   }
  # Return the response
  echo $json_response;
}



function SetConciergeServerDetails($input)
{  
 
  $inputDecode =json_decode($input);
  //echo $input; 
  $ConciergeSettingId = $inputDecode->{'csid'};  
  $EntityId = $inputDecode->{'eid'};
  $ServerId = $inputDecode->{'sid'};
  $ServerName = str_replace("'","''",trim($inputDecode->{'snm'}));
  $IsDeleted = $inputDecode->{'isd'};
      
  //$CreatedOn =  $inputDecode->{'crdt'}; 
  //$IsSynced = $inputDecode->{'issyn'};  
    
  $aResults ='';
  $ConnLink ='';  
  try
  {
	   $ConnLink = ConnectToMySQL();
	   mysql_select_db("konektconci", $ConnLink);
		  
	   //$CreatedOn = new DateTime($CreatedOn);
	  // $SysDatetime = $CreatedOn->format('Y-m-d H:i:s'); 

	   
		$ServerName=escapeJsonString($ServerName);
		  
	   $sql = "CALL SetConciergeServerDetails('".$ConciergeSettingId."','".$EntityId."',
	   '".$ServerId."','".$ServerName."','".$IsDeleted."');";
			  
	   //echo $sql;
	   $result = mysql_query($sql) or die(mysql_error());    
	  
	   if (mysql_affected_rows()<=0) 
	   {
		$aResults[] = array("Success"=>0);   
	   }
	   else
	   {
		  if(mysql_num_rows($result) >0)
		  {           
		   $aResults[] = array("Success"=>1);
		  }
	   }
  }
  catch(Exception $e){
   $aResults[] = array("Success"=>-1);
   echo 'some error' . $aResults ;
  }

  mysql_close($ConnLink);   
  $json_response = json_encode($aResults);
  # Optionally: Wrap the response in a callback function for JSONP cross-domain support
  if(ISSET($_REQUEST["callback"])) {
   $json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
   }
  # Return the response
  echo $json_response;
}


function GetServerDetails($input)
{
		$inputDecode =json_decode($input);
		
		$conciergeSettingId = $inputDecode->{'csid'};  
		$entityId = $inputDecode->{'eid'};
		
		$aResults ='';
		$ConnLink ='';
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
					
			$SQL = "CALL GetServerDetails('".$conciergeSettingId."','".$entityId."');"; 			
																				
			$result = mysql_query($SQL) or die(mysql_error());  
		
			if (mysql_affected_rows()<=0) {					
				$aResults[] = array("Success"=>0);
			}
			else{		
					$offdtls = '';
					#echo mysql_num_rows($result);
					if(mysql_num_rows($result) >0)
					{
						while($row = mysql_fetch_array($result)){
			
							$serdtls[] = array("CSerId"=>$row['ConciergeServerId']
											, "SerNm"=>$row['ServerName']											
									);													
						}						
						$aResults[] = array("Success"=>1, "SerDtls" =>$serdtls);
					}
					else
					{
						$aResults[] = array("Success"=>0);
					}										
			}	
			
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>0);
		}

		mysql_close($ConnLink);			
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}



/*
Make Async http call
$url = Url to make Async call e.g. http://www.somesite.com or http://www.somesite.com:80
$paramstring = Parameter string in the format of querystring with name=value separated by "&" e.g.: name1=value1&name2=value2
$method = "GET" or "POST"
$timeout = Timeout in seconds. Default 30 seconds
$returnresponse = true/false. Default false. If true, waits for response and return back response data.
*/
function async_call($url, $paramstring, $method='post', $timeout='30', $returnresponse=true) 
{
	$method = strtoupper($method);
	$urlParts=parse_url($url);      
	$fp = fsockopen($urlParts['host'],         
			isset($urlParts['port'])?$urlParts['port']:80,         
			$errno, $errstr, $timeout);
	
	//If method="GET", add querystring parameters
	if ($method='GET')
		$urlParts['path'] .= '?'.$paramstring;
		
	//echo $paramstring;
	$out = "$method ".$urlParts['path']." HTTP/1.1\r\n";     
	$out.= "Host: ".$urlParts['host']."\r\n";
	$out.= "Connection: Close\r\n";
	
	//If method="POST", add post parameters in http request body
	if ($method='POST')
	{
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";     
		$out.= "Content-Length: ".strlen($paramstring)."\r\n\r\n";
		$out.= $paramstring;      
	}
	
	fwrite($fp, $out);     
	
	//Wait for response and return back response only if $returnresponse=true
	if ($returnresponse)
	{
		echo stream_get_contents($fp);
	}
	
	fclose($fp); 
}


function LogErr($msg)
{
 $errFile = date('Ymd').".txt";
 $fh = fopen($errFile, 'a') or die("can't open file");
 fwrite($fh, $msg);
 fwrite($fh,"\r\n");
 fclose($fh); 
}

function escapeJsonString($value) 
{ # list from www.json.org: (\b backspace, \f formfeed)
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace( $replacements,$escapers, $value);
    return $result;
}





?>

