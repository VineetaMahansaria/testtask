<?php

function getData($countryCode,$addressLocality,$postalCode) {
 
	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL => "https://api-sandbox.dhl.com/location-finder/v1/find-by-address?countryCode=$countryCode&addressLocality=$addressLocality&postalCode=$postalCode",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => [
			"DHL-API-Key: demo-key"
		],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		return "CURL Error #:" . $err;
	} else {
		return $response; exit;
	}
}