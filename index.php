<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<meta charset="utf-8">
<style>
html, body, #map-canvas {
	width: 90%;
	height: 90%;
	//margin: 0px;
	//padding: 0px
}
a{
	cursor: pointer;
	text-decoration: underline;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyC4o_OEWdGpA87t3nlADDAEE3P90vf8GU0&sensor=false&libraries=places"></script>
<script>
// map
var poly = '';
var map;
var markeruser = '';
var markerdestination = '';

// boolean
var __global_user		 = false;
var __global_destination = false;
var update_timeout;

// temporary list angkot
var temp_list_angkot = [];

/**
* INITIALIZE GOOGLE MAP
*/
var directionsDisplay,directionsDisplay2;
var directionsService = new google.maps.DirectionsService();
var directionsService2 = new google.maps.DirectionsService();
var directionsService3 = new google.maps.DirectionsService(); var directionsDisplay3;
function initialize() {	
	/* setup map */
	//directionsDisplay = new google.maps.DirectionsRenderer();
	//directionsDisplay2 = new google.maps.DirectionsRenderer();
	var mapOptions = {
		zoom: 13,
		center: new google.maps.LatLng(-6.921986, 107.618969)
	};
	
	
	
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    //directionsDisplay.setMap(map);
	//directionsDisplay2.setMap(map);
	//directionsDisplay3.setMap(map);
	//directionsDisplay3.setOptions( { suppressMarkers: true } );
	//directionsDisplay.setOptions( { suppressMarkers: true } );
	//directionsDisplay2.setOptions( { suppressMarkers: true } );
	directionsDisplay = new google.maps.DirectionsRenderer({
                                      map:map,suppressMarkers: true,
                                      polylineOptions:{
                                        strokeColor:'red'
                                      }});
	directionsDisplay2 = new google.maps.DirectionsRenderer({
                                      map:map,suppressMarkers: true,
                                      polylineOptions:{
                                        strokeColor:'red'
                                      }});
	
	/* create marker and line by click */
	google.maps.event.addListener(map, 'click', function(event) 
	{	
		icons = 'http://latcoding.com/domains/dijkstra.latcoding.com/imgs/user_min.png';
		var location = event.latLng;	

		update_timeout = setTimeout(function()
		{
			if(__global_user == false){
				markeruser = new google.maps.Marker({
					position: location,
					map: map,
					icon: icons,
					draggable: true,
					title: 'test drag',
				});
				
				// update 
				__global_user = true;
			}else{
				markeruser.setPosition(location);
			}

		}, 200); 

	});	

	// handle click and dblclick same time
	google.maps.event.addListener(map, 'dblclick', function(event) {       
		clearTimeout(update_timeout);
	});	
}

/** 
* PILIH DESTINATION (USER) VIA <SELECT>
*/
function choose_destination(value){
	// teks option
	var teks = $("#select_tujuan option:selected").text();
	
	// -- PILIH -- dipilih
	if(value == 'pilih') return false;
	
	// reset polyline
	if(poly != '') poly.setMap(null);
	
	// RESET ANGKOT SEBELUMNYA
	$(temp_list_angkot).each(function(w, x){
		// x = marker0, marker1 dst
		window[x].setMap(null);
	});			
	
	var location = JSON.parse(value);
	icons = 'http://latcoding.com/domains/dijkstra.latcoding.com/imgs/user_min.png';
	if(__global_destination == false){
		markerdestination = new google.maps.Marker({
			position: location,
			map: map,
			icon: icons,
			draggable: false,
			title: 'TUJUAN : ' + teks,
		});
		
		__global_destination = true;
	}else{
		markerdestination.setPosition(location);
		markerdestination.setTitle('TUJUAN : ' + teks);
	}
}

/**
* GET JSON DIJSKTRA VIA AJAX
*/
function send_dijkstra(){	
	if(markeruser == '' || markerdestination == ''){
		alert('Isi dulu koordinat user & tujuan');
		return false;
	}
	
	console.log(markeruser.position.lat());
	console.log(markeruser.position.lng());
	now_koord_user 			= '{"lat": ' + markeruser.position.lat() + ', "lng": ' + markeruser.position.lng() + '}';
	now_koord_destination 	= '{"lat": ' + markerdestination.position.lat() + ', "lng": ' + markerdestination.position.lng() + '}';

	// loading
	$('#run_dijkstra').hide();
	$('#loading').show();
	
	$.ajax({
		method:"POST",
		url : "Main.php",
		data: {koord_user: now_koord_user, koord_destination: now_koord_destination},
		success:function(response){
			
			// remove loading
			$('#run_dijkstra').show();
			$('#loading').hide();
						
			var json = JSON.parse(response);
			console.log(json); 
			//var re=JSON.stringify(response);
			var key, count = 0;
			for(key in json.jalur_shortest_path) {
			  if(json.jalur_shortest_path.hasOwnProperty(key)) {
				count++;
				
			  }
			}
			console.log(count-1);
			
			console.log(json.jalur_shortest_path[count-1]);    
			var rg=json.jalur_shortest_path[count-1];
			var stringRepresentation2=JSON.stringify(rg);
		    console.log(stringRepresentation2);
				
				
				
			// RESET POLYLINE
			if(poly != '') poly.setMap(null);
			
			// RESET ANGKOT SEBELUMNYA
			$(temp_list_angkot).each(function(w, x){
				// x = marker0, marker1 dst
				window[x].setMap(null);
			});

			// ERROR ALGORITMA DIJKSTRA
			if(json.hasOwnProperty("error")) alert(json['error']['teks']);
			
			// GAMBAR JALUR SHORTEST PATH
			/* setup polyline */
			var polyOptions = {				
				/*path: [
				{"lat": 37.772, "lng": -122.214},
				{"lat": 21.291, "lng": -157.821},
				{"lat": -18.142, "lng": 178.431},
				{"lat": -27.467, "lng": 153.027}],
				*/
				path: json['jalur_shortest_path'],
				geodesic: true,
				strokeColor: 'rgb(20, 120, 218)',
				strokeOpacity: 1.0,
				strokeWeight: 2
			};			
			poly = new google.maps.Polyline(polyOptions);
			poly.setMap(map);
			
		
			// GAMBAR KOORDINAT ANGKOT
			var stringRepresentation;
			$(json['angkot']).each(function(i, v)
			{
				//console.log(v)
				// no_angkot
				no_angkot = JSON.stringify(v['no_angkot']);
				window['infowindow'+i] = new google.maps.InfoWindow({
					content: '<div>'+ no_angkot +'</div>'
				});
				
				// koordinat angkot
				koordinat_angkot = v['koordinat_angkot'];
				stringRepresentation=JSON.stringify(v['koordinat_angkot']);
				
				window['marker'+i] = new google.maps.Marker({
					position: koordinat_angkot,
					map: map,
					title: 'title',
					icon: 'http://latcoding.com/free_download/implementasi_dijkstra_di_android/car.png'
					
				});
				
				// popup
				window['marker'+i].addListener('click', function() {
					window['infowindow'+i].open(map, window['marker'+i]);
				});
				
				// temporary list angkot
				temp_list_angkot[i] = 'marker'+i;
				
				
			
				
			});
			
            var e = stringRepresentation.split(",");
			var d1=e[0].replace("{", "");
			var f1=e[1].replace("}", "");
			var e1=d1.replace(":", "");
			var e2=f1.replace(":", "");	
			var e3=e1.replace("lat", "");
			var e4=e2.replace("lng", "");	
			var e5=e3.replace(/"/g, "");
			var e6=e4.replace(/"/g, "");			
				//console.log(e5+'-'+e6);			
			var a=markeruser.position.lat(),s=markeruser.position.lng(),d=parseFloat(e5),f=parseFloat(e6);
			
			/*var polyOptions2 = {				
				path: [
				{"lat": d, "lng": f},
				{"lat": a, "lng":s }],
				geodesic: true,
				strokeColor: 'rgb(20, 120, 218)',
				strokeOpacity: 1.0,
				strokeWeight: 2,
			};			
			poly = new google.maps.Polyline(polyOptions2);
			poly.setMap(map);*/
			
			  // var source="-6.9141006990571,107.64031671262";
             // var   destination="-6.9141772413576,107.6347450912";
			// console.log(a+","+s,d,f);
			var x=a+","+s;
			var y=d+","+f;
			
			
			
			var e1 = stringRepresentation2.split(",");
			var d11=e1[0].replace("{", "");
			var f11=e1[1].replace("}", "");
			var e11=d11.replace(":", "");
			var e21=f11.replace(":", "");	
			var e31=e11.replace("lat", "");
			var e41=e21.replace("lng", "");	
			var e51=e31.replace(/"/g, "");
			var e61=e41.replace(/"/g, "");			
			var a8=markerdestination.position.lat(),s8=markerdestination.position.lng(),d8=parseFloat(e51),f8=parseFloat(e61);
			
			var xx=a8+","+s8;
			var yy=d8+","+f8;
			calcRoute(a,s,d,f);
			calcRoute2(a8,s8,d8,f8);
			//console.log(a8+","+s8,d8,f8);
		},
		error:function(er){
			alert('error: '+er);
			
			// remove loading
			$('#run_dijkstra').show();
			$('#loading').hide();
		}
	});        
}

// NEW TRANSIT
function initMap() {
  var directionsDisplay = new google.maps.DirectionsRenderer;
  var directionsService = new google.maps.DirectionsService;
  var map = new google.maps.Map(document.getElementById('map'), {
	});
  directionsDisplay.setMap(map);

  calculateAndDisplayRoute(directionsService, directionsDisplay);
  document.getElementById('select_tujuan').addEventListener('change', function() {
    calculateAndDisplayRoute(directionsService, directionsDisplay);
  });
}

function calculateAndDisplayRoute(directionsService, directionsDisplay) {
  var selectedMode = document.getElementById('select_tujuan').value;
  directionsService.route({
	//origin: {lat: "+ markeruser.position.lat()+", lng:"+ markeruser.position.lng() +"},
	//destination 	: '{'lat': ' + markerdestination.position.lat() + ', 'lng: ' + markerdestination.position.lng() + '}',
    origin: new google.maps.LatLng(r, g),
	destination: new google.maps.LatLng(rr, gg),
    // Note that Javascript allows us to access the constant
    // using square brackets and a string value as its
    // "property."
    travelMode: google.maps.TravelMode[selectedMode]
  }, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      directionsDisplay.setDirections(response);
    } else {
      window.alert('Directions request failed due to ' + status);
    }
  });
}

function calcRoute(r,g,rr,gg) {
		google.maps.Polyline.prototype.setMap=(function(f,r){
              return function(map){
			  if(
				this.get('icons')
				  &&
				this.get('icons').length===1
				  &&
				this.get('strokeOpacity')===0
				  &&
				!this.get('noRoute')
			  ){
				if(r.get('polylineOptions')&& r.get('polylineOptions').strokeColor){
				  
				  var icons=this.get('icons'),
					  color=r.get('polylineOptions').strokeColor;
				  icons[0].icon.fillOpacity=1;
				  icons[0].icon.fillColor=color;
				  icons[0].icon.strokeColor=color;
				  this.set('icons',icons);
			  }}
			f.apply(this,arguments);
		  }
		  
		 })(
          google.maps.Polyline.prototype.setMap,
          directionsDisplay);
    
  
			directionsService.route({
			origin: new google.maps.LatLng(r, g),
			destination: new google.maps.LatLng(rr, gg),
			travelMode: google.maps.TravelMode.TRANSIT
		  }, function(response, status) {
			if (status === google.maps.DirectionsStatus.OK) {
			  directionsDisplay.setDirections(response);
			} else {
			  window.alert('Directions request failed due to ' + status);
			}
		  });
}


function calcRoute2(r22,g22,rr22,gg22) {
              // var r22="-6.915435366792156,107.64026641845703";
              // var g22="-6.9141006990571,107.64031671262";
			  google.maps.Polyline.prototype.setMap=(function(f,r){
      
			return function(map){
			  if(
				this.get('icons')
				  &&
				this.get('icons').length===1
				  &&
				this.get('strokeOpacity')===0
				  &&
				!this.get('noRoute')
			  ){
				if(r.get('polylineOptions')&& r.get('polylineOptions').strokeColor){
				  
				  var icons=this.get('icons'),
					  color=r.get('polylineOptions').strokeColor;
				  icons[0].icon.fillOpacity=1;
				  icons[0].icon.fillColor=color;
				  icons[0].icon.strokeColor=color;
				  this.set('icons',icons);
			  }}
			f.apply(this,arguments);
		  }
		  
		 })(
          google.maps.Polyline.prototype.setMap,
          directionsDisplay2);
			directionsService2.route({
			origin: new google.maps.LatLng(rr22, gg22),
			destination: new google.maps.LatLng(r22, g22),
			travelMode: google.maps.TravelMode.TRANSIT
		  }, function(response, status) {
			if (status === google.maps.DirectionsStatus.OK) {
			  directionsDisplay2.setDirections(response);
			} else {
			  window.alert('Directions request failed due to ' + status);
			}
		  });
}
/* load google maps v3 */
google.maps.event.addDomListener(window, 'load', initialize);
</script>

<?php
include "Main.php";

// koneksi
$m = new Main();
$koneksi = $m->koneksi;

// query
$sql 	= "SELECT * FROM jalan";
$query 	= mysqli_query($koneksi, $sql);

// select option
echo 'TUJUAN : <select id="select_tujuan" onchange="choose_destination(this.value)">';
echo '<option value="pilih">-- PILIH --</option>';
	while($fetch = mysqli_fetch_array($query, MYSQLI_ASSOC))
	{
		$koordinat 		= $fetch['koordinat'];
		$exp_koordinat 	= explode(',', $koordinat);
		$json_koordinat	= '{"lat": '.$exp_koordinat[0].', "lng": '.$exp_koordinat[1].'}';
		
		echo "<option value='$json_koordinat'>$fetch[tujuan]</option>";
	}
echo '</select>';
?>
<span><button onclick="send_dijkstra()" id='run_dijkstra'>RUN</button><span id='loading' style='display:none'>membuat rute ..</span></span>
<div id="map-canvas" style="float:left;"></div>
<div id='DEBUG'></div>	
<table>
<tr>
            <td colspan="2">
                <div id="dvDistance">
                </div>
            </td>
        </tr>
</table>
