<?php

	require_once('config.php');
	require_once('paypal.php');
	$oldHeight = $_POST['oldHeight'];
	$totalDivHeight = 719;
	$ppAccount1 = new Paypal($api_username, $api_password, $api_signature, $api_url);
	$addedValue = file_get_contents($addBalanceFile);
	$response1 = $ppAccount1->call('GetBalance');
	
	if ($accounts == 2) {
		$ppAccount2 = new Paypal($api_username2, $api_password2, $api_signature2, $api_url);
		$response2 = $ppAccount2->call('GetBalance');
		$balance = $response2['L_AMT0'];
	}
	$balance += $response1['L_AMT0'] + $addedValue;
	$divHeight = ($totalDivHeight -($balance*0.007341))+48;

	$output = '<div class="thermoouter"><div class="thermoinner" id="thermo" style="position:static; top:0;
	width:300px; background-color: #008fd5; height:'.$oldHeight.'px; overflow:visible;"><img src="img/therm1.png" style="position:absolute;
	bottom: 0px; left: 0px; height:777px;width:300px;max-height:none;"/></div></div>';
	
	$output .= '<p class="balance">Total Raised: $' . number_format($balance,2,".",",") . '</p>';
	
	$return = ['height'=>$divHeight, 'html'=>$output, 'balance'=>$balance];
	header("Content-Type: application/json", true);
	echo json_encode($return);
?>