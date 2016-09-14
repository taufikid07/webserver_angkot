<?php
/**
# DISTANCE TWO COORDINATES
# @RETURN metres
*/
class DistanceTo{	
	function distanceTo($lat1, $lng1, $lat2, $lng2) 
	{
		set_time_limit(0);
		$earthRadius = 3958.75;

		$dLat = deg2rad($lat2-$lat1);
		$dLng = deg2rad($lng2-$lng1);

		$a = 	sin($dLat/2) * sin($dLat/2) +
				cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
				sin($dLng/2) * sin($dLng/2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));
		$dist = $earthRadius * $c;

		// from miles
		$meterConversion = 1609;
		$geopointDistance = $dist * $meterConversion;
		/*$lat1="-6.940178";
		$lng1="107.6671";
		$lat2="-6.945595";
		$lng2="107.643455";
		$q = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$lng1."&destinations=".$lat2.",".$lng2."&mode=driving&sensor=false";
		$json = file_get_contents($q);
		$details = json_decode($json, TRUE);
		$details=$details['rows'][0]['elements'][0]['distance']['value'];
		$geopointDistance=$details;*/
		
		/*
		//Our starting point / origin. Change this if you wish.
$start = "-6.941830, 107.655430";

//Our end point / destination. Change this if you wish.
$destination = "-6.945986, 107.642899";

//The Google Directions API URL. Do not change this.
$apiUrl = 'http://maps.googleapis.com/maps/api/directions/json';

//Construct the URL that we will visit with cURL.
$url = $apiUrl . '?' . 'origin=' . urlencode($start) . '&destination=' . urlencode($destination);

//Initiate cURL.
$curl = curl_init($url);

//Tell cURL that we want to return the data.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//Execute the request.
$res = curl_exec($curl);

//If something went wrong with the request.
if(curl_errno($curl)){
    throw new Exception(curl_error($curl));
}

//Close the cURL handle.
curl_close($curl);

//Decode the JSON data we received.
$json = json_decode(trim($res), true);

//Automatically select the first route that Google gave us.
$route = $json['routes'][0];

//Loop through the "legs" in our route and add up the distances.
$totalDistance = 0;
foreach($route['legs'] as $leg){
    $totalDistance = $totalDistance + $leg['distance']['value'];
}

//Divide by 1000 to get the distance in KM.
$totalDistance = round($totalDistance);
$geopointDistance=round($totalDistance);
//Print out the result.
echo 'Total distance is ' . $totalDistance . 'km' ;

//var_dump the original array, for illustrative purposes.
//var_dump($json);
		*/
		return $geopointDistance;
	}	
}


