<?php
// OAuth API
require "OAuth.php";

// Put here your authentication keys
// consume key 
$cc_key  = "XXXXXXXXXX";
// secret key
$cc_secret = "YYYYYYYYYYY";

// Coreo auth validator
$url = "http://gadget.coreo.net.br/GadgetValidator/gadgetValidator";
$args = array();

// Authenticate on Coreo and get the access token
$consumer = new OAuthConsumer( $cc_key, $cc_secret );
$request = OAuthRequest::from_consumer_and_token( $consumer, NULL, "GET", $url, $args );
$request->sign_request( new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL );
$url = sprintf( "%s?%s", $url, OAuthUtil::build_http_query( $args ) );
$ch = curl_init();
$headers = array( $request->to_header() );
curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
$rsp = curl_exec( $ch ); // Coreo access token

// Coreo application engine WSDL
$client = new SoapClient( 'http://services.coreo.net.br/services/AppEngineServerMediator?wsdl' );

// Default Coreo operation
$function = 'execute';

// Define application parameters
$arguments= array( 'execute' => array(
		'token'      => $rsp,
		'appId'     => 'a92dd01a-c17f-4664-abba-cbf52d2d3b51',
		'parameters'        => '<request><gadget-input-1>3496948012</gadget-input-1><gadget-input-2>Sending SMS from PHP</gadget-input-2></request>'
	) );

// Coreo application engine endpoint
$options = array( 'location' => 'http://services.coreo.net.br/services/AppEngineServerMediator' );

// Call Coreo application
$result = $client->__soapCall( $function, $arguments, $options );
?>