<?php
	$lat0	= $_GET['koord_user_lat'];
	$lng0	= $_GET['koord_user_lng'];
	$lat1	= $_GET['koord_destination_lat'];
	$lng1	= $_GET['koord_destination_lng'];
	
	$json_url = 'http://localhost/webserver/contoh_json/create_json.php?koord_user_lat='.$lat0.'&koord_destination_lat='.$lat1.'&koord_destination_lng='.$lng1.'';
	$ch = curl_init ($json_url);
	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER => array('Content-type: application/json'),
	);
	curl_setopt_array ($ch, $options); // setting curl options
	$result = curl_exec($ch); // getting json result string	
?>