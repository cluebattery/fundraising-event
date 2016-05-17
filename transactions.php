<?php
/**
 *	Children's Discovery Museum of San Jose modified code from:
 *	
 *	An open source PHP library written to easily work with PayPal's API's
 *	Email:  service@angelleye.com
 *  Facebook: angelleyeconsulting
 *  Twitter: angelleye
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * @package			paypal-php-library
 * @author			Andrew Angell <service@angelleye.com>
 * @link			https://github.com/angelleye/paypal-php-library/
 * @website			http://www.angelleye.com
 * @support         http://www.angelleye.com/product/premium-support/
 * @version			v2.0.4
 * @filesource
*/
	require_once('config.php');
	require_once('autoload.php');
	$username=$api_username;
	$password=$api_password;
	$signature=$api_signature;
	$application=$application_id;
	$account = 'unset';
	
	if(isset($_POST['account']) && !empty($_POST['account'])) {
		$account = $_POST['account'];
		if ($account == '2') {  //2nd time through update-names.js
			if ($accounts == 2) {  //Checks 'accounts' in config.
				$username=$api_username2;
				$password=$api_password2;
				$signature=$api_signature2;
				$application=$application_id2;
			} else {
				die("");
			}
		}
	}
	$ticker_data = file($namesFile, FILE_IGNORE_NEW_LINES);
	
	// Create PayPal Object
	$PayPalConfig = array(
						'Sandbox' => $sandbox,
						'APIUsername' => $username,
						'APIPassword' => $password,
						'APISignature' => $signature, 
						'PrintHeaders' => $print_headers,
						'LogResults' => $log_results,
						'LogPath' => $log_path,
						);
	
	$PayPal = new angelleye\PayPal\PayPal($PayPalConfig);
	
	//'start' in config
	$StartDate = gmdate("Y-m-d\\TH:i:sZ",strtotime("now - $start day"));
	
	$TSFields = array(
						'startdate' => $StartDate, 					// Required.  The earliest transaction date you want returned.  Must be in UTC/GMT format.  2008-08-30T05:00:00.00Z
						'enddate' => '', 							// The latest transaction date you want to be included.
						'email' => '', 								// Search by the buyer's email address.
						'receiver' => '', 							// Search by the receiver's email address.  
						'receiptid' => '', 							// Search by the PayPal account optional receipt ID.
						'transactionid' => '', 						// Search by the PayPal transaction ID.
						'invnum' => '', 							// Search by your custom invoice or tracking number.
						'acct' => '', 								// Search by a credit card number, as set by you in your original transaction.  
						'auctionitemnumber' => '', 					// Search by auction item number.
						'transactionclass' => '', 					// Search by classification of transaction.  Possible values are: All, Sent, Received, MassPay, MoneyRequest, FundsAdded, FundsWithdrawn, Referral, Fee, Subscription, Dividend, Billpay, Refund, CurrencyConversions, BalanceTransfer, Reversal, Shipping, BalanceAffecting, ECheck
						'amt' => '', 								// Search by transaction amount.
						'currencycode' => '', 						// Search by currency code.
						'status' => '',  							// Search by transaction status.  Possible values: Pending, Processing, Success, Denied, Reversed
						'profileid' => ''							// Recurring Payments profile ID.  Currently undocumented but has tested to work.
					);
					
	$PayerName = array(
						'salutation' => '', 						// Search by payer's salutation.
						'firstname' => '', 							// Search by payer's first name.
						'middlename' => '', 						// Search by payer's middle name.
						'lastname' => '', 							// Search by payer's last name.
						'suffix' => ''	 							// Search by payer's suffix.
					);
	
	$PayPalRequest = array(
						   'TSFields' => $TSFields, 
						   'PayerName' => $PayerName
						   );
	
	$PayPalResult = $PayPal -> TransactionSearch($PayPalRequest);
	
	$transactions = $PayPalResult['SEARCHRESULTS'];
	
	//find each transactionID, and use GetTransactionDetails to find name and opt-in value
	
	foreach ($transactions as $key => $value) {
	
		$PayPalConfig = array(
			'Sandbox' => $sandbox,
			'APIUsername' => $username,
			'APIPassword' => $password,
			'APISignature' => $signature, 
			'PrintHeaders' => $print_headers,
			'LogResults' => $log_results,
			'LogPath' => $log_path,
			);
		
		$PayPal = new angelleye\PayPal\PayPal($PayPalConfig);
		
		$GTDFields = array(
			'transactionid' => $value['L_TRANSACTIONID']
			);
						
		$PayPalRequestData = array('GTDFields'=>$GTDFields);
		
		// Pass data into class for processing with PayPal and load the response array into $transactionResult
		$transactionResult = $PayPal->GetTransactionDetails($PayPalRequestData);

		if (isset($transactionResult['ORDERITEMS']['0']['L_OPTIONSNAME'])) {
				if ($transactionResult['ORDERITEMS']['0']['L_OPTIONSNAME'] == "Opt In") {
					//this is a transaction from the web form
					if ($transactionResult['ORDERITEMS']['0']['L_OPTIONSVALUE'] == 'Yes' || $transactionResult['ORDERITEMS']['0']['L_OPTIONSVALUE'] == 'yes') {
						if (isset($transactionResult['LASTNAME']) && !empty($transactionResult['LASTNAME'])) {
								array_push($ticker_data, strtoupper($transactionResult['FIRSTNAME']).' '.strtoupper($transactionResult['LASTNAME']));
						}
					}
			} else {
				if (isset($transactionResult['INVNUM'])) {
					//this is a transaction from the PayPal Here app
		
					$PayPalConfig = array(
							'Sandbox' => $sandbox,
							'DeveloperAccountEmail' => $developer_account_email,
							'ApplicationID' => $application,
							'DeviceID' => $device_id,
							'IPAddress' => $_SERVER['REMOTE_ADDR'],
							'APIUsername' => $username,
							'APIPassword' => $password,
							'APISignature' => $signature,
							'APISubject' => $api_subject,
							'PrintHeaders' => $print_headers, 
							'LogResults' => $log_results, 
							'LogPath' => $log_path,
										);
					
					$PayPal = new angelleye\PayPal\Adaptive($PayPalConfig);
					
					// Pass data into class for processing with PayPal and load the response array into $invoiceResult
					$InvoiceID = $transactionResult['INVNUM'];
					$invoiceResult = $PayPal->GetInvoiceDetails($InvoiceID);
					//echo '<pre>';
					//print_r($invoiceResult);
					if ($invoiceResult['InvoiceItems']['0']['Description'] == 'Yes') {
						if (isset($transactionResult['LASTNAME']) && !empty($transactionResult['LASTNAME'])) {
							array_push($ticker_data, strtoupper($transactionResult['FIRSTNAME']).' '.strtoupper($transactionResult['LASTNAME']));
						} else if (isset($invoiceResult['BillingFirstName']) && !empty($invoiceResult['BillingFirstName'])) {
							array_push($ticker_data, strtoupper($invoiceResult['BillingFirstName']).' '.strtoupper($invoiceResult['BillingLastName']));
						}
					}
				}
				 
			}
		}
		//print_r($transactionResult)
		//echo '</pre>';
		
	}
	
	//print_r(array_values($ticker_data));
	file_put_contents($namesFile, implode(PHP_EOL, array_unique($ticker_data)));
?>