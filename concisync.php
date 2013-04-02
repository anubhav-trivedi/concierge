<?php

	//$ConnLink = ConnectToMySQL();	
	//mysql_select_db("konektconci", $ConnLink);	
	
	$SConnLink = ConnectToMySQLi();
	mysqli_select_db($SConnLink,"konektconci");		

	$sqlQuery = "SELECT ConciergeSettingId, EntityId, AccountId FROM conciergesettings;";
	
	//$entities = mysql_query($sqlQuery);
	$entities = mysqli_query($SConnLink,$sqlQuery) or die(mysqli_error($SConnLink)); 			
	
	//$acctId ='c473bc69-229d-4832-a599-60c7eedf50e5';
	//$eId ='651908e1-72d0-4260-81a2-1e498dd3cc96';
	
	try
	{
		if (mysqli_num_rows($entities)>0) 
		{
			while($drow = mysqli_fetch_assoc($entities))
			{
				$acctId=$drow['AccountId'];	
				$eId=$drow['EntityId'];	
					
				if($acctId != "" && $eId != "")
				{
					
					//mysqli_free_result($result);
					//mysqli_next_result($SConnLink); 	
				
					

	//if(ISSET($_REQUEST['acct']))
		//$acctId = $_REQUEST['acct'];
		
	//if(ISSET($_REQUEST['eid']))
		//$eId = $_REQUEST['eid'];

	//if($acctId=="" && $eId=="")
	//{
	//	echo '{"Success":"0","Reason":"Insufficient Parameters"}';
	//	return;
	//}
	
		
									
		$ConnLink = ConnectToMySQL();	
		mysql_select_db("konektconci", $ConnLink);	


		$sql = "SELECT `ConciergeAllOfferLogId`,`ConciergeOfferId`,`CreatedOn`,`CustomerId`,`EntityId`,IFNULL(`ConciergeBookingId`,'') AS ConciergeBookingId 
		FROM `conciergeallofferlog` 
		WHERE `IsSynced`='no' AND EntityId='".$eId."';";
							
						
		$result = mysql_query($sql);
	try {
		if (mysql_affected_rows()>0) 
		{
			$conciOLogId='';
			
			$SQLLink=ConnectToMssql();
			try {
				while($row = mysql_fetch_array($result))
				{
					if($conciOLogId=='')
						$conciOLogId=$row['ConciergeAllOfferLogId'];
					else
						$conciOLogId .=','.$row['ConciergeAllOfferLogId'];
						
					 $conciOfferLogId=$row['ConciergeAllOfferLogId'];
					 $conciOfferId=$row['ConciergeOfferId'];
					 $createdOn=$row['CreatedOn'];
					 $customerId=$row['CustomerId'];
					 $entityId=$row['EntityId'];
					 $conciBookingId=$row['ConciergeBookingId'];
					 
					 $updsql="SET NOCOUNT ON;
					 INSERT INTO [dbo].[ConciergeAllOfferLog] ([ConciergeAllOfferLogId],[EntityId],[CustomerId],[ConciergeOfferId],[CreatedOn],[SyncedOn],[ConciergeBookingId])
					VALUES (".$conciOfferLogId.",'".$entityId."','".$customerId."','".$conciOfferId."',CAST('".$createdOn."' AS DATETIME),GETUTCDATE()
					,CASE WHEN '".$conciBookingId."' = '' THEN NULL ELSE '".$conciBookingId."' END );";
					
					 
						/*$file = 'data.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------");
						fwrite($handle,"\r\n");
						fwrite($handle, $updsql ."\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fclose($handle);*/
				
				
					 $sqlresult=odbc_exec($SQLLink,$updsql) or die(odbc_error());
					   
				}
			}
			catch (Exception $e) {						
				LogErr($e->getMessage());	
			} 
			//finally {
				odbc_close($SQLLink);
			//}
			
			$sqlUpd = "UPDATE `conciergeallofferlog` SET `IsSynced`='yes'
			WHERE `IsSynced`='no' AND ConciergeAllOfferLogId IN (".$conciOLogId.");";

			$res = mysql_query($sqlUpd);
			
		}
		
		/*INSERT BOOKING DATA*/
		$sql = "SELECT CB.ApproxTurnAroundTime,CB.ApproxTurnAroundUTCTime,CB.BookingDate,CB.BookingSource,CB.BookingStatus,
		CB.BookingTime,CB.BookingType,CB.BookingUTCDateTime,CB.Cell_Number,CB.CheckedIn,CB.CheckInTime,CB.CheckInUTCDateTime,
		CB.CheckOutTime,CB.CheckOutUTCDateTime,CB.ConciergeBookingId,CB.ConciergeSettingId,CB.CountryCallingCode,CB.CreatedOn,
		CB.CustomerId,CB.EmailId,CB.Gender,CB.MaxTurnAround,CB.ModifiedOn,CB.Name,CB.Note,CB.PAX,CB.SeatingPreferenceIDs,CB.TableIDs,CB.TableNos 
		FROM `conciergebookings` CB  INNER JOIN `conciergesettings` CS ON CS.ConciergeSettingId=CB.ConciergeSettingId WHERE CB.IsSynced='no' AND EntityId='".$eId."';";

		
		$result = mysql_query($sql);

		if (mysql_affected_rows()>0) 
		{
			$conciBkId='';
			
			$SQLLink=ConnectToMssql();
			try {
				while($row = mysql_fetch_array($result))
				{

				
					if($conciBkId=='')
						$conciBkId=$row['ConciergeBookingId'];
					else
						$conciBkId .=','.$row['ConciergeBookingId'];
						
					 $ApproxTurnAroundTime=$row['ApproxTurnAroundTime'];
						$ApproxTurnAroundUTCTime=$row['ApproxTurnAroundUTCTime'];
						$BookingDate=$row['BookingDate'];
						$BookingSource=$row['BookingSource'];
						$BookingStatus=$row['BookingStatus'];
						$BookingTime=$row['BookingTime'];
						$BookingType=$row['BookingType'];
						$BookingUTCDateTime=$row['BookingUTCDateTime'];
						$Cell_Number=$row['Cell_Number'];
						$CheckedIn=$row['CheckedIn'];
						$CheckInTime=$row['CheckInTime'];
						$CheckInUTCDateTime=$row['CheckInUTCDateTime'];
						$CheckOutTime=$row['CheckOutTime'];
						$CheckOutUTCDateTime=$row['CheckOutUTCDateTime'];
						$ConciergeBookingId=$row['ConciergeBookingId'];
						$ConciergeSettingId=$row['ConciergeSettingId'];
						$CountryCallingCode=$row['CountryCallingCode'];
						$CreatedOn=$row['CreatedOn'];
						$CustomerId=$row['CustomerId'];
						$EmailId=$row['EmailId'];
						$Gender=$row['Gender'];
						$MaxTurnAround=$row['MaxTurnAround'];
						$ModifiedOn=$row['ModifiedOn'];
						$Name=$row['Name'];
						$Note=$row['Note'];
						$PAX=$row['PAX'];
						$SeatingPreferenceIDs=$row['SeatingPreferenceIDs'];
						$TableIDs=$row['TableIDs'];
						$TableNos=$row['TableNos'];
					 					 				
					 $updsql="SET NOCOUNT ON;
					INSERT INTO [dbo].[ConciergeBookings] ([ConciergeSettingId],[ConciergeBookingId],[Name],[Gender],[CountryCallingCode],[Cell_Number],[EmailId],[PAX]
					,[BookingDate],[BookingTime],[SeatingPreferenceIDs],[TableIDs],[TableNos],[BookingType],[Note],[CheckedIn],[BookingStatus],[CheckInTime]
					,[CheckOutTime],[BookingSource],[CreatedOn],[BookingUTCDateTime],[CustomerId],[CheckInUTCDateTime],[CheckOutUTCDateTime],[ModifiedOn]
					,[ApproxTurnAroundTime],[ApproxTurnAroundUTCTime],[MaxTurnAround],[SyncedOn])
					VALUES
					('".$ConciergeSettingId."','".$ConciergeBookingId."','".$Name."','".$Gender."','".$CountryCallingCode."','".$Cell_Number."','".$EmailId."','".$PAX."','".$BookingDate."','".$BookingTime
					."','".$SeatingPreferenceIDs."','".$TableIDs."','".$TableNos."','".$BookingType."','".$Note."','".$CheckedIn."','".$BookingStatus."','".$CheckInTime."','".$CheckOutTime."','".$BookingSource
					."','".$CreatedOn."','".$BookingUTCDateTime."','".$CustomerId."','".$CheckInUTCDateTime."','".$CheckOutUTCDateTime."','".$ModifiedOn."','".$ApproxTurnAroundTime."','".$ApproxTurnAroundUTCTime."','".$MaxTurnAround
					."',GETUTCDATE())";
					 
				
					 $sqlresult=odbc_exec($SQLLink,$updsql) or die(odbc_error());
					   
				}
			}
			catch (Exception $e) {												
				LogErr($e->getMessage());					
			} 
			//finally {
			odbc_close($SQLLink);
			//}
			
			$sqlUpd = "UPDATE `conciergebookings` SET `IsSynced`='yes'
			WHERE `IsSynced`='no' AND ConciergeBookingId IN (".$conciBkId.");";

			$res = mysql_query($sqlUpd);
			
		}
		
		
		/*INSERT CUSTOMER DATA*/
		$custSQL="SELECT `AccountId`,`Address`,`Annv`,`Cell_Number`,`CId`,`City`,`CountryCallingCode`,`CreatedOn`,
		`CustomerId`,`CustomerName`,`DOB`,`EmailId`,`Gender`,`LastComment`,`LastRating`,`LastVisitedOn`,
		`ModifiedOn`,`Tags`,`Zip`
		FROM `customers` WHERE `IsSynced`='No' AND AccountId='".$acctId."';";
		
		$result = mysql_query($custSQL);
		
		if (mysql_affected_rows()>0) 
		{
			$cId='';
			
			$SQLLink=ConnectToMssql();
			try {
				while($row = mysql_fetch_array($result))
				{
				try{
							if($cId=='')
								$cId=$row['CId'];
							else
								$cId .=','.$row['CId'];
								
								$AccountId=$row['AccountId'];
								$Address=$row['Address'];
								$Annv=$row['Annv'];
								$Cell_Number=$row['Cell_Number'];
								$CId=$row['CId'];
								$City=$row['City'];
								$CountryCallingCode=$row['CountryCallingCode'];
								$CreatedOn=$row['CreatedOn'];
								$CustomerId=$row['CustomerId'];
								$CustomerName=$row['CustomerName'];
								$DOB=$row['DOB'];
								$EmailId=$row['EmailId'];
								$Gender=$row['Gender'];
								$LastComment=$row['LastComment'];
								$LastRating=$row['LastRating'];
								$LastVisitedOn=$row['LastVisitedOn'];
								$ModifiedOn=$row['ModifiedOn'];
								$Tags=$row['Tags'];
								$Zip=$row['Zip'];
							 
							 $updsql="EXEC SPAcquireCustomer @AccountId='".$acctId."',@EntityId='".$eId."',@CustId='".$CustomerId."',@CountryCallingCode='".$CountryCallingCode."',@CellNumber='".$Cell_Number."',@Email='".$EmailId."',@Name='".$CustomerName."',@Address='".$Address."',@Annv='".$Annv."',@City='".$City."',@CreatedOn='".$CreatedOn."',@DOB='".$DOB."',@Gender='".$Gender."',
							 @Zip='".$Zip."',@Source='Concierge'";
							 //echo $updsql;
							 //echo '<br/><br/>';
							 $sqlresult=odbc_exec($SQLLink,$updsql) or die(odbc_error());
					  }
					  catch (Exception $e) {
							LogErr($e->getMessage());					
					} 
				}
			}
			catch (Exception $e) {
				LogErr($e->getMessage());					
			} 
			//finally {
				odbc_close($SQLLink);
			//}
			
			$sqlUpd = "UPDATE `customers` SET `IsSynced`='yes'
			WHERE `IsSynced`='no' AND CId IN (".$cId.");";

			$res = mysql_query($sqlUpd);
			
		}
	}
	catch (Exception $e) {					
				LogErr($e->getMessage());			
	} 
	//finally {
		mysql_close($ConnLink);		
	//}
		
				}		//$acctId != "" && $eId != "" if closed
			}			//$drow of while closed
		}
	}
	catch (Exception $e) {
				LogErr($e->getMessage());			
	} 

	
	mysqli_close($SConnLink);
		

	//$json_response = json_encode("{'Success':1}");
	$json_response = "{'Success':1}";
		# Optionally: Wrap the response in a callback function for JSONP cross-domain support
		if(ISSET($_REQUEST["callback"])) {
			$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
			}
		# Return the response
		echo $json_response;


function ConnectToMySQL() {
	try {    
		//$con = mysql_connect("192.168.1.52","conci","Mobikontech");
		//$con = mysql_connect("localhost:3307","root","Marijuana@77");  
			$con = mysql_connect("localhost:3306","root","");  
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

function ConnectToMySQLi() {
    try {    
			//$con = new mysqli("192.168.1.52","conci","Mobikontech");
			//$con =new mysqli("localhost:3307","root","Marijuana@77");	.
						$con =new mysqli("localhost:3306","root","");	
			if (mysqli_connect_errno())
			{
			  die('Could not connect: ' . mysqli_connect_error());
			}
	}
	catch (Exception $e) {
				LogErr($e->getMessage());			
	} 
    return $con;
}

function ConnectToMssql()
  {
	try {  
		 $dsn="Driver={SQL Server Native Client 10.0};Server=174.142.75.52;Database=KonektApp;";
		 //$dsn="Driver={SQL Server Native Client 10.0};Server=192.168.1.3\MOBIKONDBSTG;Database=KonektApp;";		 
		 //$dsn="Driver={SQL Server Native Client 10.0};Server=64.15.155.142;Database=KonektApp;";
		 
		 $username="konekt";
		 $password="Mobi!@#";
		 //$username='demodb';
		 //$password='Marijuana@77';
		 $SqlLink=odbc_connect($dsn,$username,$password)  or die ("could not connect");
	}
	catch (Exception $e) {				
			LogErr($e->getMessage());		
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
		 