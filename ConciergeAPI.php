

<?php 


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

if(ISSET($_REQUEST['tp']))
		$type = $_REQUEST['tp'];

if(ISSET($_REQUEST['d']))
		$data = $_REQUEST['d'];
			
		
	switch (strtoupper($type)) {
		case 'GAD':
			GetAccountDetails($xml);
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
			
			
		case 'AMOLSBT':	
			//book table
			AmolSetBookTable($data);
			break;
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
		   //Set Concierge Users
		   SetTableSettings($data);
		   break;
		  case 'TAT': 
		   //Set Concierge Users
		   SetTableTATSettings($data);
		   break;
		  case 'SCS': 
		   //Set Concierge Users
		   SetConciergeSettings($data);
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
    $con = mysql_connect("192.168.1.52","conci","Mobikontech");
	//$con = mysql_connect("localhost:3307","root","Marijuana@77");		
    if (!$con)
    {
      die('Could not connect: ' . mysql_error());
    }

    return $con;
}

function ConnectToMySQLi() {
    
	$mysqli = new mysqli("192.168.1.52","conci","Mobikontech");
	//$mysqli =new mysqli("localhost:3307","root","Marijuana@77");	
    if (mysqli_connect_errno())
    {
      die('Could not connect: ' . mysqli_connect_error());
    }

    return $mysqli;
}

function LoadXml($xml_str)
{	
	$xml = simplexml_load_string($xml_str);
	return $xml;
	
}



