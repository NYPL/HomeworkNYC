
<?php
        //this is ip authentication for gale
        $ip=getRealIpAddr();
        $url=$_GET['link'];
		
		$gale_url ='http://galesupport.com/nygeoipcheck/nygeoipcheck.php?ip='.$ip;
		$str = file_get_contents($gale_url,0);
		
		$status=split(',' , $str);
		
	    if($status[0]=='yes') {
			//this is locId
			$pattern = '/youraccountid/';
			// replace the locID with locID/password
            $add_auth = 'youraccountid%2Fyourpassword';
            $url=preg_replace($pattern,$add_auth,$url);

			// redirect to authenticated URL
			echo("<script>location.href = '". $url."';</script>");
				  

		} else {
				  // geoLocation Failed, redirect to unauthenticated URL
				  // user will be prompted for barcode or password
				   echo("<script>location.href = '". $url."';</script>");
				  
		}
		
		function getRealIpAddr()
		{
			if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
			{
			  $ip=$_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
			{
			  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
			  $ip=$_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}


?>