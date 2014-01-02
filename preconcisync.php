<?php
	$user="";
	$pass="";
	$input="";
	$entityId="";
	$concSettingId="";
	$concTBSettingId="";
	$type="";
	$accountId ="";
	$aResults ="";
	$reqCnt="";

	//'d' : '{"u": "coromum","p":"coromum"}'

	if(ISSET($_REQUEST['d']))
		$input = $_REQUEST['d'];

	if(ISSET($_REQUEST['tp']))
		$type = $_REQUEST['tp'];

	if(ISSET($_REQUEST['cnt']))
		$reqCnt = $_REQUEST['cnt'];

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

	$inputDecode =json_decode($input);

	if($type=="AUTH") {
		$user = $inputDecode->{'u'};
		$pass = $inputDecode->{'p'};

		if($user=="" || $pass=="")
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
	}
	else if($type=="CONCSET" || $type=="CONCOFR" || $type=="CONCSEATPREF" || $type=="CONCTBSET" || $type=='CONCUSR'|| $type=="CONCBOOKINGS"|| $type=="CONCCUSTS")
	{
		$concSettingId = $inputDecode->{'c'};
		$entityId = $inputDecode->{'e'};

		if($concSettingId=="" || $entityId=="")
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

	}
	else if($type=="CONCTAT")
	{
		$concTBSettingId = $inputDecode->{'ctb'};
		$entityId = $inputDecode->{'e'};

		if($concTBSettingId=="" || $entityId=="")
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

	}

	$ConnLink = ConnectToMssql();

	if($type=="AUTH")
	{
		$sql = "SELECT EntityId,CU.ConciergeSettingId FROM conciergeusers CU INNER JOIN conciergesettings CS ON CU.ConciergeSettingId=
		CS.ConciergeSettingId WHERE [LoginName]='".str_replace("'", "''", $user)."' AND [Password]='".str_replace("'", "''", $pass)."';";


		$result =  odbc_exec($ConnLink,$sql) ;

		try {
				if (odbc_num_rows($result))
				{
					$row = odbc_fetch_array($result);

					$entityId=$row['EntityId'];
					$conciId=$row['ConciergeSettingId'];

					odbc_free_result($result);

					// OID,IsSynced not in sql server
					$sql = "SELECT
					ISNULL(OutletTimings.AccountId,'') AS AccountId
					,ISNULL(outlets.OutletId,'') AS EntityId,ISNULL(outlets.Area,'') AS Area
					,ISNULL(outlets.City,'') AS City,ISNULL((SELECT CountryCallingCode FROM Country where CountryId=outlets.Cell_Country),'') AS CountryCallingCode
					,ISNULL(outlets.Cell_Number,'') AS Cell_Number,
					ISNULL(outlets.EmailId,'') AS EmailId,ISNULL(outlets.EmailSenderEmail,'') AS EmailSenderEmailId
					,ISNULL(outlets.EmailSenderName,'') AS EmailSenderName,
					ISNULL(outlets.ContactPerson,'') AS ManagerName,ISNULL(SmsSenderName,'') AS SmsSenderName,
					ISNULL(CONVERT(VARCHAR, CAST(OutletTimings.BreakfastEnd AS DATETIME),108),'') AS BreakfastEnd,ISNULL(CONVERT(VARCHAR,CAST(OutletTimings.BreakfastStart AS DATETIME),108),'') AS BreakfastStart
	,ISNULL(CONVERT(VARCHAR, CAST(OutletTimings.LunchEnd AS DATETIME),108),'') AS LunchEnd,ISNULL(CONVERT(VARCHAR, CAST(OutletTimings.LunchStart AS DATETIME),108),'') AS LunchStart
	,ISNULL(CONVERT(VARCHAR, CAST(OutletTimings.DinnerEnd AS DATETIME),108),'') AS DinnerEnd,ISNULL(CONVERT(VARCHAR, CAST(OutletTimings.DinnerStart AS DATETIME),108),'') AS DinnerStart
					FROM outlets
					INNER JOIN OutletTimings on outlets.OutletId = OutletTimings.EntityId
					WHERE  EntityId='".$entityId."' ;";

					$result = odbc_exec($ConnLink,$sql) ;

					if(!$result)
						throw new Exception(odbc_error($ConnLink));

					if (odbc_num_rows($result))
					{
						$row = odbc_fetch_array($result);

						$a[] = array(
									 "AccountId"=>$row['AccountId']
									, "EntityId"=>$row['EntityId']
									, "Area"=>$row['Area']
									, "City"=>$row['City']
									, "CountryCallingCode"=>$row['CountryCallingCode']
									, "Cell_Number"=>$row['Cell_Number']
									, "EmailId"=>$row['EmailId']
									, "EmailSenderEmailId"=>$row['EmailSenderEmailId']
									, "EmailSenderName"=>$row['EmailSenderName']
									, "ManagerName"=>$row['ManagerName']
									, "SmsSenderName"=>$row['SmsSenderName']
									, "BreakfastStart"=>$row['BreakfastStart']
									, "BreakfastEnd"=>$row['BreakfastEnd']
									, "LunchStart"=>$row['LunchStart']
									, "LunchEnd"=>$row['LunchEnd']
									, "DinnerStart"=>$row['DinnerStart']
									, "DinnerEnd"=>$row['DinnerEnd']
									, "ConciergeSettingId"=>$conciId
									);

						$aResults[] = array("Success"=>1, "outlet" =>$a);
					}
					else
						$aResults[] =array("Success"=>0, "Reason" =>"Details not found");
				}
				else
					$aResults[] =array("Success"=>0, "Reason" =>"Incorrect Credentials"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {

				$file = 'preconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);

					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}
	}

	else if($type=="CONCSET")
	{
		$sql = "SELECT ConciergeSettings.AccountId,ISNULL(CheckInEmailMessage,'') AS CheckInEmailMessage
			,ISNULL(CheckInSMSMessage,'') AS CheckInSMSMessage,ISNULL(CheckOutEmailMessage,'') AS CheckOutEmailMessage,
			ISNULL(CheckOutSMSMessage,'') AS CheckOutSMSMessage,ConciergeSettingId
			,ISNULL((SELECT CountryCallingCode FROM Country where CountryId=outlets.Cell_Country),'') AS CountryCallingCode,CONVERT(VARCHAR,ConciergeSettings.CreatedOn, 120) AS CreatedOn
			,ISNULL(vwEntities.Emailid,'') AS Email,
			ISNULL(ConciergeSettings.EntityId,'') AS EntityId,ISNULL(vwEntities.EntityName,'') AS EntityName,IsSynced
			,ISNULL(ConciergeSettings.LogoPath,'') AS LogoPath
			,ISNULL(Outlets.ContactPerson,'') AS ManagerName,ISNULL(vwEntities.Cell_Number,'') AS Mobile,ISNULL( CONVERT(VARCHAR,ConciergeSettings.SettingExpiresOn, 120) ,'') AS SettingExpiresOn
			,ISNULL(vwEntities.TimeZone,'') AS TimeZone
			FROM ConciergeSettings INNER JOIN vwEntities ON ConciergeSettings.EntityId = vwEntities.EntityId LEFT OUTER JOIN
			Outlets ON vwEntities.EntityId = Outlets.OutletId
			WHERE ConciergeSettingId='".$concSettingId."';";

		try {

				$result = odbc_exec($ConnLink,$sql) ;

				if(!$result)
					throw new Exception(odbc_error($ConnLink));

				if (odbc_num_rows($result))
				{
					while($row = odbc_fetch_array($result))
					{

						$a[] = array("AccountId"=>$row['AccountId'],
							"CheckInEmailMessage"=>$row['CheckInEmailMessage'],
							"CheckInSMSMessage"=>$row['CheckInSMSMessage'],
							"CheckOutEmailMessage"=>$row['CheckOutEmailMessage'],
							"CheckOutSMSMessage"=>$row['CheckOutSMSMessage'],
							"ConciergeSettingId"=>$row['ConciergeSettingId'],
							"CountryCallingCode"=>$row['CountryCallingCode'],
							"CreatedOn"=>$row['CreatedOn'],
							"Email"=>$row['Email'],
							"EntityId"=>$row['EntityId'],
							"EntityName"=>$row['EntityName'],
							"IsSynced"=>$row['IsSynced'],
							"LogoPath"=>$row['LogoPath'],
							"ManagerName"=>$row['ManagerName'],
							"Mobile"=>$row['Mobile'],
							"SettingExpiresOn"=>$row['SettingExpiresOn'],
							"TimeZone"=>$row['TimeZone']
						);


					}
					$aResults[] = array("Success"=>1, "concset" =>$a);

				}
				else
					$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {

				$file = 'preconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->getMessage(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);

					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}

	}

	else if($type=="CONCOFR")
	{
		$sql = "SELECT ISNULL(AboutThisOffer,'') AS AboutThisOffer,ISNULL(ConciergeOfferId,'') AS ConciergeOfferId,ISNULL(ConciergeSettingId,'') AS ConciergeSettingId,ISNULL(CONVERT(VARCHAR,CreatedOn, 120),'') AS CreatedOn,ISNULL(Criteria,'') AS Criteria,
		ISNULL(EmailText,'') AS EmailText,ISNULL(IsActive,'') AS IsActive,ISNULL(IsSynced,'') AS IsSynced,ISNULL(NoOfOffers,'') AS NoOfOffers
		,ISNULL(NoOfPAX,'') AS NoOfPAX
		,ISNULL(CAST(OfferLastSentOn AS VARCHAR),'') AS OfferLastSentOn
		,ISNULL(OfferTitle,'') AS OfferTitle,
		ISNULL(PAXCriteria,'') AS PAXCriteria,ISNULL(SMSText,'') AS SMSText
		--,'' AS TotOfferSent
		,ISNULL(TotOfferSent,'') AS TotOfferSent
		,ISNULL(CAST(ValidFromDate AS VARCHAR),'') AS ValidFromDate,ISNULL(CONVERT(VARCHAR, CAST(ValidFromTime AS DATETIME),108),'') AS ValidFromTime
		,ISNULL(ValidOnWeekDays,'') AS ValidOnWeekDays,
		ISNULL(CAST(ValidToDate AS VARCHAR),'') AS ValidToDate,ISNULL(CONVERT(VARCHAR, CAST(ValidToTime AS DATETIME),108),'') AS ValidToTime,ISNULL(VoucherValidForSources,'') AS VoucherValidForSources
		FROM conciergealloffers WHERE ConciergeSettingId='".$concSettingId."';";
		try {


			$result = odbc_exec($ConnLink,$sql) ;

			if(!$result)
				throw new Exception(odbc_error($ConnLink));

			if (odbc_num_rows($result))
			{
				while($row = odbc_fetch_array($result))
				{
					$a[] = array("AboutThisOffer"=>$row['AboutThisOffer'],
						"ConciergeOfferId"=>$row['ConciergeOfferId'],
						"ConciergeSettingId"=>$row['ConciergeSettingId'],
						"CreatedOn"=>$row['CreatedOn'],
						"Criteria"=>$row['Criteria'],
						"EmailText"=>$row['EmailText'],
						"IsActive"=>$row['IsActive'],
						"IsSynced"=>$row['IsSynced'],
						"NoOfOffers"=>$row['NoOfOffers'],
						"NoOfPAX"=>$row['NoOfPAX'],
						"OfferLastSentOn"=>$row['OfferLastSentOn'],
						"OfferTitle"=>$row['OfferTitle'],
						"PAXCriteria"=>$row['PAXCriteria'],
						"SMSText"=>$row['SMSText'],
						"TotOfferSent"=>$row['TotOfferSent'],
						"ValidFromDate"=>$row['ValidFromDate'],
						"ValidFromTime"=>$row['ValidFromTime'],
						"ValidOnWeekDays"=>$row['ValidOnWeekDays'],
						"ValidToDate"=>$row['ValidToDate'],
						"ValidToTime"=>$row['ValidToTime'],
						"VoucherValidForSources"=>$row['VoucherValidForSources']
					);


				}
				$aResults[] = array("Success"=>1, "concofr" =>$a);

			}
			else
				$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {

				$file = 'preconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);

					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}

	}
	else if($type=="CONCSEATPREF")
	{
		$sql = "SELECT ISNULL(ConciergeSeatingPreferenceId,'') AS ConciergeSeatingPreferenceId,ISNULL(CONVERT(VARCHAR,CreatedOn, 120),'') AS CreatedOn
		,ISNULL(EntityId,'') AS EntityId,ISNULL(IsSynced,'') AS IsSynced,ISNULL(Preference,'') AS Preference
		FROM conciergeseatingpreferences WHERE EntityId='".$entityId."'";

		try {

			$result = odbc_exec($ConnLink,$sql) ;

			if(!$result)
				throw new Exception(odbc_error($ConnLink));

			if (odbc_num_rows($result))
			{
				while($row = odbc_fetch_array($result))
				{
					$a[] = array("ConciergeSeatingPreferenceId"=>$row['ConciergeSeatingPreferenceId'],
					"CreatedOn"=>$row['CreatedOn'],
					"EntityId"=>$row['EntityId'],
					"IsSynced"=>$row['IsSynced'],
					"Preference"=>$row['Preference']

					);


				}
				$aResults[] = array("Success"=>1, "concseatpref" =>$a);

			}
			else
				$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {

				$file = 'preconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);

					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}

	}

	else if($type=="CONCTBSET")
	{
		$sql = "SELECT ISNULL(ConciergeTableSettingId,'') AS ConciergeTableSettingId,ISNULL(CONVERT(VARCHAR,CreatedOn, 120),'') AS CreatedOn,ISNULL(EntityId,'') AS EntityId,ISNULL(IsSynced,'') AS IsSynced ,ISNULL(MatchingSeatingPreferenceIds,'') AS MatchingSeatingPreferenceIds,
ISNULL(MaxCapacity,'') AS MaxCapacity,ISNULL(SeatingCapacity,'') AS SeatingCapacity,ISNULL(TableNo,'') AS TableNo
FROM conciergetablesettings WHERE EntityId='".$entityId."';";

		try {

			$result = odbc_exec($ConnLink,$sql) ;

			if(!$result)
				throw new Exception(odbc_error($ConnLink));

			if (odbc_num_rows($result))
			{
				while($row = odbc_fetch_array($result))
				{

					$a[] = array("ConciergeTableSettingId"=>$row['ConciergeTableSettingId'],
						"CreatedOn"=>$row['CreatedOn'],
						"EntityId"=>$row['EntityId'],
						"IsSynced"=>$row['IsSynced'],
						"MatchingSeatingPreferenceIds"=>$row['MatchingSeatingPreferenceIds'],
						"MaxCapacity"=>$row['MaxCapacity'],
						"SeatingCapacity"=>$row['SeatingCapacity'],
						"TableNo"=>$row['TableNo']
					);

				}
				$aResults[] = array("Success"=>1, "conctbset" =>$a);

			}
			else
				$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {

				$file = 'preconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . $e ->__toString(). " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
				fclose($handle);

					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}

	}

	/*TURN AROUND TIME FOR EVERY TB SETTING ID. THIS SHOULD BE CALL IN LOOP */
	else if($type=="CONCTAT")
	{
		$sql = "SELECT ISNULL(ConciergeTableTurnaroundTimeId,'') AS ConciergeTableTurnaroundTimeId,ISNULL(ConciergeTableSettingId,'') AS ConciergeTableSettingId,ISNULL(TurnaroundTimeSun,'') AS TurnaroundTimeSun,
		ISNULL(TurnaroundTimeMon,'') AS TurnaroundTimeMon,ISNULL(TurnaroundTimeTue,'') AS TurnaroundTimeTue,ISNULL(TurnaroundTimeWed,'') AS TurnaroundTimeWed,ISNULL(TurnaroundTimeThu,'') AS TurnaroundTimeThu,ISNULL(TurnaroundTimeFri,'') AS TurnaroundTimeFri,
		ISNULL(TurnaroundTimeSat,'') AS TurnaroundTimeSat,ISNULL(ApplicableFor,'') AS ApplicableFor,ISNULL(CONVERT(VARCHAR,CreatedOn, 120),'') AS CreatedOn,ISNULL(IsSynced,'') AS IsSynced
		FROM conciergetableturnaroundtimes WHERE ConciergeTableSettingId='".$concTBSettingId."' ";


		$result = odbc_exec($ConnLink,$sql) ;

		if(!$result)
		{
			$file = 'preconcisync.txt';
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fwrite($handle,"\r\n");
			fwrite($handle, "TAT query id is : ". "\r\n". $sql . "\r\n" . 'Error message is : ' . print_r(odbc_error($ConnLink), true) . " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fclose($handle);
		}
			//throw new Exception(print_r( sqlsrv_errors(), true));


		try {
			if (odbc_num_rows($result))
			{
				while($row = odbc_fetch_array($result))
				{

					$a[] = array("ConciergeTableTurnaroundTimeId"=>$row['ConciergeTableTurnaroundTimeId'],
						"ConciergeTableSettingId"=>$row['ConciergeTableSettingId'],
						"TurnaroundTimeSun"=>$row['TurnaroundTimeSun'],
						"TurnaroundTimeMon"=>$row['TurnaroundTimeMon'],
						"TurnaroundTimeTue"=>$row['TurnaroundTimeTue'],
						"TurnaroundTimeWed"=>$row['TurnaroundTimeWed'],
						"TurnaroundTimeThu"=>$row['TurnaroundTimeThu'],
						"TurnaroundTimeFri"=>$row['TurnaroundTimeFri'],
						"TurnaroundTimeSat"=>$row['TurnaroundTimeSat'],
						"ApplicableFor"=>$row['ApplicableFor'],
						"CreatedOn"=>$row['CreatedOn'],
						"IsSynced"=>$row['IsSynced']
					);
				}
				$aResults[] = array("Success"=>1, "conctat" =>$a);

			}
			else
				$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {
					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}

	}
	else if($type=="CONCUSR")
	{
		$sql = "SELECT ISNULL(Cell_Number,'') AS Cell_Number,ISNULL(ConciergeSettingId,'') AS ConciergeSettingId,ISNULL(ConciergeUserId,'') AS ConciergeUserId,ISNULL(CONVERT(VARCHAR,CreatedOn, 120),'') AS CreatedOn,ISNULL(EmailId,'') AS EmailId,
		ISNULL(IsActive,'') AS IsActive,ISNULL(IsSynced,'') AS IsSynced,ISNULL(LoginName,'') AS LoginName,ISNULL(Name,'') AS Name,ISNULL(Password,'') AS Password,ISNULL(UserType,'') AS UserType
		FROM conciergeusers WHERE ConciergeSettingId='".$concSettingId."' ";

		$result = odbc_exec($ConnLink,$sql);

		if(!$result)
		{
			$file = 'preconcisync.txt';
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . print_r(odbc_error($ConnLink), true) . " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fclose($handle);
		}
			//throw new Exception(print_r( sqlsrv_errors(), true));

		try {
				if (odbc_num_rows($result))
				{
					while($row = odbc_fetch_array($result))
					{

						$a[] = array("Cell_Number"=>$row['Cell_Number'],
							"ConciergeSettingId"=>$row['ConciergeSettingId'],
							"ConciergeUserId"=>$row['ConciergeUserId'],
							"CreatedOn"=>$row['CreatedOn'],
							"EmailId"=>$row['EmailId'],
							"IsActive"=>$row['IsActive'],
							"IsSynced"=>$row['IsSynced'],
							"LoginName"=>$row['LoginName'],
							"Name"=>$row['Name'],
							"Password"=>$row['Password'],
							"UserType"=>$row['UserType']
						);
					}
					$aResults[] = array("Success"=>1, "concusr" =>$a);

				}
				else
				$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {


					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}

	} else if ($type == "CONCBOOKINGS")
	{
		//SentToApp, AppBookingId not present on sql server

		$sql = "SELECT ISNULL(ConciergeBookingId,'') AS ConciergeBookingId,ConciergeSettingId,ISNULL(Name,'') AS  Name
		,ISNULL(Gender,'') AS Gender,ISNULL(CountryCallingCode,'') AS CountryCallingCode,ISNULL(Cell_Number,'') AS Cell_Number
		,ISNULL(EmailId,'') AS EmailId,ISNULL(PAX,'') AS PAX,ISNULL(CAST(BookingDate AS VARCHAR),'') AS BookingDate
		,ISNULL(CONVERT(VARCHAR,BookingTime,108),'') AS BookingTime,ISNULL(CONVERT(VARCHAR,BookingUTCDateTime, 120), '') AS BookingUTCDateTime
		,ISNULL(SeatingPreferenceIDs,'') AS SeatingPreferenceIDs,ISNULL(TableIDs,'') AS TableIDs,ISNULL(TableNos,'') AS TableNos
		,ISNULL(BookingType,'') AS BookingType,ISNULL(Note, '') AS Note,ISNULL(CheckedIn,'') AS CheckedIn
		,ISNULL(BookingStatus,'') AS BookingStatus,ISNULL(CONVERT(VARCHAR,CheckInTime, 120),'') AS CheckInTime
		,ISNULL(CONVERT(VARCHAR,CheckInUTCDateTime, 120),'') AS CheckInUTCDateTime,ISNULL(CONVERT(VARCHAR,CheckOutTime, 120),'') AS CheckOutTime
		,ISNULL(CONVERT(VARCHAR,CheckOutUTCDateTime, 120),'') AS CheckOutUTCDateTime,ISNULL(BookingSource,'') AS BookingSource
		,ISNULL(CONVERT(VARCHAR,CreatedOn, 120),'') AS CreatedOn,ISNULL(CustomerId,'') AS CustomerId,ISNULL(CONVERT(VARCHAR,ModifiedOn, 120),'') AS ModifiedOn
		,ISNULL(MaxTurnAround,'') AS MaxTurnAround ,ISNULL(CONVERT(VARCHAR,ApproxTurnAroundTime, 120),'') AS ApproxTurnAroundTime
		,ISNULL(CONVERT(VARCHAR,ApproxTurnAroundUTCTime, 120),'') AS ApproxTurnAroundUTCTime
		,ISNULL(AppBookingId,'') AS AppBookingId
		FROM conciergebookings WHERE
		SentToApp = 'No' AND
		ConciergeSettingId='".$concSettingId."';";

		try {

			$result = odbc_exec($ConnLink,$sql);

			if(!$result)
			{
				$file = 'preconcisync.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, 'Error message is : ' . print_r(odbc_error($ConnLink), true) . " "   ."\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle);
			}

				if (odbc_num_rows($result))
				{
					while($row = odbc_fetch_array($result))
					{
						$a[] = array("ConciergeBookingId"=>$row['ConciergeBookingId'],
							"ConciergeSettingId"=>$row['ConciergeSettingId'],
							"Name"=>$row['Name'],
							"Gender"=>$row['Gender'],
							"CountryCallingCode"=>$row['CountryCallingCode'],
							"Cell_Number"=>$row['Cell_Number'],
							"EmailId"=>$row['EmailId'],
							"PAX"=>$row['PAX'],
							"BookingDate"=>$row['BookingDate'],
							"BookingTime"=>$row['BookingTime'],
							"BookingUTCDateTime"=>$row['BookingUTCDateTime'],
							"SeatingPreferenceIDs"=>$row['SeatingPreferenceIDs'],
							"TableIDs"=>$row['TableIDs'],
							"TableNos"=>$row['TableNos'],
							"BookingType"=>$row['BookingType'],
							"Note"=>$row['Note'],
							"CheckedIn"=>$row['CheckedIn'],
							"BookingStatus"=>$row['BookingStatus'],
							"CheckInTime"=>$row['CheckInTime'],
							"CheckInUTCDateTime"=>$row['CheckInUTCDateTime'],
							"CheckOutTime"=>$row['CheckOutTime'],
							"CheckOutUTCDateTime"=>$row['CheckOutUTCDateTime'],
							"BookingSource"=>$row['BookingSource'],
							"CreatedOn"=>$row['CreatedOn'],
							"CustomerId"=>$row['CustomerId'],
							"ModifiedOn"=>$row['ModifiedOn'],
							"MaxTurnAround"=>$row['MaxTurnAround'],
							"ApproxTurnAroundTime"=>$row['ApproxTurnAroundTime'],
							"ApproxTurnAroundUTCTime"=>$row['ApproxTurnAroundUTCTime'],
							//"IsSynced"=>$row['IsSynced'],
							"AppBookingId"=>$row['AppBookingId']
						);


					}

					$aResults[] = array("Success"=>1, "concbookings" =>$a);
					odbc_free_result($result);


						$sqlUpd = "UPDATE conciergebookings SET SentToApp ='Yes'
						WHERE ConciergeSettingId= '".$concSettingId."'
						AND AppBookingId IS NOT NULL ";

						$result = odbc_exec($ConnLink,$sqlUpd) or die(odbc_error($ConnLink));

				}
				else
					$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found"); // '{"Success":0,"Reason":"Incorrect Credentials"}';


		}
		catch (Exception $e) {
					LogErr($e->getMessage());
		}
		//finally {
			odbc_close($ConnLink);
		//}
	}
	else if ($type == "CONCCSTCNT")
	{
		$ActualCounter = "";
		$entityId = $inputDecode->{'e'};

		$sql = "SELECT COUNT(*) AS rcnt
		FROM customers INNER JOIN conciergesettings ON customers.AccountId = conciergesettings.AccountId
		WHERE customers.IsAppSynced = 'No' AND conciergesettings.EntityId='".$entityId ."' ;";

		$result = odbc_exec($ConnLink,$sql);

		if(!$result)
		{
			$file = 'preconcisync.txt';
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fwrite($handle,"\r\n");
			fwrite($handle, $entityId. "act cnt : ". "\r\n".  print_r(odbc_error($ConnLink), true) . "\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fclose($handle);
		}

		$row = odbc_fetch_array($result);

		$ActualCounter = $row['rcnt'];

		odbc_free_result($result);

		$aResults[] = array("Success"=>1, "conccstcnt" =>$ActualCounter);

		odbc_close($ConnLink);
	}
	else if ($type == "CONCCUSTS")
	{
		//	LastVisitedOn, LastComment, LastRating, Tags, IsAppSynced not present on SQL SERVER
		$sql = "
				SELECT TOP (".$reqCnt .")
				ISNULL(customers.CustomerId,'') AS CustomerId,
				ISNULL(customers.AccountId,'') AS AccountId,
				ISNULL(customers.CustomerName,'') AS CustomerName,
				ISNULL((SELECT CountryCallingCode FROM Country WHERE CountryId=customers.Cell_Country),'') AS CountryCallingCode,
				ISNULL(customers.Cell_Number,'') AS Cell_Number,
				ISNULL(customers.City,'') AS City,
				ISNULL(customers.Zip,'') AS Zip,
				ISNULL(customers.EmailId,'') AS EmailId,
				ISNULL(customers.Address,'') AS Address,
				ISNULL(CAST(customers.DateOfBirth AS VARCHAR),'') AS DOB,
				ISNULL(CAST(customers.Anniversary AS VARCHAR),'') AS Annv,
				ISNULL(CONVERT(VARCHAR,customers.CreatedOn, 120),'') AS CreatedOn,
				ISNULL(CONVERT(VARCHAR,customers.ModifiedOn, 120),'') AS ModifiedOn,
				'' AS LastVisitedOn,
				ISNULL(customers.Gender,'') AS Gender,
				'' AS LastComment,
				'' AS LastRating,
				'' AS Tags
				FROM customers INNER JOIN conciergesettings ON customers.AccountId = conciergesettings.AccountId
				WHERE
				customers.IsAppSynced = 'No' AND
				conciergesettings.EntityId='".$entityId ."'";

		/*$file = 'preconcisync.txt';
		$handle = fopen($file, 'a');
		$logTime = new DateTime();
		$logTime= $logTime->format('Y-m-d H:i:s');
		fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
		fwrite($handle,"\r\n");
		fwrite($handle, "pre custquery is : ". "\r\n". $sql . "\r\n" . $logTime);
		fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
		fclose($handle);*/

		//$result = mysql_query($sql);

		$result = odbc_exec($ConnLink,$sql);

		if(!$result)
		{
			$file = 'preconcisync.txt';
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fwrite($handle,"\r\n");
			fwrite($handle, 'Error message is : ' . print_r(odbc_error($ConnLink), true). " "   ."\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fclose($handle);
		}

		try {

			if (odbc_num_rows($result))
			{
				$custidtoUpdate = '';
				while($row = odbc_fetch_array($result))
				{
					if($custidtoUpdate=='')
						$custidtoUpdate= "'". $row['CustomerId']."'";
					else
						$custidtoUpdate .=",'".$row['CustomerId']."'";

					$a[] = array(
						"CustomerId"=>$row['CustomerId'],
						"AccountId"=>$row['AccountId'],
						"CustomerName"=>$row['CustomerName'],
						"CountryCallingCode"=>$row['CountryCallingCode'],
						"Cell_Number"=>$row['Cell_Number'],
						"City"=>$row['City'],
						"Zip"=>$row['Zip'],
						"EmailId"=>$row['EmailId'],
						"Address"=>$row['Address'],
						"DOB"=>$row['DOB'],
						"Annv"=>$row['Annv'],
						"CreatedOn"=>$row['CreatedOn'],
						"ModifiedOn"=>$row['ModifiedOn'],
						"LastVisitedOn"=>$row['LastVisitedOn'],
						"Gender"=>$row['Gender'],
						"LastComment"=>$row['LastComment'],
						"LastRating"=>$row['LastRating'],
						"Tags"=>$row['Tags']
					);
				}


				$aResults[] = array("Success"=>1, "conccusts" =>$a);

				//$SqlliConnLink = ConnectToMySQLi();
				//mysqli_select_db($SqlliConnLink,"dummy_konektconci");

				$sqlUpd = "
				UPDATE Customers
				SET Customers.IsAppSynced ='Yes'
				FROM Customers INNER JOIN  ConciergeSettings ON
				Customers.AccountId = ConciergeSettings.AccountId
				WHERE Customers.CustomerId IN (".$custidtoUpdate.") AND ConciergeSettings.EntityId='".$entityId ."'";

				//$result = mysqli_query($SqlliConnLink,$sqlUpd) or die(mysqli_error($SqlliConnLink));
				//mysqli_close($SqlliConnLink);

				$result =odbc_exec($ConnLink,$sqlUpd);

				if(!$result)
				{
					$file = 'preconcisync.txt';
					$handle = fopen($file, 'a');
					$logTime = new DateTime();
					$logTime= $logTime->format('Y-m-d H:i:s');
					fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
					fwrite($handle,"\r\n");
					fwrite($handle, 'Error message is : ' . print_r(odbc_error($ConnLink), true). " "   ."\r\n" . $logTime);
					fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
					fclose($handle);
				}
			}
			else
				$aResults[] =array("Success"=>0, "Reason" =>"Details Not Found");
		}
		catch (Exception $e) {
					LogErr($e->getMessage());
		}
		odbc_close($ConnLink);

	}



	$json_response = json_encode($aResults);
	# Optionally: Wrap the response in a callback function for JSONP cross-domain support
	if(ISSET($_REQUEST["callback"])) {
		$json_response = $_REQUEST["callback"] . "(" . $json_response . ")";
		}
	# Return the response
	echo $json_response;

/*
function ConnectToMySQL() {
	try {
		//$con = mysql_connect("192.168.1.52","conci","Mobikontech");		  //local
		$con = mysql_connect("localhost:3307","root","Marijuana@77");  		  //beta
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

	//$mysqli = new mysqli("192.168.1.52","conci","Mobikontech");
	$mysqli =new mysqli("localhost:3307","root","Marijuana@77");	//beta
    if (mysqli_connect_errno())
    {
      die('Could not connect: ' . mysqli_connect_error());
    }

    return $mysqli;
} */

  function ConnectToMssql()
  {
	try {
		$dsn="Driver={SQL Server Native Client 10.0};Server=mobi-staging.cunbw6re0gf0.ap-southeast-1.rds.amazonaws.com;Database=KonektApp;";
		$username="konektrds";
		$password="L0g!nK0n3ktrd5!";
		 $SqlLink=odbc_connect($dsn,$username,$password)  or die ("could not connect");
	}
	catch (Exception $e) {
			LogErr($e->getMessage());
	}
	  return $SqlLink;
  }

/*function ConnectToMssql(){
		try {
			 //$dsn="Driver={SQL Server Native Client 10.0};Server=174.142.75.52;Database=KonektApp;";
			 //$username="konekt";
			 //$password="Mobi!@#";


			 $username='demodb';
			 $password='Marijuana@77';
			 //$SqlLink=odbc_connect($dsn,$username,$password) or die ("could not connect");
			//$dsn="Driver={SQL Server Native Client 10.0};Server=64.15.155.142;Database=KonektApp;";				//beta

			$servername = "64.15.155.142";
			$connectionOptions = array("Database"=>"KonektApp", "UID"=>$username, "PWD"=>$password);

			$SqlLink = sqlsrv_connect($servername, $connectionOptions) or die("Connection to MS SQL could not be established.\n");

			if( $SqlLink === false)
			{
				//print_r(sqlsrv_errors());
				throw new Exception(print_r( sqlsrv_errors(), true));
			}

		}
		catch (Exception $e) {
				LogErr($e->getMessage());

				$file = 'preconcisync.txt';
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
	}   */

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