function Authenticate($loginName,$password)
{
		$aResults ='';
		$ConnLink ='';
		
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
			
	$sql = "CALL AuthUser('".$loginName."','".$password."');";
						
	$result = mysql_query($sql);
	
	if (mysql_affected_rows()<=0) 
	{
		$aResults[] = array("Success"=>0);	
	}
	else
	{
		while($row = mysql_fetch_array($result))
		{
			  $aResults[] = array( "oid"=>$row['OID'],"csid"=>$row['ConciergeSettingId'],"acid"=>$row['AccountId'] ,"eid"=>$row['EntityId'],
"bkfs"=>$row['BreakfastStart'],"bkfe"=>$row['BreakfastEnd'],"ls"=>$row['LunchStart'],"le"=>$row['LunchEnd'],"ds"=>$row['DinnerStart']
,"de"=>$row['DinnerEnd'],"cc"=>$row['CountryCallingCode'],"Success"=>1);

			   
		}
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
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "CALL GetCancelledBookingDetails(".$ConciergeSettingId.",'".$cresult."');"; 			
									
			$result = mysql_query($SQL) or die(mysql_error());  
		
			if (mysql_affected_rows()<=0) {					
				$aResults[] = array("Success"=>0);
			}
			else{		
					$cbdtls = '';
					#echo mysql_num_rows($result);
					if(mysql_num_rows($result) >0)
					{
						while($row = mysql_fetch_array($result)){
							//echo $row['CID']. " - ". $row['ConciergeTableBookingRequestId'];
							//echo "<br />";
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
						$aResults[] = array("Success"=>1, "CBDtls" =>$cbdtls);
					}
					else
					{
						$aResults[] = array("Success"=>0);
					}										
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
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "CALL GetBookingDetails(".$ConciergeSettingId.",'".$cresult."','".$SearchText."','".$DineType."');"; 			
									
			$result = mysql_query($SQL) or die(mysql_error());  
		
			if (mysql_affected_rows()<=0) {					
				$aResults[] = array("Success"=>0);
			}
			else{		
					$bdtls = '';
					#echo mysql_num_rows($result);
					if(mysql_num_rows($result) >0)
					{
						while($row = mysql_fetch_array($result)){
							//echo $row['CID']. " - ". $row['ConciergeTableBookingRequestId'];
							//echo "<br />";
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
											, "CheckOutStatus"=>$row['CheckOutStatus']
											);	
												
						}						
						$aResults[] = array("Success"=>1, "BDtls" =>$bdtls);
					}
					else
					{
						$aResults[] = array("Success"=>0);
					}										
			}					
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			
			//$GLOBALS['glog']->error($e);
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

function GetCheckins($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
			$BookingDate = new DateTime($BookingDate);
			$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "CALL GetCheckinCount(".$ConciergeSettingId.",'".$cresult."');"; 			
			#$SQL = "CALL GetBookingDetails(1,'2013-02-07');";
																		
			$result = mysql_query($SQL) or die(mysql_error());  
		
			if (mysql_affected_rows()<=0) {					
				$aResults[] = array("TotChk"=>0,"TotPax"=>0);
			}
			else{		
					$bdtls = '';
					#echo mysql_num_rows($result);
					if(mysql_num_rows($result) >0)
					{
						while($row = mysql_fetch_array($result)){
							//echo $row['CID']. " - ". $row['ConciergeTableBookingRequestId'];
							//echo "<br />";
							$aResults[] = array("TotChk"=>$row['TotChk'], "TotPax"=>$row['TotPax']);	
												
						}						
						//$aResults[] = array("Success"=>1, "BDtls" =>$bdtls);
					}
					else
					{
						$aResults[] = array("TotChk"=>0,"TotPax"=>0);
					}										
			}					
		}
		catch(Exception $e){
			$aResults[] = array("TotChk"=>0,"TotPax"=>0);
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

function SetCheckinoutMessages($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$CheckInSms = str_replace("'","''",trim($inputDecode->{'chkisms'}));
		$CheckInEmail = str_replace("'","''",trim($inputDecode->{'chkiemail'}));
		$CheckOutSms = str_replace("'","''",trim($inputDecode->{'chkosms'}));
		$CheckOutEmail = str_replace("'","''",trim($inputDecode->{'chkoemail'}));
		#$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
				
			$SQL = "CALL SetCheckinoutMessages(".$ConciergeSettingId.",'".$CheckInSms."','".$CheckInEmail."','".$CheckOutSms."','".$CheckOutEmail."');"; 			
																				
			$result = mysql_query($SQL) or die(mysql_error());  
		
		
			/*if (mysql_affected_rows()<=0) {					
				$aResults[] = array("Success"=>0);
			}
			else{	*/
					if(mysql_num_rows($result) >0)
					{
						$UpdateStatus ='';
						while($row = mysql_fetch_array($result)){
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
							$aResults[] = array("Success"=>0);	//no updation happened on my sql or record does not exist.
						}												
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}										
			//}					
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1); // some error occoured
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
							
		$ConnLink = ConnectToMySQL();
		mysql_select_db("konektconci", $ConnLink);
				
		$sql = "CALL GetCustomerInfo('".$accountid."','".$action."');";			
		
		$result = mysql_query($sql) or die(mysql_error());  
						
		while ($row = mysql_fetch_array($result)) 
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
														
		mysql_close($ConnLink);			
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
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
			//$BookingDate = new DateTime($BookingDate);
			//$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "CALL GetAllOffersDetails(".$ConciergeSettingId.");"; 			
																					
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
						$aResults[] = array("Success"=>1, "OffDtls" =>$offdtls);
					}
					else
					{
						$aResults[] = array("Success"=>0);
					}										
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


function GetCheckinoutMessages($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		//$BookingDate = $inputDecode->{'dt'};
		
		$aResults ='';
		$ConnLink ='';
						
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
			//$BookingDate = new DateTime($BookingDate);
			//$cresult = $BookingDate->format('Y-m-d');	
			
			$SQL = "CALL GetCheckinoutMessages(".$ConciergeSettingId.");"; 			
																					
			$result = mysql_query($SQL) or die(mysql_error());  
		
			if (mysql_affected_rows()<=0) {					
				$aResults[] = array("Success"=>0);
			}
			else{		
					$chkmsg = '';
					#echo mysql_num_rows($result);
					if(mysql_num_rows($result) >0)
					{
						while($row = mysql_fetch_array($result)){
			
							$chkmsg[] = array("ChkISms"=>$row['CheckInSMSMessage']
											, "ChkIEmail"=>$row['CheckInEmailMessage']											
											, "ChkOSms"=>$row['CheckOutSMSMessage']
											, "ChkOEmail"=>$row['CheckOutEmailMessage']
										);													
						}						
						$aResults[] = array("Success"=>1, "ChkMsg" =>$chkmsg);
					}
					else
					{
						$aResults[] = array("Success"=>0);
					}										
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
			
		try
		{
			#$ConnLink = ConnectToMySQL();
			#mysql_select_db("konektconci", $ConnLink);

			$ConnLink = ConnectToMySQLi();
			mysqli_select_db($ConnLink,"konektconci");				
				
			$SQL = "CALL SetCheckinTable(".$ConciergeSettingId.",'".$ConciergeBookingId."','".$cresult."');"; 			
																				
			#$result = mysql_query($SQL) or die(mysql_error());  
			$result = mysqli_query($ConnLink,$SQL) or die(mysqli_error($ConnLink)); 			
		
		
			/*if (mysql_affected_rows()<=0) {	*/
			if (mysqli_affected_rows($ConnLink)<=0) {				
				$aResults[] = array("Success"=>0);
			}
			else{	
					//if(mysql_num_rows($result) >0)
					if(mysqli_num_rows($result) >0)
					{
						$UpdateStatus ='';
						#$CheckInDeviceTime='';
						#$CheckInUTCDateTime='';
						$ActiveCheckins=''; $ActivePax='';
												
						//while($row = mysql_fetch_array($result)){
						$row = mysqli_fetch_assoc($result);
						
							$UpdateStatus = $row['ChkStatus'];
							#$CheckInDeviceTime=$row['DeviceTime'];
							#$CheckInUTCDateTime=$row['CheckInUTCDateTime'];	
							#$ModifiedTime=$row['ModifiedOn'];	
							$ActiveCheckins=$row['ActiveCheckins'];		
							$ActivePax=$row['ActivePax'];							
						//}
						
						mysqli_free_result($result);
						mysqli_next_result($ConnLink); 
						
						if($UpdateStatus =='1')
						{
						
							$sql = "CALL GetManagerDetails(".$ConciergeSettingId.");";

							#$OuletDetails = mysql_query($sql) or die(mysql_error());  
							#$outrow =  mysql_fetch_array($OuletDetails);
							
							$OuletDetails = mysqli_query($ConnLink,$sql) or die(mysqli_error($ConnLink));  
							$outrow =  mysqli_fetch_assoc($OuletDetails);
							
							
							mysqli_free_result($OuletDetails);
							mysqli_next_result($ConnLink);	

							$ReturnResult = GenerateResponse($row,$outrow,'Checkin');	


							# Based on type send message.
							
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
							$aResults[] = array("Success"=>$UpdateStatus);	//-1 and -2
						}
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}										
			}					
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1); // some error occoured
		}

		//mysql_close($ConnLink);		
		mysqli_close($ConnLink);		
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
		$RequestNote = str_replace("'","''",trim($inputDecode->{'reqnt'}));
		$Source = $inputDecode->{'source'};
		$Gender = $inputDecode->{'gen'};
		$SeatingPreferenceIDs = $inputDecode->{'spref'};	//may be empty in new case
		$TableIDs = $inputDecode->{'tabid'};
		$TableNos = $inputDecode->{'tabno'};
		
		$DeviceDateTime = $inputDecode->{'sysdt'};
		$OfferId =  $inputDecode->{'offid'};
	
			
		$aResults ='';
		$ConnLink ='';
		
		try
		{
					
		$ConnLink = ConnectToMySQLi();
		mysqli_select_db($ConnLink,"konektconci");
		
			//booking date and time
			$cdate = new DateTime($BookingDate);
			$cresult = $cdate->format('Y-m-d');			
			$BookingDateTime = $cresult . ' ' . $BookingTime ; 
						
			//$SysDatetime = String.Format("{0:g}", DateTime.Now);
			//$SysDatetime = date("m/d/y H:i:s");
						
			$DeviceDateTime = new DateTime($DeviceDateTime);
			$SysDatetime = $DeviceDateTime->format('Y-m-d H:i:s');		
		
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time

			
$sql = "CALL SetBookTable('".$BookingMode."','".$SysDatetime."','".$BookingDateTime."','".$ConciergeBookingId."'
,'".$ConciergeSettingId."','".$CustomerId."','".$NoOfPeople."','".$CallingCode."','".$CustomerNumber."','".$CustomerEmail."','".$CustomerName."'
,'".$RequestNote."','".$cresult."','".$BookingTime."','".$BookingType."','".$Source."','".$Gender."','".$SeatingPreferenceIDs."'
,'".$TableIDs."','".$TableNos."','".$OfferId."');";
										
			
			//$result = mysql_query($sql) or die(mysql_error());  
			$result = mysqli_query($ConnLink,$sql) or die(mysqli_error($ConnLink));  
		
			//if (mysql_affected_rows()<=0) {
			if (mysqli_affected_rows($ConnLink)<=0) {	
			
				$aResults[] = array("Success"=>0);
			
			}
			else{
					//if(mysql_num_rows($result) >0)
					if(mysqli_num_rows($result) >0)
					{
						$ReturnResult = '';
						$Response = '';
						$row = '';
						$ActiveCheckins = ''; 
						$ActivePax= '';		
						$AlreadyBooked ='';								
						//$row = mysql_fetch_array($result);
						$row = mysqli_fetch_assoc($result);
			
						if(ISSET($row['BkStatus']))
							$AlreadyBooked = $row['BkStatus'];
						if($AlreadyBooked == '-1')
						{
							//means someone already booked that table,so please select differenct table and then book again.
							$aResults[] = array("Success"=>-1);	
						}
						else
						{ 
						
							$ActiveCheckins=$row['ActiveCheckins'];		
							$ActivePax=$row['ActivePax'];	
															
							mysqli_free_result($result);
							mysqli_next_result($ConnLink); 
				
							$sql = "CALL GetManagerDetails(".$ConciergeSettingId.");";
						
							$OuletDetails = mysqli_query($ConnLink,$sql) or die(mysqli_error($ConnLink));  
							$outrow =  mysqli_fetch_assoc($OuletDetails);
							
							
							mysqli_free_result($OuletDetails);
							mysqli_next_result($ConnLink);						
							//mysql_free_result($OuletDetails);
															
							if($row['pmode'] == "n")
							{
								$ReturnResult = GenerateResponse($row,$outrow,'Book');	
							}
							else if($row['pmode'] == "e" || $row['pmode'] == "ep" )	
							{
								$ReturnResult = GenerateResponse($row,$outrow,'Amend');	
							}

							if($row['pmode'] == "n" || $row['pmode'] == "e" )	
							{
								if(strtolower($row['CheckedIn']) == strtolower("yes"))
								{
									$ReturnResult .= GenerateResponse($row,$outrow,'Checkin');
								}									
							}							

							# Based on type send message.
							
							$Response = "<?xml version=\"1.0\"?><Request type=\"simple\">"; 
								
							$Response .= $ReturnResult;
							$Response .= "</Request>";						
							$Response = "data=" . $Response;
																							
								//$file = 'data.txt';
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

							
							#Call asmx method

							#send same data to sql server
						#	set_time_limit(0);
						#	$konektUrl = "http://192.168.1.168/KonektSolution/Service/KonektConci.asmx/";

						#	$konektUrl .= "ConciergeTableBookingHandler";

							/* Calling  web service using SOAP 	
							$client = new SoapClient($konektUrl);
							$params = array('Data'=>$input) ;
							$result = $client->UpdateCheckInOutMessage($params);
							print_r( $result);*/
	/*
	$data = "Data={'mode':'".$mode."','CId':".$CId.",'Cust_CreatedOn':'".$Cust_CreatedOn."','Cust_ModifiedOn':'".$Cust_ModifiedOn."'
	,'acid':'".$AccountId."'
	,'csid':".$ConciergeSettingId.",'cbid':".$ConciergeBookingId.",'custid':'".$CustomerId."','nm':'".$Name."','gen':'".$Gender."'
	,'cocode':'".$CountryCallingCode."','cellno':'".$Cell_Number."','eid':'".$EmailId."','px':".$PAX.",'bdt':'".$BookingDate."'
	,'bt':'".$BookingTime."','btutc':'".$BookingUTCDateTime."','spref':'".$SeatingPreferenceIDs."','tabid':'".$TableIDs."'
	,'tabno':'".$TableNos."','btp':'".$BookingType."','reqnt':'".$Note."','chkStat':'".$CheckedIn."','bstat':'".$BookingStatus."'
	,'bs':'".$BookingSource."','credt':'".$CreatedOn."','moddt':'".$ModifiedOn."','mxtrnmin':'".$MaxTurnAround."'
	,'apptrntim':'".$ApproxTurnAroundTime."','apptrntimutc':'".$ApproxTurnAroundUTCTime."'}";
	*/


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
								//echo 'Return Value is: '. $retVal .'<br/>';
								
								
								$aResults[] = array("Success"=>1, "ActChkins" =>$ActiveCheckins,"Actpx" =>$ActivePax); // Table release done on both servers
								
						#	}	
						#	else
						#	{
						#		$aResults[] = array("Success"=>0); //release failed on sql server.									
						#	}														
						}										
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}
				

			}	
				
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1);
			echo 'some error' . $aResults ;
		}

		//mysql_close($ConnLink);			
		mysqli_close($ConnLink);
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
}

function GenerateResponse($custrow,$outrow,$type)
{	
	$OutputMsgString = "";
	$RetVal = "";
	
	$CustSMSTxt ='';
	$CustEmailTxt ='';
	$ManagerSMSTxt ='';
	$ManagerEmailTxt ='';
	$OfferSubject ='';
	$OfferSMSTxt ='';
	$OfferEmailTxt ='';
	$ManagerSubject ='';
	$CustSubject ='';
	
	$AccId = $outrow['AccountId'];
	$EntityId = $outrow['EntityId'];
	
	/* Get messages from xml */
	$objDOM = new DOMDocument();
	$objDOM->load("concimessages.xml");

	$accnodes = '';
	$Exact_Account = '';	
	
	$myxpath    = new DOMXPath($objDOM);
	$accnodes = $myxpath->query('//Account[@AccountId="'. $AccId .'" and @EntityId="'. $EntityId .'"]');

	if ($accnodes ->length > 0) {	
		$Exact_Account = $accnodes->item(0); 
	}
	else{
		$accnodes = '';			
		$accnodes = $myxpath->query('//DefaultMsg');		
		$Exact_Account = $accnodes->item(0); 		
	}
		
	/*	$xml_account = $objDOM->getElementsByTagName("Account");

		$i = 0;
		while ($xml_account->item($i) && $xml_account->item($i)->getAttribute('EntityId') != $EntityId)
		{
			$i += 1;
		}
		//echo $xml_account->item($i)->nodeValue;	
		//get the value of the first node:
		//$sniper->item(0)->nodeValue
		//For attributes of the first node you have to do it analogous:

		//$sniper->item(0)->getAttribute('level')
		
		$Exact_Account = $xml_account->item($i); 		*/
					
		switch ($type) 
		{
			case 'Book':
				
				$CustSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
												
				$CustEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 				
				
				$ManagerSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
				
				$ManagerEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 	
				
				$CustSubject = ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$ManagerSubject =  ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
				
				$OfferSubject = ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Offer")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
				
				if($custrow['pmode'] == "n" && $custrow['OfferId'] != "")
				{
					$OfferSMSTxt = ResolveTags($custrow['OfferSMSText'],$custrow);
					$OfferEmailTxt = ResolveTags($custrow['OfferEmailText'],$custrow);								
				}
				#$CustSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("BookingCustSMS")->item(0)->nodeValue,$custrow); 
				#$CustEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("BookingCustEmail")->item(0)->nodeValue,$custrow);
				#$ManagerSMSTxt =ResolveTags($Exact_Account->getElementsByTagName("BookingMSMS")->item(0)->nodeValue,$custrow);
				#$ManagerEmailTxt =	ResolveTags($Exact_Account->getElementsByTagName("BookingMEmail")->item(0)->nodeValue,$custrow);  
				
				/*	Offer send mail 	*/
				/*	Offer send mail 	*/	
			
				break;
			case 'Amend':	

				$CustSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
												
				$CustEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 				
				
				$ManagerSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
				
				$ManagerEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 	
				
				$CustSubject = ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$ManagerSubject =  ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
				
				break;
			case 'Cancel':	

				$CustSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
												
				$CustEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 				
				
				$ManagerSMSTxt = ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
				
				$ManagerEmailTxt = ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 	
				
				$CustSubject = ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$ManagerSubject =  ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
			
				break; 
			case 'Checkin':	

				$CustSubject = ResolveTags($Exact_Account->getElementsByTagName("CheckIn")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$CustSMSTxt = ResolveTags($custrow['CheckInSMSMessage'],$custrow); 
												
				$CustEmailTxt = ResolveTags($custrow['CheckInEmailMessage'],$custrow); 				
							
				break;
			case 'Checkout':	

				$CustSubject = ResolveTags($Exact_Account->getElementsByTagName("CheckOut")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$CustSMSTxt = ResolveTags($custrow['CheckOutSMSMessage'],$custrow); 
												
				$CustEmailTxt = ResolveTags($custrow['CheckOutEmailMessage'],$custrow); 				
							
				break;				
		}
	
	
			
	/* Get messages from, xml code complete*/
		
		if($type == "Checkin")
		{
			$RetVal .= "<Message>";
			$RetVal .= "<EntityId>" . $EntityId . "</EntityId>";
			$RetVal .= "<CampaignId></CampaignId>";
			$RetVal .= "<MsgType>General</MsgType>";
			$RetVal .= "<AcquireCust>No</AcquireCust>";
			$RetVal .= "<AuthKey>743A8F1B-C33F-48DC-9B3E-93BA4DD2E280</AuthKey>";
			$RetVal .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
			$RetVal .= "<FollowTemplate>True</FollowTemplate>";
			$RetVal .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
			$RetVal .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";
			
			//$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";
			
			if($custrow['Cell_Number'] =="")
			{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
			}
			else
			{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
			}
			
			$RetVal .= "</Message>";
		}
		else
		{
				$RetVal .= "<Message>";
				$RetVal .= "<EntityId>" . $EntityId . "</EntityId>";
				$RetVal .= "<CampaignId></CampaignId>";
				$RetVal .= "<MsgType>General</MsgType>";
				$RetVal .= "<AcquireCust>No</AcquireCust>";
				$RetVal .= "<AuthKey>743A8F1B-C33F-48DC-9B3E-93BA4DD2E280</AuthKey>";
				$RetVal .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
				$RetVal .= "<FollowTemplate>True</FollowTemplate>";
				$RetVal .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
				$RetVal .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";
				
				//$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";
			if($custrow['Cell_Number'] =="")
			{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
			}
			else
			{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";
			}
				$RetVal .= "</Message>";
		
				if($custrow['pmode'] != "chkin" || $custrow['pmode'] != "chkout")
				{
					$RetVal .= "<Message>";
					$RetVal .=  "<EntityId>" . $EntityId . "</EntityId>";
					$RetVal .= "<CampaignId></CampaignId>";
					$RetVal .= "<MsgType>General</MsgType>";
					$RetVal .= "<AcquireCust>No</AcquireCust>";
					$RetVal .= "<AuthKey>743A8F1B-C33F-48DC-9B3E-93BA4DD2E280</AuthKey>";
					$RetVal .= "<Text><![CDATA[".$ManagerSMSTxt."]]></Text>";
					$RetVal .="<FollowTemplate>True</FollowTemplate>";
					$RetVal .= "<EmailText><![CDATA[".$ManagerEmailTxt."]]></EmailText>";
					$RetVal .= "<EmailSubject><![CDATA[".$ManagerSubject."]]></EmailSubject>";
					//$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$outrow['MCell_Number']."\" CallingCode=\"".$outrow['MCallingCode']."\" EmailId=\"".$outrow['MEmailId']."\" />";
					
					if($outrow['MCell_Number'] =="")
					{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$outrow['MEmailId']."\" />";
					}
					else
					{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$outrow['MCell_Number']."\" CallingCode=\"".$outrow['MCallingCode']."\" EmailId=\"".$outrow['MEmailId']."\" />";
					}					
					
					$RetVal .= "</Message>";
				}
				if($custrow['pmode'] == "n" && $custrow['OfferId'] != "")
				{
					$RetVal .= "<Message>";
					$RetVal .= "<EntityId>" . $EntityId . "</EntityId>";
					$RetVal .= "<CampaignId></CampaignId>";
					$RetVal .= "<MsgType>General</MsgType>";
					$RetVal .= "<AcquireCust>No</AcquireCust>";
					$RetVal .= "<AuthKey>743A8F1B-C33F-48DC-9B3E-93BA4DD2E280</AuthKey>";
					$RetVal .= "<Text><![CDATA[".$OfferSMSTxt."]]></Text>";
					$RetVal .= "<FollowTemplate>True</FollowTemplate>";
					$RetVal .= "<EmailText><![CDATA[".$OfferEmailTxt."]]></EmailText>";
					$RetVal .= "<EmailSubject><![CDATA[".$OfferSubject."]]></EmailSubject>";
					//$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";
					
					if($custrow['Cell_Number'] == "")
					{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";					
					}
					else
					{
$RetVal .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";					
					}
					
					$RetVal .= "</Message>";			
				}
		}		
		$objDOM = null;
					
		return $RetVal;						
}

function ResolveTags($Message, $Row)
{
	$Message = str_replace("[[CustomerName]]",$Row["CustomerName"],$Message);
	$Message = str_replace("[[CustomerEmail]]",$Row["CustomerEmail"],$Message);	
	$Message = str_replace("[[RequestDate]]",$Row["RequestDate"],$Message);
	$Message = str_replace("[[RequestTime]]",$Row["RequestTime"],$Message);
	$Message = str_replace("[[NoOfPeople]]",$Row["NoOfPeople"],$Message);
	$Message = str_replace("[[OutletName]]",$Row["OutletName"],$Message);
	$Message = str_replace("[[CountryCallingCode]]",$Row["CountryCallingCode"],$Message);			
	$Message = str_replace("[[Cell_Number]]",$Row["Cell_Number"],$Message);		
	
	return $Message;
}

function AmolSetBookTable($input)
{
		/* Parse xml */
		//$Otid = $xml->Otid;
		//$EntityId = $xml->EntityId;
		//$AccountId = $xml->AccountId;
		//$CustomerId = $xml->CustomerId;
		//$CustomerName = str_replace("'","''",trim($xml->CustomerName));
		
		/* Parse xml */
		
		//echo $input;
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
		$RequestNote = str_replace("'","''",trim($inputDecode->{'reqnt'}));
		$Source = $inputDecode->{'source'};
		$Gender = $inputDecode->{'gen'};
		$SeatingPreferenceIDs = $inputDecode->{'spref'};	//may be empty in new case
		$TableIDs = $inputDecode->{'tabid'};
		$TableNos = $inputDecode->{'tabno'};
		
		$DeviceDateTime = $inputDecode->{'sysdt'};
	
			
		
		$aResults ='';
		$ConnLink ='';
		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
			//booking date and time
			$cdate = new DateTime($BookingDate);
			$cresult = $cdate->format('Y-m-d');			
			$BookingDateTime = $cresult . ' ' . $BookingTime ; 
						
			//$SysDatetime = String.Format("{0:g}", DateTime.Now);
			//$SysDatetime = date("m/d/y H:i:s");
						
			$DeviceDateTime = new DateTime($DeviceDateTime);
			$SysDatetime = $DeviceDateTime->format('Y-m-d H:i:s');		
		
			//$SysDatetime = date("Y-m-d H:i:s", time()); 	// local device time


			
$sql = "CALL SetBookTable('".$BookingMode."','".$SysDatetime."','".$BookingDateTime."','".$ConciergeBookingId."'
,'".$ConciergeSettingId."','".$CustomerId."','".$NoOfPeople."','".$CallingCode."','".$CustomerNumber."','".$CustomerEmail."','".$CustomerName."'
,'".$RequestNote."','".$cresult."','".$BookingTime."','".$BookingType."','".$Source."','".$Gender."','".$SeatingPreferenceIDs."'
,'".$TableIDs."','".$TableNos."');";
										
			//echo $sql;
			$result = mysql_query($sql) or die(mysql_error());  
			
		
			if (mysql_affected_rows()<=0) {
				
			
				$aResults[] = array("Success"=>0);
			
			}
			else{
					if(mysql_num_rows($result) >0)
					{

						$mode=''; $CId = '';$Cust_CreatedOn='';$Cust_ModifiedOn=''; $AccountId='';$ConciergeSettingId ='';	$ConciergeBookingId =''; $CustomerId='';	$Name='';
						$Gender=''; $CountryCallingCode=''; $Cell_Number=''; $EmailId=''; $PAX=''; $BookingDate=''; $BookingTime='';  $BookingUTCDateTime='';
						$SeatingPreferenceIDs=''; $TableIDs=''; $TableNos=''; $BookingType=''; $Note=''; $CheckedIn=''; $BookingStatus=''; $BookingSource='';
						$CreatedOn=''; $ModifiedOn=''; $ActiveCheckins=''; $ActivePax=''; $MaxTurnAround=''; $ApproxTurnAroundTime='';
						$ApproxTurnAroundUTCTime='';	
												
						while($row = mysql_fetch_array($result)){
							
							$mode = $row['pmode'];						
							$CId = $row['CId'];
							$Cust_CreatedOn=$row['Cust_CreatedOn'];
							$Cust_ModifiedOn=$row['Cust_ModifiedOn'];
							$AccountId = $row['AccountId'];
							$ConciergeSettingId =$row['ConciergeSettingId'];
							$ConciergeBookingId =$row['ConciergeBookingId'];
							$CustomerId=$row['CustomerId'];
							$Name=$row['Name'];
							$Gender=$row['Gender'];
							$CountryCallingCode=$row['CountryCallingCode'];
							$Cell_Number=$row['Cell_Number'];
							$EmailId=$row['EmailId'];
							$PAX=$row['PAX'];
							$BookingDate=$row['BookingDate'];
							$BookingTime=$row['BookingTime'];
							$BookingUTCDateTime=$row['BookingUTCDateTime'];
							$SeatingPreferenceIDs=$row['SeatingPreferenceIDs'];
							$TableIDs=$row['TableIDs'];
							$TableNos=$row['TableNos'];
							$BookingType=$row['BookingType'];
							$Note=$row['Note'];
							$CheckedIn=$row['CheckedIn'];
							$BookingStatus=$row['BookingStatus'];
							$BookingSource=$row['BookingSource'];
							$CreatedOn=$row['CreatedOn'];
							$ModifiedOn=$row['ModifiedOn'];		
							$ActiveCheckins=$row['ActiveCheckins'];		
							$ActivePax=$row['ActivePax'];	
							$MaxTurnAround=$row['MaxTurnAround'];	
							$ApproxTurnAroundTime=$row['ApproxTurnAroundTime'];	
							$ApproxTurnAroundUTCTime=$row['ApproxTurnAroundUTCTime'];					
						}
						
				
						#Call asmx method

						#send same data to sql server
						//set_time_limit(0);
						//$konektUrl = "http://192.168.1.168/KonektSolution/Service/KonektConci.asmx/";

						//$konektUrl .= "ConciergeTableBookingHandler";

$data = "Data={'mode':'".$mode."','CId':".$CId.",'Cust_CreatedOn':'".$Cust_CreatedOn."','Cust_ModifiedOn':'".$Cust_ModifiedOn."'
,'acid':'".$AccountId."'
,'csid':".$ConciergeSettingId.",'cbid':".$ConciergeBookingId.",'custid':'".$CustomerId."','nm':'".$Name."','gen':'".$Gender."'
,'cocode':'".$CountryCallingCode."','cellno':'".$Cell_Number."','eid':'".$EmailId."','px':".$PAX.",'bdt':'".$BookingDate."'
,'bt':'".$BookingTime."','btutc':'".$BookingUTCDateTime."','spref':'".$SeatingPreferenceIDs."','tabid':'".$TableIDs."'
,'tabno':'".$TableNos."','btp':'".$BookingType."','reqnt':'".$Note."','chkStat':'".$CheckedIn."','bstat':'".$BookingStatus."'
,'bs':'".$BookingSource."','credt':'".$CreatedOn."','moddt':'".$ModifiedOn."','mxtrnmin':'".$MaxTurnAround."'
,'apptrntim':'".$ApproxTurnAroundTime."','apptrntimutc':'".$ApproxTurnAroundUTCTime."'}";

						$retVal = '';
																					
						//echo $data . '<br/>';
							$aResults[] = array("Success"=>1); // Table release done on both servers
																				
																	
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
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

function GetTablesToRelease($input)
{
	//get requestids with multiple tables	GetReleaseTable
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};		
		$BookingDate = $inputDecode->{'bdt'};
		
		$aResults ='';
		$ConnLink ='';
				
				
		try
		{
			if($ConciergeSettingId !='')
			{
					$ConnLink = ConnectToMySQL();
					mysql_select_db("konektconci", $ConnLink);
				
					$BookingDate = new DateTime($BookingDate);
					$cresult = $BookingDate->format('Y-m-d');	
					
					$SQL = "CALL GetReleaseTable(".$ConciergeSettingId.",'".$cresult."');"; 			
						//echo $SQL;																	
					$result = mysql_query($SQL) or die(mysql_error());  
				
					if (mysql_affected_rows()<=0) {					
						$aResults[] = array("Success"=>0);
					}
					else{		
							$reltbls = '';
							#echo mysql_num_rows($result);
							if(mysql_num_rows($result) >0)
							{
								while($row = mysql_fetch_array($result)){
					
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
								$aResults[] = array("Success"=>1, "RelTbls" =>$reltbls);
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
			#$ConnLink = ConnectToMySQL();
			#mysql_select_db("konektconci", $ConnLink);

			$ConnLink = ConnectToMySQLi();
			mysqli_select_db($ConnLink,"konektconci");	
			
			$SQL = "CALL SetReleaseTable(".$ConciergeSettingId.",'".$ConciergeBookingId."','".$cresult."');"; 			
																				
			#$result = mysql_query($SQL) or die(mysql_error());  
			$result = mysqli_query($ConnLink,$SQL) or die(mysqli_error($ConnLink)); 		
		
			//if (mysql_affected_rows()<=0) {					
			if (mysqli_affected_rows($ConnLink)<=0) {				
				$aResults[] = array("Success"=>0);
			}
			else{	
					//if(mysql_num_rows($result) >0)
					if(mysqli_num_rows($result) >0)					
					{
						$UpdateStatus ='';
						#$CheckOutDeviceTime='';
						#$CheckOutUTCDateTime='';
						#while($row = mysql_fetch_array($result)){
						
						$row = mysqli_fetch_assoc($result);
						
							$UpdateStatus = $row['ChkOtStatus'];
							#$CheckOutDeviceTime=$row['DeviceTime'];
							#$CheckOutUTCDateTime=$row['CheckOutUTCDateTime'];
							#$ModifiedTime=$row['ModifiedOn'];									
						#}

						mysqli_free_result($result);
						mysqli_next_result($ConnLink); 
						
						if($UpdateStatus =='1')
						{

							$sql = "CALL GetManagerDetails(".$ConciergeSettingId.");";

							#$OuletDetails = mysql_query($sql) or die(mysql_error());  
							#$outrow =  mysql_fetch_array($OuletDetails);
							
							$OuletDetails = mysqli_query($ConnLink,$sql) or die(mysqli_error($ConnLink));  
							$outrow =  mysqli_fetch_assoc($OuletDetails);
							
							
							mysqli_free_result($OuletDetails);
							mysqli_next_result($ConnLink);	

							$ReturnResult = GenerateResponse($row,$outrow,'Checkout');	


							# Based on type send message.
							
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
						else
						{
							$aResults[] = array("Success"=>0);	//release failed on mysql.
						}												
					}
					else
					{
						$aResults[] = array("Success"=>0); //no record exist.
					}										
			}					
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1); // some error occoured
		}

		//mysql_close($ConnLink);
		mysqli_close($ConnLink);		
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
			//$ConnLink = ConnectToMySQL();
			//mysql_select_db("konektconci", $ConnLink);
			
			$ConnLink = ConnectToMySQLi();
			mysqli_select_db($ConnLink,"konektconci");			
				
			$SQL = "CALL SetCancelTable(".$ConciergeSettingId.",'".$ConciergeBookingId."');"; 			
																				
			//$result = mysql_query($SQL) or die(mysql_error());  
			
			$result = mysqli_query($ConnLink,$SQL) or die(mysqli_error($ConnLink));  		
		
			//if (mysql_affected_rows()<=0) {	
			if (mysqli_affected_rows($ConnLink)<=0) {	
			
				$aResults[] = array("Success"=>0);
			}
			else{	
					//if(mysql_num_rows($result) >0)
					if(mysqli_num_rows($result) >0)
					{
						$UpdateStatus ='';
						#$CheckOutDeviceTime='';
						#$CheckOutUTCDateTime='';
						
						//while($row = mysql_fetch_array($result)){
							
							$row = mysqli_fetch_assoc($result);
						
							$UpdateStatus = $row['ChkCanStatus'];
							#$CheckOutDeviceTime=$row['DeviceTime'];
							#$CheckOutUTCDateTime=$row['CheckOutUTCDateTime'];	
							#$ModifiedTime=$row['ModifiedOn'];							
						//}
						
						
						mysqli_free_result($result);
						mysqli_next_result($ConnLink); 
						
						if($UpdateStatus =='1')
						{
						$sql = "CALL GetManagerDetails(".$ConciergeSettingId.");";

						#$OuletDetails = mysql_query($sql) or die(mysql_error());  
						#$outrow =  mysql_fetch_array($OuletDetails);
						
						$OuletDetails = mysqli_query($ConnLink,$sql) or die(mysqli_error($ConnLink));  
						$outrow =  mysqli_fetch_assoc($OuletDetails);
						
						
						mysqli_free_result($OuletDetails);
						mysqli_next_result($ConnLink);	

						$ReturnResult = GenerateResponse($row,$outrow,'Cancel');	


						# Based on type send message.
						
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

								#Call asmx method

								#send same data to sql server
						#		set_time_limit(0);
						#		$konektUrl = "http://192.168.1.168/KonektSolution/Service/KonektConci.asmx/";

						#		$konektUrl .= "CancelBooking";

								/* Calling  web service using SOAP 	
								$client = new SoapClient($konektUrl);
								$params = array('Data'=>$input) ;
								$result = $client->UpdateCheckInOutMessage($params);
								print_r( $result);*/

						#		$data = "Data={'chksts':'".$UpdateStatus."','csid':".$ConciergeSettingId.",'cbid':".$ConciergeBookingId.",'chkcanmodtime':'".$ModifiedTime."'}";

						#		$ch = curl_init();

								//Set the URL
						#		curl_setopt($ch, CURLOPT_URL, $konektUrl);
						#		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

								//Enable POST data
						#		curl_setopt($ch, CURLOPT_POST, true);
								//Use the $pData array as the POST data
						#		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

								//curl_exec automatically writes the data returned
						#		$retVal=curl_exec($ch);
								
								// close cURL resource, and free up system resources
						#		curl_close($ch); 
																							
						#		if($retVal)
						#		{									
									$aResults[] = array("Success"=>1); // Table release done on both servers
									//echo 'release done on both servers<br/>'; 
						#		}	
						#		else
						#		{
						#			$aResults[] = array("Success"=>0); //release failed on sql server.									
						#		}														
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
				}					
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1); // some error occoured
		}

		//mysql_close($ConnLink);			
		mysqli_close($ConnLink);
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
						
		try
		{
			if($ConciergeSettingId !='')
			{
					$ConnLink = ConnectToMySQL();
					mysql_select_db("konektconci", $ConnLink);
				
					//$BookingDate = new DateTime($BookingDate);
					//$cresult = $BookingDate->format('Y-m-d');	
					
					$SQL = "CALL GetSeatingPreferences(".$ConciergeSettingId.");"; 			
																							
					$result = mysql_query($SQL) or die(mysql_error());  
				
					if (mysql_affected_rows()<=0) {					
						$aResults[] = array("Success"=>0);
					}
					else{		
							$prefdtls = '';
							#echo mysql_num_rows($result);
							if(mysql_num_rows($result) >0)
							{
								while($row = mysql_fetch_array($result)){
					
									$prefdtls[] = array("StPrefId"=>$row['ConciergeSeatingPreferenceId']
													, "Pref"=>$row['Preference'] 											
												);													
								}						
								$aResults[] = array("Success"=>1, "PrefDtls" =>$prefdtls);
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


function GetPreferencebasedTables($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};
		$Preferences = $inputDecode->{'prefs'};
		$BookingDate = $inputDecode->{'bdt'};
		$BookingTime = $inputDecode->{'bt'};
		
		$aResults ='';
		$ConnLink ='';$cresult='';
		
		
		#$CheckinDeviceDateTime = new DateTime($CheckinDeviceDateTime);
		
		#$cresult = $CheckinDeviceDateTime->format('Y-m-d H:i:s');	
		
		try
		{
			if($ConciergeSettingId !='')
			{
					$ConnLink = ConnectToMySQL();
					mysql_select_db("konektconci", $ConnLink);
				
					$BookingDate = new DateTime($BookingDate);
					$cresult = $BookingDate->format('Y-m-d');	
					
					$SQL = "CALL GetPreferencewiseTables(".$ConciergeSettingId.",'".$cresult."','".$BookingTime."','".$Preferences."');"; 			
						//echo $SQL;																
					$result = mysql_query($SQL) or die(mysql_error());  
				
					if (mysql_affected_rows()<=0) {					
						$aResults[] = array("Success"=>0);
					}
					else{		
							$preftbldtls = '';
							#echo mysql_num_rows($result);
							if(mysql_num_rows($result) >0)
							{
								while($row = mysql_fetch_array($result)){
											
									$preftbldtls[] = array(
													"tblno"=>$row['TableNo']
													,"ias"=>$row['IsAvailableStatus'] 
													,"tnm"=>$row['TableName']
													,"scap"=>$row['SeatingCapacity'] 
													,"mcap"=>$row['MaxCapacity'] 											
												);													
								}						
								$aResults[] = array("Success"=>1, "PrefTblDtls" =>$preftbldtls);
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

function GetBookingNotification($input)
{
		$inputDecode =json_decode($input);
		
		$ConciergeSettingId = $inputDecode->{'csid'};		
		$SysDateTime = $inputDecode->{'sysdt'};
		
		
		$SysDateTime = new DateTime($SysDateTime);
		$cresult = $SysDateTime->format('Y-m-d H:i:s');	
					
		$aResults ='';
		$ConnLink ='';
		
		try
		{
			$ConnLink = ConnectToMySQL();
			mysql_select_db("konektconci", $ConnLink);
		
						
			$SQL = "CALL GetBookingNotification(".$ConciergeSettingId.",'".$cresult."');"; 			
									
			$result = mysql_query($SQL) or die(mysql_error());  
			$lastTime=$SysDateTime;
			if (mysql_affected_rows()<=0) {					
				$aResults[] = array("Success"=>0,"LastTimeExecutedOn"=>$lastTime);
			}
			else{		
					$bdtls = '';
					#echo mysql_num_rows($result);
					if(mysql_num_rows($result) >0)
					{	
						$i=0;
						
						while($row = mysql_fetch_array($result)){
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
						$aResults[] = array("Success"=>1,"LastTimeExecutedOn"=>$lastTime,"NCnt"=>(string)mysql_num_rows($result), "BDtls" =>$bdtls);
					}
					else
					{
						$aResults[] = array("Success"=>0,"LastTimeExecutedOn"=>$lastTime);
					}										
			}					
		}
		catch(Exception $e){
			$aResults[] = array("Success"=>-1,"LastTimeExecutedOn"=>$lastTime);
		}

		mysql_close($ConnLink);			
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
						
		try
		{
			if($EntityId !='')
			{
					$ConnLink = ConnectToMySQL();
					mysql_select_db("konektconci", $ConnLink);
				
					//$BookingDate = new DateTime($BookingDate);
					//$cresult = $BookingDate->format('Y-m-d');	
					
					$SQL = "CALL GetCoverStats('".$EntityId."');"; 			
																							
					$result = mysql_query($SQL) or die(mysql_error());  
				
					if (mysql_affected_rows()<=0) {					
						$aResults[] = array("Success"=>0);
					}
					else{		
							$coverstats = '';
							#echo mysql_num_rows($result);
							if(mysql_num_rows($result) >0)
							{
								while($row = mysql_fetch_array($result)){
					
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
								$aResults[] = array("Success"=>1, "CoverStats" =>$coverstats);
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

		try
		{
		
					$ConnLink = ConnectToMySQL();
					mysql_select_db("konektconci", $ConnLink);
				
					$BookingDate = new DateTime($BookingDate);
					$cresult = $BookingDate->format('Y-m-d H:i:s');	
					
					$SQL = "CALL GetBookingbasedOffers(".$ConciergeSettingId.",'".$cresult."',".$Paxs.",'".$Source."');"; 			
																							
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
								$aResults[] = array("Success"=>1, "OffDtls" =>$offdtls);
							}
							else
							{
								$aResults[] = array("Success"=>0);
							}										
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

  
  
  $aResults ='';
  $ConnLink ='';  
  try
  {
   $ConnLink = ConnectToMySQL();
   mysql_select_db("konektconci", $ConnLink);
      
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
   '".$DinnerEnd."','".$SmsSenderName."','".$EmailSenderName."','".$EmailSenderEmailId."','".$City."','".$Area."');";
          
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

