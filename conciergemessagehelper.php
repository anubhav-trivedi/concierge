<?php


class ConciergeMessageSender
{
	
	var $gSendMessageURL;
	
	public function ConciergeMessageSender()
	{
		//$this->gSendMessageURL = "http://beta.mobikontech.com/konekt/service/konektapi.asmx/";		//Beta
		$this->gSendMessageURL = "http://getkonekt.com/konekt/service/konektapi.asmx/";			//Prod		
	}

		
	public function PostMessage($custrow,$outrow,$Mode)
	{
		$OutputMsgString = "";
		$RetVal = "";
		$ReturnResult = "";
		$Response ="";
		
						
		if($Mode == "Book")
		{
			$ReturnResult = $this->GenerateResponse($custrow,$outrow,'Book');	
		}
		else if($Mode == "Amend" || $Mode == "Amendep")	
		{
			$ReturnResult = $this->GenerateResponse($custrow,$outrow,'Amend');	
		}
		else if($Mode == "Cancel")	
		{
			$ReturnResult = $this->GenerateResponse($custrow,$outrow,'Cancel');	
		}		
		else if($Mode == "Checkin")	
		{
			$ReturnResult = $this->GenerateResponse($custrow,$outrow,'Checkin');	
		}	
		else if($Mode == "Checkout")	
		{
			$ReturnResult = $this->GenerateResponse($custrow,$outrow,'Checkout');	
		}	
		

		if($Mode == "Book" || $Mode == "Amend")	
		{
			if(strtolower($custrow['CheckedIn']) == strtolower("yes"))
			{
				$ReturnResult .= $this->GenerateResponse($custrow,$outrow,'Checkin');
			}									
		}
 
						
		//send messages
		if($ReturnResult !="")
		{
			$Response = "<?xml version=\"1.0\"?><Request type=\"simple\">";
			$Response .= $ReturnResult;
			$Response .= "</Request>";
			//$Response = "data=" . $Response;
			
			$file = 'data.txt';
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fwrite($handle,"\r\n");
			fwrite($handle, "Return result is : ". "\r\n".  $Response . "\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fclose($handle);
			
			set_time_limit(0);							
			$url = $this->gSendMessageURL . "SendMessage";
			
			$fields_string ="";
			$fields = array('data'=>urlencode($Response));

			//url-ify the data for the POST

			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
				rtrim($fields_string,'&');
			 

			$ch = curl_init();

			//Set the URL
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type:  application/x-www-form-urlencoded"));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
			curl_setopt($ch, CURLOPT_URL, $url);

			//Enable POST data
			curl_setopt($ch, CURLOPT_POST, count($fields));
			//Use the $pData array as the POST data
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

			//curl_exec automatically writes the data returned
			$retVal=curl_exec($ch);

			//echo $retVal;
			curl_close($ch);			
			
	/*		$ch = curl_init();					
			//Set the URL
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//Enable POST data
			curl_setopt($ch, CURLOPT_POST, true);
			//Use the $pData array as the POST data
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Response);

			//curl_exec automatically writes the data returned
			$retVal=curl_exec($ch);
			
			// Check if any error occurred
			if(curl_errno($ch))
			{
				//echo 'Curl error: ' . curl_error($ch);
				
				$file = 'data.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, "Curl error: ". "\r\n".  print_r(curl_error($ch), true) . "\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle); 
			}

			// close cURL resource, and free up system resources
			curl_close($ch);  */
			
			$file = 'data.txt';
			$handle = fopen($file, 'a');
			$logTime = new DateTime();
			$logTime= $logTime->format('Y-m-d H:i:s');
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fwrite($handle,"\r\n");
			fwrite($handle, "ret val  is : ". "\r\n".  print_r($retVal, true) . "\r\n" . $logTime);
			fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
			fclose($handle); 		
		}
					
	}

  
  
  function ConnectToMssql()
  {
	 /* BETA
     $dsn="Driver={SQL Server Native Client 10.0};Server=mobi-staging.cunbw6re0gf0.ap-southeast-1.rds.amazonaws.com;Database=KonektApp;";		//Beta
     $username="konektrds";
     $password="L0g!nK0n3ktrd5!";
     $SqlLink=odbc_connect($dsn,$username,$password)  or die ("could not connect"); */

	 // PROD
     $dsn="Driver={SQL Server Native Client 10.0};Server=174.142.77.66;Database=KonektApp;";		
     $username="konekt";
     $password="Mobi!@#";
     $SqlLink=odbc_connect($dsn,$username,$password)  or die ("could not connect"); 
	
	  return $SqlLink;
   }
   

	
	private function GenerateResponse($custrow,$outrow,$Mode)
	{
		
		$RetVal = "";
		
		$PartialCommonStr ="";
		$FollowTemplate ="";
		$FollowTemplateOffer ="";

		$CustSMSTxt ="";
		$CustEmailTxt ="";
		$ManagerSMSTxt ="";
		$ManagerEmailTxt ="";
		$OfferSubject ="";
		$OfferSMSTxt ="";
		$OfferEmailTxt ="";
		$ManagerSubject ="";
		$CustSubject ="";
		
		$AccId = $outrow['AccountId'];
		$EntityId = $outrow['EntityId'];
					
		$PartialCommonStr .= "<Message>";
		
		/*Commented to handle API case for third party 05-Dec-2013 Using Konekt account */
		//$PartialCommonStr .= "<EntityId>" . $EntityId . "</EntityId>";
		
		// Parse without sections
		$ini_array = parse_ini_file("conciergeconfig.ini");
		$KonektEntityId = $ini_array['konektaccount'];
		
		$Source = trim($custrow['BookingSource']);
		if(strtolower($Source) === strtolower("API"))
		{						
			$PartialCommonStr .= "<EntityId>" . $KonektEntityId . "</EntityId>";
		}
		else
		{
			$PartialCommonStr .= "<EntityId>" . $EntityId . "</EntityId>";
		}
		
		$PartialCommonStr .= "<CampaignId></CampaignId>";
		$PartialCommonStr .= "<MsgType>General</MsgType>";
		$PartialCommonStr .= "<AcquireCust>No</AcquireCust>";
		$PartialCommonStr .= "<AuthKey>743A8F1B-C33F-48DC-9B3E-93BA4DD2E280</AuthKey>";
		$FollowTemplate = "<FollowTemplate>True</FollowTemplate>";
		$FollowTemplateOffer = "<FollowTemplate>False</FollowTemplate>";
		
		
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

		$CustomerString	= "";
		$CustMsgToString = "";
		
		
		switch ($Mode) 
		{
			case 'Book':
				
				$CustSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 
												
				$CustEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 				
								
				$ManagerSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 
				
				/*	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/	
				/*  GenerateOccasionString converts [[OccasionString]] to birthday, anniversary message. */ 	
				$ManagerSMSTxt = $this->GenerateOccasionString($ManagerSMSTxt,$custrow);
				/*  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/
				
				$ManagerEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 	
				
				/*	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/
				$ManagerEmailTxt = $this->GenerateOccasionString($ManagerEmailTxt,$custrow);							
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/				
								
				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow); 
				
				$ManagerSubject =  $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow);
				
				$OfferSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Offer")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow);
														
				/* Build messages as  per  messaging subscription */
				
				/* Customer Message */				
				if($CustSMSTxt =="")
				{
					$CustomerString .= "<Text><![CDATA[]]></Text>";
				}
				else
				{
					$CustomerString .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
				}
				
				if($CustEmailTxt =="")
				{
					$CustomerString .= "<EmailText><![CDATA[]]></EmailText>";
					$CustomerString .= "<EmailSubject><![CDATA[]]></EmailSubject>";
				}
				else
				{
					$CustomerString .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
					$CustomerString .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";				
				}
				/* Customer Message Complete */				
				
				if($custrow['Cell_Number'] =="")
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				else
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
					
				if(strtolower($custrow["CstBookFlg"]) == 'yes')
				{
					//Default customer msg
					$RetVal .= $PartialCommonStr;
					$RetVal .= $FollowTemplate;					
					$RetVal .= $CustomerString;	
					$RetVal .= $CustMsgToString;			
					$RetVal .= "</Message>";
				}
			
				if(strtolower($custrow["MngBookFlg"]) == 'yes')
				{
						$RetVal .= $PartialCommonStr;
						$RetVal .= $FollowTemplate;

						/* Manager Message */
						
						if($ManagerSMSTxt =="")
						{
							$RetVal .= "<Text><![CDATA[]]></Text>";
						}
						else
						{
							$RetVal .= "<Text><![CDATA[".$ManagerSMSTxt."]]></Text>";						
						}

						if($ManagerEmailTxt =="")
						{
							$RetVal .= "<EmailText><![CDATA[]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[]]></EmailSubject>";
						}
						else
						{
							$RetVal .= "<EmailText><![CDATA[".$ManagerEmailTxt."]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[".$ManagerSubject."]]></EmailSubject>";							
						}
						
						/* Manager Message Complete*/
						
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
												
				if(strtolower($custrow["CstOfferFlg"]) == 'yes' && $custrow["OfferId"] != "")
				{
					$NewOfferConciergeCode="";
					
					$OfferSMSTxt = $this->ResolveTags($custrow['OfferSMSText'],$custrow,$outrow);
					
					//$OfferEmailTxt = $this->ResolveTags($custrow['OfferEmailText'],$custrow,$outrow);
					
					$ConnLink = ConnectToMssql();
					
$offSql = "SET NOCOUNT ON; SET TEXTSIZE 268435456; SELECT ISNULL(CAST(EmailText AS Text),'') AS OfferEmailText FROM conciergealloffers WHERE ConciergeOfferId =CAST(". $custrow["OfferId"] ." AS INT) ; SET TEXTSIZE 0;";
					$omresult = odbc_exec($ConnLink,$offSql);
					$row = odbc_fetch_array($omresult);
					
					$OfferEmailTxt = $this->ResolveTags($row['OfferEmailText'],$custrow,$outrow);
																		
								$file = 'data.txt';				
								$handle = fopen($file, 'a');
								$logTime = new DateTime();
								$logTime= $logTime->format('Y-m-d H:i:s');
								fwrite($handle, "--------------------------------------------------------------------------------------------------");
								fwrite($handle,"\r\n");
								fwrite($handle, 'offer email text is from helper  : ' . $row['OfferEmailText']. " "   ."\r\n". $OfferEmailTxt ."\r\n". $logTime);
								fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
								fclose($handle);
								
					odbc_free_result($omresult);
					odbc_close($ConnLink);	
					$row ="";
					
					
					$RetVal .= $PartialCommonStr;
					$RetVal .= $FollowTemplateOffer;
					
					if($OfferSMSTxt !="" || $OfferEmailTxt != "")				
					{
						//Generate offercode and make entry to DB
							//Offercode generation
							$smspos = strpos($OfferSMSTxt, "[[NewOfferCode]]");
							$mailpos = strpos($OfferEmailTxt, "[[NewOfferCode]]");
							
							if($smspos ==true || $mailpos== true)
							{
								//[[NewOfferCode]] exists
																				
								$aResults ="";
								$ConnLink ="";
								$Offercode ="";
								
								$ConnLink = ConnectToMssql();								
								try
								{										
									//$ConnLink = ConnectToMySQL();
									//mysql_select_db("konektconci", $ConnLink);
									
									
									
$sql = "EXEC spConciergeAppGenerateNewConciergeOfferCode '".$custrow['ConciergeSettingId']."','".$custrow['ConciergeBookingId']."','".$custrow['OfferId']."'";

$oresult = odbc_exec($ConnLink,$sql);
//$oresult = odbc_exec($ConnLink,"SELECT @NewOfferConciergeCode AS Offercode;");

//$sql = "EXEC spConciergeAppGenerateNewConciergeOfferCode @pConciergeSettingId =?,@pConciergeBookingId = ?,@pOfferId = ?,@NewOfferConciergeCode = ?";

//$params = array(array($custrow['ConciergeSettingId'], SQLSRV_PARAM_IN),array($custrow['ConciergeBookingId'], SQLSRV_PARAM_IN,),array($custrow['OfferId'], SQLSRV_PARAM_IN),array($NewOfferConciergeCode, SQLSRV_PARAM_OUT,SQLSRV_PHPTYPE_STRING('UTF-8'),SQLSRV_SQLTYPE_NVARCHAR(250)));

//$oresult = sqlsrv_query($ConnLink,$sql,$params) ;


									if(!$oresult)
									{
										$file = 'data.txt';				
										$handle = fopen($file, 'a');
										$logTime = new DateTime();
										$logTime= $logTime->format('Y-m-d H:i:s');
										fwrite($handle, "--------------------------------------------------------------------------------------------------");
										fwrite($handle,"\r\n");
										fwrite($handle, $sql . "\r\n" . "Error is :". "\r\n" . print_r(odbc_error($ConnLink), true) ."\r\n" . $logTime);
										fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
										fclose($handle);									
									} 
									
									$row = odbc_fetch_array($oresult);
									
										/*$file = 'data.txt';				
										$handle = fopen($file, 'a');
										$logTime = new DateTime();
										$logTime= $logTime->format('Y-m-d H:i:s');
										fwrite($handle, "--------------------------------------------------------------------------------------------------");
										fwrite($handle,"\r\n");
										fwrite($handle, "Got Offer : " ."\r\n" .$NewOfferConciergeCode ."\r\n" . $logTime);
										fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
										fclose($handle);*/
										

									$Offercode = $row['OfferConciergeCode'];
										
										$file = 'data.txt';				
										$handle = fopen($file, 'a');
										$logTime = new DateTime();
										$logTime= $logTime->format('Y-m-d H:i:s');
										fwrite($handle, "--------------------------------------------------------------------------------------------------");
										fwrite($handle,"\r\n");
										fwrite($handle, $sql . "\r\n" . "Offer code generated :". "\r\n" . $Offercode ."\r\n" . $logTime);
										fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n\r\n");
										fclose($handle);
										
									if($Offercode !="")
									{
										$OfferSMSTxt = str_replace("[[NewOfferCode]]",$Offercode,$OfferSMSTxt);	
										$OfferEmailTxt = str_replace("[[NewOfferCode]]",$Offercode,$OfferEmailTxt);	
									}
									else
									{
										$OfferSMSTxt = str_replace("[[NewOfferCode]]","",$OfferSMSTxt);	
										$OfferEmailTxt = str_replace("[[NewOfferCode]]","",$OfferEmailTxt);																			
									}						
									
									odbc_close($ConnLink);									
								}
								catch(Exception $e)
								{
									odbc_close($ConnLink);				
								}

							}
						
						if($OfferSMSTxt =="")
						{
							$RetVal .= "<Text><![CDATA[]]></Text>";						
						}
						else
						{
							$RetVal .= "<Text><![CDATA[".$OfferSMSTxt."]]></Text>";	
						}

						if($OfferEmailTxt =="")
						{
							$RetVal .= "<EmailText><![CDATA[]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[]]></EmailSubject>";
						}
						else
						{
							$RetVal .= "<EmailText><![CDATA[".$OfferEmailTxt."]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[".$OfferSubject."]]></EmailSubject>";
						}

						
					}
					
					$RetVal .= $CustMsgToString;			
					$RetVal .= "</Message>";			
				}			
			
					
						$file = 'data.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fwrite($handle,"\r\n");
						fwrite($handle, "Book Response : ". "\r\n".  $RetVal . "\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fclose($handle);
						
				break;
			case 'Amend':	
			case 'Amendep':

				$CustSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 
												
				$CustEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 				
				
				$ManagerSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 
				
				/*	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/
				$ManagerSMSTxt = $this->GenerateOccasionString($ManagerSMSTxt,$custrow);
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/		
				
				$ManagerEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 	
				
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/		
				$ManagerEmailTxt = $this->GenerateOccasionString($ManagerEmailTxt,$custrow);
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/						
								
				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow); 
				
				$ManagerSubject =  $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow);
				
				
				/* Build messages as  per  messaging subscription */

				/* Customer Message */
				if($CustSMSTxt =="")
				{
					$CustomerString .= "<Text><![CDATA[]]></Text>";
				}
				else
				{
					$CustomerString .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
				}
				
				if($CustEmailTxt =="")
				{
					$CustomerString .= "<EmailText><![CDATA[]]></EmailText>";
					$CustomerString .= "<EmailSubject><![CDATA[]]></EmailSubject>";
				}
				else
				{
					$CustomerString .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
					$CustomerString .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";				
				}
				/* Customer Message Complete*/
				
				if($custrow['Cell_Number'] =="")
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				else
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				
				if(strtolower($custrow["CstAmendFlg"]) == 'yes')
				{
					//Default customer msg
					$RetVal .= $PartialCommonStr;	
					$RetVal .= $FollowTemplate;					
					$RetVal .= $CustomerString;	
					$RetVal .= $CustMsgToString;			
					$RetVal .= "</Message>";
				}
			
				if(strtolower($custrow["MngAmendFlg"]) == 'yes')
				{
						$RetVal .= $PartialCommonStr;
						$RetVal .= $FollowTemplate;

						/* Manager Message */
						if($ManagerSMSTxt =="")
						{
							$RetVal .= "<Text><![CDATA[]]></Text>";
						}
						else
						{
							$RetVal .= "<Text><![CDATA[".$ManagerSMSTxt."]]></Text>";						
						}

						if($ManagerEmailTxt =="")
						{
							$RetVal .= "<EmailText><![CDATA[]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[]]></EmailSubject>";
						}
						else
						{
							$RetVal .= "<EmailText><![CDATA[".$ManagerEmailTxt."]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[".$ManagerSubject."]]></EmailSubject>";							
						}
						/* Manager Message Complete*/
											
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
				
						$file = 'data.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fwrite($handle,"\r\n");
						fwrite($handle, "Amend Response : ". "\r\n".  $RetVal . "\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fclose($handle);					
				break;
			case 'Cancel':	

				$CustSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 
												
				$CustEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 				
				
				$ManagerSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 
				
				$ManagerEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow,$outrow); 	
				
				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow); 
				
				$ManagerSubject =  $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow);
			

				/* Build messages as  per  messaging subscription */

					/* Customer Message */
					if($CustSMSTxt =="")
					{
						$CustomerString .= "<Text><![CDATA[]]></Text>";
					}
					else
					{
						$CustomerString .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
					}
					
					if($CustEmailTxt =="")
					{
						$CustomerString .= "<EmailText><![CDATA[]]></EmailText>";
						$CustomerString .= "<EmailSubject><![CDATA[]]></EmailSubject>";
					}
					else
					{
						$CustomerString .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
						$CustomerString .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";				
					}
					/* Customer Message Complete*/
				
				if($custrow['Cell_Number'] =="")
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				else
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				
				if(strtolower($custrow["CstCancelFlg"]) == 'yes')
				{
					//Default customer msg
					$RetVal .= $PartialCommonStr;	
					$RetVal .= $FollowTemplate;					
					$RetVal .= $CustomerString;	
					$RetVal .= $CustMsgToString;			
					$RetVal .= "</Message>";
				}
			
				if(strtolower($custrow["MngCanelFlg"]) == 'yes')
				{
						$RetVal .= $PartialCommonStr;
						$RetVal .= $FollowTemplate;

						/* Manager Message */
						if($ManagerSMSTxt =="")
						{
							$RetVal .= "<Text><![CDATA[]]></Text>";
						}
						else
						{
							$RetVal .= "<Text><![CDATA[".$ManagerSMSTxt."]]></Text>";						
						}

						if($ManagerEmailTxt =="")
						{
							$RetVal .= "<EmailText><![CDATA[]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[]]></EmailSubject>";
						}
						else
						{
							$RetVal .= "<EmailText><![CDATA[".$ManagerEmailTxt."]]></EmailText>";
							$RetVal .= "<EmailSubject><![CDATA[".$ManagerSubject."]]></EmailSubject>";							
						}
						/* Manager Message Complete*/
											
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


						$file = 'data.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fwrite($handle,"\r\n");
						fwrite($handle, "Cancel Response : ". "\r\n".  $RetVal . "\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fclose($handle);	
						
				break; 
			case 'Checkin':	

				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("CheckIn")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow); 
				
				$CustSMSTxt = $this->ResolveTags($custrow['CheckInSMSMessage'],$custrow,$outrow); 
												
				$CustEmailTxt = $this->ResolveTags($custrow['CheckInEmailMessage'],$custrow,$outrow); 				
						
				/*$file = 'data.txt';
				$handle = fopen($file, 'a');
				$logTime = new DateTime();
				$logTime= $logTime->format('Y-m-d H:i:s');
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
				fwrite($handle,"\r\n");
				fwrite($handle, "Checkin message : ". "\r\n".  $CustSMSTxt . "\r\n" . $CustEmailTxt . "\r\n" . $logTime);
				fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
				fclose($handle);*/
				
				/* Build messages as  per  messaging subscription */

					/* Customer Message */
					if($CustSMSTxt =="")
					{
						$CustomerString .= "<Text><![CDATA[]]></Text>";
					}
					else
					{
						$CustomerString .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
					}
					
					if($CustEmailTxt =="")
					{
						$CustomerString .= "<EmailText><![CDATA[]]></EmailText>";
						$CustomerString .= "<EmailSubject><![CDATA[]]></EmailSubject>";
					}
					else
					{
						$CustomerString .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
						$CustomerString .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";				
					}
					/* Customer Message Complete*/
				
				if($custrow['Cell_Number'] =="")
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				else
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
					
				if(strtolower($custrow["CstChkinFlg"]) == 'yes')
				{
					//Default customer msg
					$RetVal .= $PartialCommonStr;
					$RetVal .= $FollowTemplate;					
					$RetVal .= $CustomerString;	
					$RetVal .= $CustMsgToString;			
					$RetVal .= "</Message>";
				}
			
				
						$file = 'data.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fwrite($handle,"\r\n");
						fwrite($handle, "Checkin Response : ". "\r\n".  $RetVal . "\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fclose($handle);					
				break;
			case 'Checkout':	

				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("CheckOut")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow,$outrow); 
				
				$CustSMSTxt = $this->ResolveTags($custrow['CheckOutSMSMessage'],$custrow,$outrow); 
												
				$CustEmailTxt = $this->ResolveTags($custrow['CheckOutEmailMessage'],$custrow,$outrow); 				

				/* Build messages as  per  messaging subscription */

					/* Customer Message */
					if($CustSMSTxt =="")
					{
						$CustomerString .= "<Text><![CDATA[]]></Text>";
					}
					else
					{
						$CustomerString .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
					}
					
					if($CustEmailTxt =="")
					{
						$CustomerString .= "<EmailText><![CDATA[]]></EmailText>";
						$CustomerString .= "<EmailSubject><![CDATA[]]></EmailSubject>";
					}
					else
					{
						$CustomerString .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
						$CustomerString .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";				
					}
					/* Customer Message Complete*/
				
				if($custrow['Cell_Number'] =="")
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
				else
				{
					$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
				}
					
				if(strtolower($custrow["CstChkoutFlg"]) == 'yes')
				{
					//Default customer msg
					$RetVal .= $PartialCommonStr;	
					$RetVal .= $FollowTemplate;					
					$RetVal .= $CustomerString;	
					$RetVal .= $CustMsgToString;			
					$RetVal .= "</Message>";
				}
				
						$file = 'data.txt';
						$handle = fopen($file, 'a');
						$logTime = new DateTime();
						$logTime= $logTime->format('Y-m-d H:i:s');
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fwrite($handle,"\r\n");
						fwrite($handle, "Checkout Response : ". "\r\n".  $RetVal . "\r\n" . $logTime);
						fwrite($handle, "--------------------------------------------------------------------------------------------------". "\r\n");
						fclose($handle);
						
				break;				
		}
							
		$objDOM = null;
		
		return $RetVal;
	}	
	
	function ResolveTags($Message, $Row,$OutletRow)
	{
		if($Row["CustomerName"] =="")
		{
			$Message = str_replace("[[CustomerName]]","Customer",$Message);
		}
		else		
		{
			/*if($Row["Gender"] == "Male")
				$Message = str_replace("[[CustomerName]]","Mr. " . $Row["CustomerName"],$Message);
			else if($Row["Gender"] == "Female")	
				$Message = str_replace("[[CustomerName]]","Ms. " . $Row["CustomerName"],$Message);*/
				
			$Message = str_replace("[[CustomerName]]",$Row["CustomerName"],$Message);	
			
			//Case to handle if undefined comes against cusotmer name
			$Message = str_replace("undefined. ","",$Message);
		}
		
		$Message = str_replace("[[CustomerEmail]]",$Row["CustomerEmail"],$Message);	
		$Message = str_replace("[[RequestDate]]",$Row["RequestDate"],$Message);
		$Message = str_replace("[[RequestTime]]",$Row["RequestTime"],$Message);
		$Message = str_replace("[[NoOfPeople]]",$Row["NoOfPeople"],$Message);
		$Message = str_replace("[[OutletName]]",$Row["OutletName"],$Message);		
		$Message = str_replace("[[CountryCallingCode]]",$Row["CountryCallingCode"],$Message);			
		$Message = str_replace("[[Cell_Number]]",$Row["Cell_Number"],$Message);		
		
		/* Manager name */	
		if(ISSET($OutletRow["ManagerName"]))		
		{
				$pos = strpos($Message, "[[ManagerName]]");
				if($pos !== false)
				{
					$Message = str_replace("[[ManagerName]]",$OutletRow["ManagerName"],$Message);
				}
		}
		else
		{
			$Message = str_replace("[[ManagerName]]","",$Message);
		}		
		
		/* Manager cell number */	
		if(ISSET($OutletRow["MCallingCode"]) && ISSET($OutletRow["MCell_Number"]))
		{
				$pos = strpos($Message, "[[ManagerNumber]]");
				if($pos !== false)
				{
					$number = $OutletRow["MCallingCode"].$OutletRow["MCell_Number"];
					$Message = str_replace("[[ManagerNumber]]",$number,$Message);
				}
		}
		else
		{
			$Message = str_replace("[[ManagerNumber]]","",$Message);
		}
		
		return $Message;
	}	

	private function GenerateOccasionString($Msg,$custrow)
	{
		$CustomString = "";
		$ModifiedString= "";
		
		$ModifiedString= $Msg;
		
		$pos = strpos($ModifiedString, "[[OccasionString]]");
		if($pos !== false)
		{
			//OccasionString exists
			if($custrow["BDayDM"] !="")
			{
				if($custrow["BookDM"] == $custrow["BDayDM"])
				{
					$CustomString = "This customer's birthday is on ". $custrow["DisplayBirthday"] . " ";
				}
			}						
			if($custrow["AnnvDM"] !="")
			{
				if($custrow["BookDM"] == $custrow["AnnvDM"])
				{
					if($CustomString != "")
					{
						$CustomString .= " and anniversary is on ". $custrow["DisplayAnniversary"] . " ";
					}
					else
					{
						$CustomString = "This customer's anniversary is on ". $custrow["DisplayAnniversary"] . " ";
					}
				}
			}
			
			$ModifiedString = str_replace("[[OccasionString]]",$CustomString,$ModifiedString);			
		}
		
		return $ModifiedString;
						
	}

	function ConnectToMySQL() {
		//$con = mysql_connect("192.168.1.52","conci","Mobikontech");			//Local
		$con = mysql_connect("localhost:3307","root","Marijuana@77");			//Beta
		//$con = mysql_connect("localhost:3306","root","");						//Prod
		if (!$con)
		{
		  die('Could not connect: ' . mysql_error());
		}

		return $con;
	}

	function ConnectToMySQLi() {
		
		//$mysqli = new mysqli("192.168.1.52","conci","Mobikontech");			//Local
		$mysqli =new mysqli("localhost:3307","root","Marijuana@77");			//Beta
		//$mysqli = new mysqli("localhost:3306","root","");						//Prod	
		if (mysqli_connect_errno())
		{
		  die('Could not connect: ' . mysqli_connect_error());
		}

		return $mysqli;
	}

	
   
		/*private function GenerateResponse($custrow,$outrow,$Mode)
	{
		
		$RetVal = "";
		
		$PartialCommonStr ="";
		
		$CustSMSTxt ="";
		$CustEmailTxt ="";
		$ManagerSMSTxt ="";
		$ManagerEmailTxt ="";
		$OfferSubject ="";
		$OfferSMSTxt ="";
		$OfferEmailTxt ="";
		$ManagerSubject ="";
		$CustSubject ="";
		
		$AccId = $outrow['AccountId'];
		$EntityId = $outrow['EntityId'];
					
		$PartialCommonStr .= "<Message>";
		$PartialCommonStr .= "<EntityId>" . $EntityId . "</EntityId>";
		$PartialCommonStr .= "<CampaignId></CampaignId>";
		$PartialCommonStr .= "<MsgType>General</MsgType>";
		$PartialCommonStr .= "<AcquireCust>No</AcquireCust>";
		$PartialCommonStr .= "<AuthKey>743A8F1B-C33F-48DC-9B3E-93BA4DD2E280</AuthKey>";
		$PartialCommonStr .= "<FollowTemplate>True</FollowTemplate>";*/
		
		
		/* Get messages from xml */
		/*$objDOM = new DOMDocument();
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
		
		switch ($Mode) 
		{
			case 'Book':
				
				$CustSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
												
				$CustEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 				
								
				$ManagerSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); */
				
				/*	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/	
				/*  GenerateOccasionString converts [[OccasionString]] to birthday, anniversary message. */ 	
				//$ManagerSMSTxt = $this->GenerateOccasionString($ManagerSMSTxt,$custrow);
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/
				
				/*$ManagerEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 	*/
				
				/*	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/
				//$ManagerEmailTxt = $this->GenerateOccasionString($ManagerEmailTxt,$custrow);							
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/				
				
				
				/*$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$ManagerSubject =  $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
				
				$OfferSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Booking")->item(0)->
				getElementsByTagName("Offer")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
				
				if($Mode == "Book" && $custrow['OfferId'] != "")
				{
					$OfferSMSTxt = $this->ResolveTags($custrow['OfferSMSText'],$custrow);
					$OfferEmailTxt = $this->ResolveTags($custrow['OfferEmailText'],$custrow);								
				}				
				
			
			
				break;
			case 'Amend':	
			case 'Amendep':

				$CustSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
												
				$CustEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 				
				
				$ManagerSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
				*/
				/*	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/
				//$ManagerSMSTxt = $this->GenerateOccasionString($ManagerSMSTxt,$custrow);
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/		
				
				//$ManagerEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				//getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				//->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 	
				
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/		
				//$ManagerEmailTxt = $this->GenerateOccasionString($ManagerEmailTxt,$custrow);
				/* ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	*/						
								
				/*$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$ManagerSubject =  $this->ResolveTags($Exact_Account->getElementsByTagName("Amend")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
				
				break;
			case 'Cancel':	

				$CustSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
												
				$CustEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 				
				
				$ManagerSMSTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("SMS")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 
				
				$ManagerEmailTxt = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Body")->item(0)->nodeValue,$custrow); 	
				
				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Customer")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$ManagerSubject =  $this->ResolveTags($Exact_Account->getElementsByTagName("Cancel")->item(0)->
				getElementsByTagName("Manager")->item(0)->getElementsByTagName("Email")->item(0)
				->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow);
			
				break; 
			case 'Checkin':	

				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("CheckIn")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$CustSMSTxt = $this->ResolveTags($custrow['CheckInSMSMessage'],$custrow); 
												
				$CustEmailTxt = $this->ResolveTags($custrow['CheckInEmailMessage'],$custrow); 				
							
				break;
			case 'Checkout':	

				$CustSubject = $this->ResolveTags($Exact_Account->getElementsByTagName("CheckOut")->item(0)->getElementsByTagName("Subject")->item(0)->nodeValue,$custrow); 
				
				$CustSMSTxt = $this->ResolveTags($custrow['CheckOutSMSMessage'],$custrow); 
												
				$CustEmailTxt = $this->ResolveTags($custrow['CheckOutEmailMessage'],$custrow); 				
							
				break;				
		}
		
		$CustomerString	= "";
		$CustMsgToString	= "";
		
		$CustomerString .= "<Text><![CDATA[".$CustSMSTxt."]]></Text>";
		$CustomerString .= "<EmailText><![CDATA[".$CustEmailTxt."]]></EmailText>";
		$CustomerString .= "<EmailSubject><![CDATA[".$CustSubject."]]></EmailSubject>";
		
		if($custrow['Cell_Number'] =="")
		{
			$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"\" CallingCode=\"\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
		}
		else
		{
			$CustMsgToString .= "<MessageTo FromEmail=\"".$outrow['EmailSenderEmailId']."\" FromName=\"".$outrow['EmailSenderName']."\" Seq=\"1\" CellNo=\"".$custrow['Cell_Number']."\" CallingCode=\"".$custrow['CountryCallingCode']."\" EmailId=\"".$custrow['CustomerEmail']."\" />";			
		}
				
		if($Mode == "Book" || $Mode == "Amend" || $Mode == "Amendep" || $Mode == "Cancel" || $Mode == "Checkin" || $Mode == "Checkout")
		{
			//Default customer msg
			$RetVal .= $PartialCommonStr;			
			$RetVal .= $CustomerString;	
			$RetVal .= $CustMsgToString;			
			$RetVal .= "</Message>";
			
			if($Mode != "Checkin" || $Mode != "Checkout")
			{
					$RetVal .= $PartialCommonStr;
					$RetVal .= "<Text><![CDATA[".$ManagerSMSTxt."]]></Text>";					
					$RetVal .= "<EmailText><![CDATA[".$ManagerEmailTxt."]]></EmailText>";
					$RetVal .= "<EmailSubject><![CDATA[".$ManagerSubject."]]></EmailSubject>";
										
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
			
			if($Mode == "Book" && $custrow['OfferId'] != "")
			{
				$RetVal .= $PartialCommonStr;
				$RetVal .= "<Text><![CDATA[".$OfferSMSTxt."]]></Text>";
				$RetVal .= "<EmailText><![CDATA[".$OfferEmailTxt."]]></EmailText>";
				$RetVal .= "<EmailSubject><![CDATA[".$OfferSubject."]]></EmailSubject>";
				$RetVal .= $CustMsgToString;			
				$RetVal .= "</Message>";			
			}
		}
				
		$objDOM = null;
		
		return $RetVal;
	}*/
	
}



?>