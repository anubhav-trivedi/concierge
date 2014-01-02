<?php

	include("conciergemessagehelper.php");

	$user="";
	$pass="";
	$input="";
	$entityId="";
	$concSettingId="";
	$concTBSettingId="";
	$type="";

	$aResults ='';

	//'d' : '{"u": "coromum","p":"coromum"}'

	if(ISSET($_REQUEST['d']))
		$input = $_REQUEST['d'];

	if(ISSET($_REQUEST['tp']))
		$type = $_REQUEST['tp'];

	if($input=="" || $type=="")
	{
		$aResults[] =array("Success"=>0, "Reason" =>"Insufficient Parameters");
		//echo '{"Success":0,"Reason":"Insufficient Parameters"}';
		$json_response = json_encode($aResults);
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;
		return;
	}

	$inputDecode =json_decode($input,true); // setting true in json_decode returns associative array


	if($type=="CONAPPBK") {

		$ConnLink ='';
		$CorruptAppIds ='';
		$ABkId = '';
		$CBkId = '';
		$CstId = '';


		//for ($i = 0; $i < count($cardArray); $i++) {}
		foreach($inputDecode as $objArr )
		{
				$ConciergeBookingId  = $objArr["ConciergeBookingId"];
				$AppBookingId  = $objArr["AppBookingId"];
				$ConciergeSettingId  = $objArr["ConciergeSettingId"];
				$Name  =  str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["Name"])),null,'UTF-8') ));
				$Gender  = $objArr["Gender"];
				$CountryCallingCode  = $objArr["CountryCallingCode"];
				$Cell_Number  = $objArr["Cell_Number"];
				$EmailId  = $objArr["EmailId"];
				$PAX  = $objArr["PAX"];
				$BookingDate  = $objArr["BookingDate"];
				$BookingTime  = $objArr["BookingTime"];
				$BookingUTCDateTime  = $objArr["BookingUTCDateTime"];
				$SeatingPreferenceIDs  = $objArr["SeatingPreferenceIDs"];
				$TableIDs  = $objArr["TableIDs"];
				$TableNos  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["TableNos"])),null,'UTF-8') ));
				$BookingType  = $objArr["BookingType"];
				$Note  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["Note"])),null,'UTF-8') ));
				$CheckedIn  = $objArr["CheckedIn"];
				$BookingStatus  = $objArr["BookingStatus"];
				$CheckInTime  = $objArr["CheckInTime"];
				$CheckInUTCDateTime  = $objArr["CheckInUTCDateTime"];
				$CheckOutTime  = $objArr["CheckOutTime"];
				$CheckOutUTCDateTime  = $objArr["CheckOutUTCDateTime"];
				$BookingSource  = $objArr["BookingSource"];
				$CreatedOn  = $objArr["CreatedOn"];
				$CustomerId  = $objArr["CustomerId"];
				$ModifiedOn  = $objArr["ModifiedOn"];
				$MaxTurnAround  = $objArr["MaxTurnAround"];
				$ApproxTurnAroundTime  = $objArr["ApproxTurnAroundTime"];
				$ApproxTurnAroundUTCTime= $objArr["ApproxTurnAroundUTCTime"];

				//$ConnLink = ConnectToMySQL();
				//mysql_select_db("dummy_konektconci", $ConnLink);

				$ConnLink = ConnectToMssql();

				$SQL = '';
				try
				{
					$SQL = "EXEC spConciergeAppSetAppBookings
					'". $ConciergeBookingId ."','". $AppBookingId ."','". $ConciergeSettingId ."',
					'". $Name ."','". $Gender ."','". $CountryCallingCode ."','". $Cell_Number ."',
					'". $EmailId ."','". $PAX ."','". $BookingDate ."','". $BookingTime ."',
					'". $BookingUTCDateTime ."','". $SeatingPreferenceIDs ."',
					'". $TableIDs ."','". $TableNos ."','". $BookingType ."','". $Note ."',
					'". $CheckedIn ."','". $BookingStatus ."','". $CheckInTime ."','". $CheckInUTCDateTime ."',
					'". $CheckOutTime ."','". $CheckOutUTCDateTime ."','". $BookingSource ."','". $CustomerId ."',
					'". $CreatedOn ."','". $ModifiedOn ."','". $MaxTurnAround ."','". $ApproxTurnAroundTime ."','". $ApproxTurnAroundUTCTime ."' ";

/*$SQL = "EXEC spConciergeAppSetAppBookings ? , ? , ? ,? , ? , ? , ? ,? , ? , ?, ?,?, ?,?,?,? ,?,?, ?, ?, ?,? ,?, ?, ?,? , ? , ?,? , ? ";

$params = array($ConciergeBookingId,$AppBookingId,$ConciergeSettingId,$Name,$Gender,$CountryCallingCode,$Cell_Number,$EmailId
,$PAX , $BookingDate , $BookingTime , $BookingUTCDateTime , $SeatingPreferenceIDs , $TableIDs , $TableNos , $BookingType , $Note , $CheckedIn , $BookingStatus , $CheckInTime ,
$CheckInUTCDateTime , $CheckOutTime , $CheckOutUTCDateTime , $BookingSource , $CustomerId , $CreatedOn , $ModifiedOn , $MaxTurnAround , $ApproxTurnAroundTime , $ApproxTurnAroundUTCTime); */

					$result =odbc_exec($ConnLink,$SQL) ;


					if(!$result)
					{
						 throw new Exception(odbc_error($ConnLink));
					}

					$row = odbc_fetch_array($result);

					//$ABkId = $row["AppBookingId"];

					if($ABkId=='')
					{
						$ABkId = $row["AppBookingId"];
					}
					else
					{
						$ABkId .= ','. $row["AppBookingId"];
					}

					//$CBkId = $row["ConciergeBookingId"];
					if($CBkId=='')
					{
						$CBkId = $row["ConciergeBookingId"];
					}
					else
					{
						$CBkId .= ','. $row["ConciergeBookingId"];
					}

					//$CstId = $row["CustomerId"];
					if($CstId=='')
					{
						$CstId = $row["CustomerId"];
					}
					else
					{
						$CstId .= ','. $row["CustomerId"];
					}

				/*$file = 'postconsync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle,  "\r\n". "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, "return value is : ". $ABkId . ",". $CBkId .",".$CstId ."\r\n" . $logTime);
				fwrite($handle,  "\r\n"."--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle);*/

				}
				catch(Exception $e)
				{

					if($CorruptAppIds=='')
					{
						$CorruptAppIds = $AppBookingId;
					}
					else
					{
						$CorruptAppIds .= ','. $AppBookingId;
					}
						$file = 'postconsync.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------");
						fwrite($handle,"\r\n");
						fwrite($handle, 'Error message is : ' . $e->__toString(). " "   ."\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
						fclose($handle);


					LogErr($e->getMessage());
				}

				odbc_close($ConnLink);
		}



		if($CorruptAppIds=='')
		{
			$aResults[] = array("Success"=>"1", "abid"=>$ABkId,"cbid"=>$CBkId, "cstid"=>$CstId );
		}
		else
		{
			$aResults[] = array("Success"=>"0", "corrpid" =>$CorruptAppIds);

				$file = 'postconsync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle,  "\r\n". "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, "Corrupt App Ids are : ". $CorruptAppIds ."\r\n" . $logTime);
				fwrite($handle,  "\r\n"."--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle);
		}


	}
	else if($type=="CONAPPOFFERLG") {

		$ConnLink ='';
		$CorruptAppIds ='';

		foreach($inputDecode as $objArr )
		{
				$AppBookingId  = $objArr["AppBookingId"];
				$OfferId  = $objArr["OfferId"];
				$EntityId = $objArr["EntityId"];
				$CSId  = $objArr["CSId"];
				$CustomerId =  $objArr["CustomerId"];
				$CreatedOn =  $objArr["CreatedOn"];


				//$ConnLink = ConnectToMySQL();
				//mysql_select_db("dummy_konektconci", $ConnLink);

				$ConnLink = ConnectToMssql();

				$SQL = '';
				try
				{
$SQL = "EXEC spConciergeAppSyncSetAppOfferLog('". $AppBookingId ."','". $OfferId ."','". $EntityId ."','". $CSId ."','". $CustomerId ."','". $CreatedOn ."');";

					//Some fields not in SQL SERVER
					//$SQL = "EXEC spConciergeAppSyncSetAppOfferLog ?,?,?,?,?,? ";
					//$params = array($AppBookingId,$OfferId,$EntityId,$CSId,$CustomerId,$CreatedOn);

					$result = odbc_exec($ConnLink,$SQL);

					if(!$result)
					{
						throw new Exception(odbc_error($ConnLink));
					}
				}
				catch(Exception $e)
				{

					if($CorruptAppIds=='')
					{
						$CorruptAppIds = $AppBookingId;
					}
					else
					{
						$CorruptAppIds .= ','. $AppBookingId;
					}
						$file = 'postconsync.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------");
						fwrite($handle,"\r\n");
						fwrite($handle, 'Error message is : ' . $e->getMessage() . " "   ."\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
						fclose($handle);

					LogErr($e->getMessage());
				}

				odbc_close($ConnLink);

		}

		if($CorruptAppIds=='')
		{
			$aResults[] = array("Success"=>"1");
		}
		else
		{
			$aResults[] = array("Success"=>"0", "corrpid" =>$CorruptAppIds);

				$file = 'postconsync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle,  "\r\n". "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, "Corrupt App Ids are : ". $CorruptAppIds ."\r\n" . $logTime);
				fwrite($handle,  "\r\n"."--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle);
		}

	}
	else if($type=="CONAPPCUST") {

		$ConnLink ='';
		//$CorruptAppIds ='';

		/*$file = 'postconsync.txt';
		$handle = fopen($file, 'a');
		$logTime = new DateTime();
		$logTime= $logTime->format('Y-m-d H:i:s');
		fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
		fwrite($handle,"\r\n");
		fwrite($handle, "type is  : ". "\r\n".  $type . "\r\n" . $logTime);
		fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
		fclose($handle);	*/

		foreach($inputDecode as $objArr )
		{
				//Some fields not in SQL SERVER
				//$CId  = $objArr["CId"];
				$CustomerId  = $objArr["CustomerId"];
				$AccountId  = $objArr["AccountId"];
				$CustomerName  =  str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["CustomerName"])),null,'UTF-8') ));
				$CountryCallingCode  = $objArr["CountryCallingCode"];
				$Cell_Number  = $objArr["Cell_Number"];
				$EmailId  = $objArr["EmailId"];
				$City  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["City"])),null,'UTF-8') ));
				$Zip  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["Zip"])),null,'UTF-8') ));
				$Address  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["Address"])),null,'UTF-8') ));
				$DOB  = $objArr["DOB"];
				$Annv  = $objArr["Annv"];
				$CreatedOn  = $objArr["CreatedOn"];
				$ModifiedOn  = $objArr["ModifiedOn"];
				$LastVisitedOn  = $objArr["LastVisitedOn"];
				$Gender  = $objArr["Gender"];
				$LastComment  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["LastComment"])),null,'UTF-8') ));
				$LastRating  = $objArr["LastRating"];
				$Tags  = str_replace("'","''",trim(html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($objArr["Tags"])),null,'UTF-8') ));


				$ConnLink = ConnectToMssql();

				$SQL = '';
				try
				{
$SQL = "EXEC spConciergeAppSetAppCustomers
'". $CustomerId ."','". $AccountId ."',
'". $CustomerName ."','". $CountryCallingCode ."','". $Cell_Number ."','". $EmailId ."',
'". $City ."','". $Zip ."','". $Address ."','". $DOB ."',
'". $Annv ."','". $CreatedOn ."',
'". $ModifiedOn ."','". $LastVisitedOn ."','". $Gender ."','". $LastComment ."','". $LastRating ."','". $Tags ."' ";


//$SQL = "EXEC spConciergeAppSetAppCustomers ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? ";

//$params = array($CustomerId, $AccountId ,$CustomerName, $CountryCallingCode, $Cell_Number, $EmailId ,$City, $Zip, $Address, $DOB , $Annv, $CreatedOn , $ModifiedOn, $LastVisitedOn, $Gender, $LastComment , $LastRating, $Tags) ;

					//$result = sqlsrv_query($ConnLink,$SQL,$params) ;
					$result = odbc_exec($ConnLink,$SQL);

					if(!$result)
					{
						 throw new Exception(odbc_error($ConnLink));
					}
				}
				catch(Exception $e)
				{
						$file = 'postconsync.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------");
						fwrite($handle,"\r\n");
						fwrite($handle, 'Error message is : ' . $e->getMessage() . " "   ."\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
						fclose($handle);

					LogErr($e->getMessage());
				}

				odbc_close($ConnLink);

		}

		$aResults[] = array("Success"=>"1");
		/*if($CorruptAppIds=='')
		{
			$aResults[] = array("s"=>"1");
		}
		else
		{
			$aResults[] = array("s"=>"0", "corrpid" =>$CorruptAppIds);

				$file = 'postconsync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle,  "\r\n". "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, "Corrupt App Ids are : ". $CorruptAppIds ."\r\n" . $logTime);
				fwrite($handle,  "\r\n"."--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle);
		} */



	}

	else if($type=="CONAPPOFLG") {

		$ConnLink ='';
		$CorruptAppIds ='';

		foreach($inputDecode as $objArr )
		{
				$AppBookingId  = $objArr["AppBookingId"];
				$OfferId  = $objArr["OfferId"];
				$EntityId = $objArr["EntityId"];
				$CSId  = $objArr["CSId"];
				$Mode =  $objArr["Mode"];

				if($Mode == "Book" && $OfferId !="") //Book, Amend,Amendep, Cancel, Checkin, Checkout
				{
					//make entry in offer log
					//$ConnLink = ConnectToMySQL();
					//mysql_select_db("konektconci", $ConnLink);

					$ConnLink = ConnectToMssql();

					$SQL = '';
					try
					{
						$SQL = "EXEC spConciergeAppSetAppOfferLog '". $AppBookingId ."','". $OfferId ."','". $EntityId ."','". $CSId ."' ";

						//$SQL = "EXEC spConciergeAppSetAppOfferLog ?,?,?,? ";
						//$params = array($AppBookingId , $OfferId ,$EntityId ,$CSId) ;

						$result = odbc_exec($ConnLink,$SQL);

						if(!$result)
						{
							  throw new Exception(odbc_error($ConnLink));
						}
					}
					catch(Exception $e)
					{
						$file = 'postconsync.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------");
						fwrite($handle,"\r\n");
						fwrite($handle, 'Error message is : ' . $e->getMessage(). " "   ."\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
						fclose($handle);

						LogErr($e->getMessage());
					}

					odbc_close($ConnLink);
				}

				//Build Message
				//$ConnLink = ConnectToMySQLi();
				//mysqli_select_db($ConnLink,"konektconci");

				$ConnLink = ConnectToMssql();

				$SQL = '';
				try
				{
					$SQL = "EXEC spConciergeAppGetAppBookingData '". $AppBookingId ."','". $OfferId ."','". $EntityId ."','". $CSId ."' ";

					//$SQL = "EXEC spConciergeAppGetAppBookingData ?,?,?,? ";
					//$params = array($AppBookingId , $OfferId ,$EntityId ,$CSId) ;
					//$result = sqlsrv_query($ConnLink,$SQL,$params) ;
					$result = odbc_exec($ConnLink,$SQL);

					if(!$result)
					{
						 throw new Exception(odbc_error($ConnLink));
					}

					/*if (mysqli_affected_rows($ConnLink)<=0) {

						$aResults[] = array("Success"=>0);
					}
					else{*/
							if(odbc_num_rows($result))
							{
								$ReturnResult = '';
								$Response = '';
								$BookingRow = '';

								$BookingRow = odbc_fetch_array($result);

								odbc_free_result($result);

								//$sql = "CALL GetManagerDetails(".$CSId.");";
								//$OuletDetails = mysqli_query($ConnLink,$sql) or die(mysqli_error($ConnLink));
								//$OutRow =  mysqli_fetch_assoc($OuletDetails);
								//$sql = "EXEC spConciergeAppGetManagerDetails @pConciergeSettingId = ? ";

								$sql = "EXEC spConciergeAppGetManagerDetails ".$CSId;

								//$params = array($CSId);
								//$OuletDetails = sqlsrv_query($ConnLink,$sql,$params) ;

								$OuletDetails = odbc_exec($ConnLink,$sql);
								$OutRow = odbc_fetch_array($OuletDetails);

								odbc_free_result($OuletDetails);

								//SEND MESSAGE ONE BY ONE
								$ObjConciergeMessage = new ConciergeMessageSender();
								$ObjConciergeMessage->PostMessage($BookingRow,$OutRow,$Mode);

								$aResults[] = array("Success"=>"1"); // Table release done on both servers


							}
							else
							{
								$aResults[] = array("Success"=>"0"); //no record exist.
							}


					//}

				}
				catch(Exception $e)
				{
					$file = 'postconsync.txt';
					$handle = fopen($file, 'a');
					$logTime = new DateTime();
					$logTime= $logTime->format('Y-m-d H:i:s');
					fwrite($handle, "--------------------------------------------------------------------------------------------------");
					fwrite($handle,"\r\n");
					fwrite($handle, 'Error message is : ' . $e->getMessage(). " "   ."\r\n" . $logTime);
					fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
					fclose($handle);

					LogErr($e->getMessage());
				}

				odbc_close($ConnLink);


		}


	}


	$json_response = json_encode($aResults);
	# Optionally: Wrap the response in a callback function for JSONP cross-domain support
	if(ISSET($_REQUEST["callback"])) {
		$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
		}
	# Return the response
	echo $json_response;

	function ConnectToMySQL()
	{
		try {
			//$con = mysql_connect("192.168.1.52","conci","Mobikontech");
			$con = mysql_connect("localhost:3307","root","Marijuana@77");  //beta
			if (!$con)
			{
			  die('Could not connect: ' . mysql_error());
			}
		}
		catch (Exception $e) {
					LogErr($e->getMessage());
		}
		return $con;

	}

	function ConnectToMySQLi()
	{
		try {
			 //$mysqli = new mysqli("192.168.1.52","conci","Mobikontech");
			$mysqli =new mysqli("localhost:3307","root","Marijuana@77");	//beta
			if (mysqli_connect_errno())
			{
			  die('Could not connect: ' . mysqli_connect_error());
			}
		}
		catch (Exception $e) {
				LogErr($e->getMessage());
		}

		return $mysqli;
	}


function ConnectToMssql(){
		try {
			$dsn="Driver={SQL Server Native Client 10.0};Server=mobi-staging.cunbw6re0gf0.ap-southeast-1.rds.amazonaws.com;Database=KonektApp;";
			$username="konektrds";
			$password="L0g!nK0n3ktrd5!";
			$SqlLink=odbc_connect($dsn,$username,$password) or die ("could not connect");



			//$servername = "64.15.155.142";
			//$connectionOptions = array("Database"=>"KonektApp", "UID"=>$username, "PWD"=>$password);

			//$SqlLink = sqlsrv_connect($servername, $connectionOptions) or die("Connection to MS SQL could not be established.\n");

			//if( $SqlLink === false)
			//{
				//print_r(sqlsrv_errors());
				//throw new Exception(print_r( sqlsrv_errors(), true));
			//}

		}
		catch (Exception $e) {
				LogErr($e->getMessage());

				$file = 'postconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'con  '." ".$e ->getMessage(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);

		}
		  return $SqlLink;
	}

	function LogErr($msg)
{
	$date = date('Y-m-d h:i:s');

		$errFile = date('Ymd').".txt";
		$fh = fopen($errFile, 'a') or die("can't open file");
		fwrite($fh, "--------------------------------------------------------------------------------------------------");
		fwrite($fh,"\r\n");
		fwrite($fh,$date);
		fwrite($fh,"\r\n");
		fwrite($fh, $msg);
		fwrite($fh,"\r\n");
		fwrite($fh, "--------------------------------------------------------------------------------------------------");
		fwrite($fh,"\r\n");
		fclose($fh);
	}
?>