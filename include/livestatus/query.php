<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.0
# APPLICATION : eonweb for eyesofnetwork project
#
# LICENCE :
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
#########################################
*/

require_once("../config.php");
require_once("../arrays.php");
require_once("../function.php");  
require_once("Client.php");
use Nagios\Livestatus\Client;

// define the action to do
if( !empty($_GET["action"]) )
{
	switch($_GET["action"])
	{
		case "services_state": getServicesStateNbr(); break;
		case "hosts_state": getHostsStateNbr(); break;
		case "event_state": getEventStateNbr(); break;
		case "event_state_time": getNumberEventByStateAndTime(); break;
	}
}

function checkHost($type, $address, $port, $path){
	$host = false;
        if($type == "unix"){
                $socket_path_connexion = "unix://".$path;
                $host = fsockopen($socket_path_connexion, $port, $errno, $errstr, 5);
        }
        else{
                $host = fsockopen($address, $port, $errno, $errstr, 5);
        }
	return $host;
	
}

/**
 * Get the number of services, ordered by state, and according to the logged user
 */
function getServicesStateNbr()
{
global $sockets;

$result = array();
$nbr_services_ok = 0;
$nbr_services_warning = 0;
$nbr_services_critical = 0;
$nbr_services_unknown = 0;

foreach($sockets as $socket){
	$socket_parts = explode(":", $socket);
        $socket_type = $socket_parts[0];
        $socket_address = $socket_parts[1];
        $socket_port = $socket_parts[2];
        $socket_path = $socket_parts[3];
	if( checkHost($socket_type, $socket_address, $socket_port, $socket_path) ){
		if($socket_port == -1){
			$socket_port = "";
			$socket_address = "";
		}
		$options = array(
                	'socketType' => $socket_type,
                	'socketAddress' => $socket_address,
                	'socketPort' => $socket_port,
                	'socketPath' => $socket_path,
        	);
		// construct mklivestatus request, and get the response
        	$client = new Client($options);
        	// construct mklivestatus request, and get the response
		$response = $client
                ->get('services')
                        ->stat('state = 0')
                        ->stat('state = 1')
                        ->stat('state = 2')
                        ->stat('state = 3')
                        ->filter('contacts >= '. $_SERVER["REMOTE_USER"])
                ->execute();
                $nbr_services_ok += $response[0][0];
                $nbr_services_warning += $response[0][1];
                $nbr_services_critical += $response[0][2];
                $nbr_services_unknown += $response[0][3];	
	}
}

	// fill an empty array with previous response, in order to have a beautiful JSON to use
	array_push($result, $nbr_services_ok);
	array_push($result, $nbr_services_warning);
	array_push($result, $nbr_services_critical);
	array_push($result, $nbr_services_unknown);
	
	$client->command(
    array(
        'ACKNOWLEDGE_SVC_PROBLEM',
        'example.com',
        'some service', 2, 0, 1,
        'username', 'Example comment'));
	
	// response for the Ajax call
	echo json_encode($result);
}

/**
 * Get the number of hosts, ordered by state, and according to the logged user
 */
function getHostsStateNbr()
{
global $sockets;

$result = array();
$nbr_host_ok = 0;
$nbr_host_warning = 0;
$nbr_host_critical = 0;
$nbr_host_unknown = 0;

foreach($sockets as $socket){
        $socket_parts = explode(":", $socket);
        $socket_type = $socket_parts[0];
        $socket_address = $socket_parts[1];
        $socket_port = $socket_parts[2];
        $socket_path = $socket_parts[3];
        if( checkHost($socket_type, $socket_address, $socket_port, $socket_path) ){
                if($socket_port == -1){
                        $socket_port = "";
                        $socket_address = "";
                }
                $options = array(
                        'socketType' => $socket_type,
                        'socketAddress' => $socket_address,
                        'socketPort' => $socket_port,
                        'socketPath' => $socket_path,
                );
                // construct mklivestatus request, and get the response
                $client = new Client($options);
                // construct mklivestatus request, and get the response
                $response = $client
                ->get('hosts')
                        ->stat('state = 0')
                        ->stat('state = 1')
                        ->stat('state = 2')
                        ->stat('state = 3')
                        ->filter('contacts >= '. $_SERVER["REMOTE_USER"])
                ->execute();
                $nbr_host_ok += $response[0][0];
                $nbr_host_warning += $response[0][1];
                $nbr_host_critical += $response[0][2];
                $nbr_host_unknown += $response[0][3];
        }
}	

	array_push($result, $nbr_host_ok);
	array_push($result, $nbr_host_warning);
	array_push($result, $nbr_host_critical);
	array_push($result, $nbr_host_unknown);

	// fill an empty array with previous response, in order to have a beautiful JSON to use
	$client->command(
    array(
        'ACKNOWLEDGE_SVC_PROBLEM',
        'example.com',
        'some service', 2, 0, 1,
        'username', 'Example comment'));
	
	// response for the Ajax call
	echo json_encode($result);
}

/**
 * Get number of event ordered by state (in DB GED), according to the default filter if there is.
 */
function getEventStateNbr()
{
	global $database_ged;
	
	$default = false;
	// define and load the right xml file
	$file = '../../cache/'.$_COOKIE['user_name'].'-ged.xml';
	if(file_exists($file)){
		$xmlfilters = new DOMDocument("1.0","UTF-8");
		$xmlfilters->load($file);
		$g = $xmlfilters->getElementsByTagName("ged")->item(0);
		
		//Default filter detection
		$default = $g->getElementsByTagName("default")->item(0)->nodeValue;
		$xpath = new DOMXpath($xmlfilters);
		$filters = $xpath->query("//ged/filters[@name='$default']/filter");
	}
	
	// will be filled during the "for loop"
	$result = array();
	$nbr_ok = 0;
	$nbr_warning = 0;
	$nbr_critical = 0;
	$nbr_unknown = 0;
	
	$pkt_type = sqlrequest("ged", "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id != 0 AND pkt_type_id < 100");
	while($row = mysqli_fetch_row($pkt_type))
	{
		$unknown = 0;
		// will construct the SQL request
		$requete = "SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ";
		if($default)
		{
			// construct the WHERE clause foreach <filter> in the XML file
			$i = 1;
			foreach($filters as $filter)
			{
				$name = $filter->getAttribute('name');
				$text = $filter->nodeValue;
				
				if($i < $filters->length){
					switch($name){
						case 'not_equipment': $name = "equipment"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_service': $name = "service"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_description': $name = "description"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_hostgroups': $name = "hostgroups"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_servicegroups': $name = "servicegroups"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_owner': $name = "owner"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						default: $requete .= $name . " LIKE '%".$text."%' AND ";
					}
				}
				else{
					switch($name){
						case 'not_equipment': $name = "equipment"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_service': $name = "service"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_description': $name = "description"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_hostgroups': $name = "hostgroups"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_servicegroups': $name = "servicegroups"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						case 'not_owner': $name = "owner"; $requete .= "$name NOT LIKE '%".$text."%' AND "; break;
						default: $requete .= $name . " LIKE '%".$text."%' AND ";
					}
				}
				$i++;
			}
		}
		
		// foreach event state (0 to 3), will finish the SQL request, and get a result in order to fill the empty array
		for($i = 0; $i <= 3; $i++)
		{
			$requete_finale = $requete . "state = ". $i;
			//echo "<br>" . $requete_finale . "<br>";
			$query_result = sqlrequest($database_ged, $requete_finale);
			switch($i)
			{
				case 0: $nbr_ok += intval(mysqli_result($query_result, 0, 0)); break;
				case 1: $nbr_warning += intval(mysqli_result($query_result, 0, 0)); break;
				case 2: $nbr_critical += intval(mysqli_result($query_result, 0, 0)); break;
				case 3: $nbr_unknown += intval(mysqli_result($query_result, 0, 0)); break;
			}
		}

	}
	
	// push the final results into the json array
	array_push($result, $nbr_ok);
	array_push($result, $nbr_warning);
	array_push($result, $nbr_critical);
	array_push($result, $nbr_unknown);
	
	// response for the Ajax call
	echo json_encode($result);
}

/**
 * Sort events by time range : 
 * - event ok
 * - event not ok 
 * - event critical or unknown owned
 * - event critical or unknown not owned
 * and according to a ged filter if by default
 */
function getNumberEventByStateAndTime()
{
	global $database_ged;

	$default = false;
	// define and load the right xml file
	$file = '../../cache/'.$_COOKIE['user_name'].'-ged.xml';
	if(file_exists($file)){
		$xmlfilters = new DOMDocument("1.0","UTF-8");
		$xmlfilters->load($file);
		$g = $xmlfilters->getElementsByTagName("ged")->item(0);
		
		//Default filter detection
		$default = $g->getElementsByTagName("default")->item(0)->nodeValue;
		$xpath = new DOMXpath($xmlfilters);
		$filters = $xpath->query("//ged/filters[@name='$default']/filter");
	}
	
	// define all times needed (for each range)
	$actual_time = time();
	$five_minutes = $actual_time - (60 * 5);
	$fifteen_minutes = $actual_time - (60 * 15);
	$thirty_minutes = $actual_time - (60 * 30);
	$one_hour = $actual_time - (60 * 60);
	
	
	$milieu_requete = "";
	if($default)
	{
		$i = 1;
		foreach($filters as $filter)
		{
			$name = $filter->getAttribute('name');
			$text = $filter->nodeValue;
			
			// construct the WHERE clause
			if($i < $filters->length){
				switch($name){
					case 'not_equipment': $name = "equipment"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND "; break;
					case 'not_service': $name = "service"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND "; break;
					case 'not_description': $name = "description"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND "; break;
					case 'not_hostgroups': $name = "hostgroups"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND "; break;
					case 'not_servicegroups': $name = "servicegroups"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND "; break;
					case 'not_owner': $name = "owner"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND "; break;
					default: $milieu_requete .= $name . " LIKE '%".$text."%' AND ";
				}
			}
			else{
				switch($name){
					case 'not_equipment': $name = "equipment"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND"; break;
					case 'not_service': $name = "service"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND"; break;
					case 'not_description': $name = "description"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND"; break;
					case 'not_hostgroups': $name = "hostgroups"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND"; break;
					case 'not_servicegroups': $name = "servicegroups"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND"; break;
					case 'not_owner': $name = "owner"; $milieu_requete .= "$name NOT LIKE '%".$text."%' AND"; break;
					default: $milieu_requete .= $name . " LIKE '%".$text."%' AND";
				}
			}
			$i++;
		}
	}
	
	$result = array();
	$result_0_5 = array();
	$result_5_15 = array();
	$result_15_30 = array();
	$result_30_1h = array();
	$result_1h = array();
	
	$cpt = 0;
	$pkt_type = sqlrequest($database_ged, "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id != 0 AND pkt_type_id < 100");
	while($row = mysqli_fetch_row($pkt_type))
	{
		$temp_result_0_5 = array();
		$temp_result_5_15 = array();
		$temp_result_15_30 = array();
		$temp_result_30_1h = array();
		$temp_result_1h = array();
		
		$sql = "
		SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state=0 AND o_sec <= ". $actual_time ." AND o_sec > ". $five_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state!=0 AND o_sec <= ". $actual_time ." AND o_sec > ". $five_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner='' AND o_sec <= ". $actual_time ." AND o_sec > ". $five_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner!='' AND o_sec <= ". $actual_time ." AND o_sec > ". $five_minutes ."
					UNION ALL
		SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state=0 AND o_sec <= ". $five_minutes ." AND o_sec > ". $fifteen_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state!=0 AND o_sec <= ". $five_minutes ." AND o_sec > ". $fifteen_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner='' AND o_sec <= ". $five_minutes ." AND o_sec > ". $fifteen_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner!='' AND o_sec <= ". $five_minutes ." AND o_sec > ". $fifteen_minutes ."
					UNION ALL
		SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state=0 AND o_sec <= ". $fifteen_minutes ." AND o_sec > ". $thirty_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state!=0 AND o_sec <= ". $fifteen_minutes ." AND o_sec > ". $thirty_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner='' AND o_sec <= ". $fifteen_minutes ." AND o_sec > ". $thirty_minutes ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner!='' AND o_sec <= ". $fifteen_minutes ." AND o_sec > ". $thirty_minutes ."
					UNION ALL
		SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state=0 AND o_sec <= ". $thirty_minutes ." AND o_sec > ". $one_hour ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state!=0 AND o_sec <= ". $thirty_minutes ." AND o_sec > ". $one_hour ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner='' AND o_sec <= ". $thirty_minutes ." AND o_sec > ". $one_hour ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner!='' AND o_sec <= ". $thirty_minutes ." AND o_sec > ". $one_hour ."
					UNION ALL
		SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state=0 AND o_sec <= ". $one_hour ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state!=0 AND o_sec <= ". $one_hour ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner='' AND o_sec <= ". $one_hour ."
		UNION ALL SELECT COUNT(*) FROM ".$row[0]."_queue_active WHERE ".$milieu_requete." state IN (1,2,3) AND owner!='' AND o_sec <= ". $one_hour;

		$query_result = sqlrequest($database_ged, $sql);
		
		if($cpt == 0)
		{
			for($i = 0; $i < $query_result->num_rows; $i++)
			{
				if($i >= 0 && $i < 4) { array_push($result_0_5, intval(mysqli_result($query_result, $i, 0))); }
				elseif($i >= 4 && $i < 8) { array_push($result_5_15, intval(mysqli_result($query_result, $i, 0))); }
				elseif($i >= 8 && $i < 12) { array_push($result_15_30, intval(mysqli_result($query_result, $i, 0))); }
				elseif($i >= 12 && $i < 16){	array_push($result_30_1h, intval(mysqli_result($query_result, $i, 0))); }
				elseif($i >= 16 && $i < 20){	array_push($result_1h, intval(mysqli_result($query_result, $i, 0))); }
			}
		}
		else
		{
			for($i = 0; $i < $query_result->num_rows; $i++)
			{
				if($i >= 0 && $i < 4) { $result_0_5[$i] += intval(mysqli_result($query_result, $i, 0)); }
				elseif($i >= 4 && $i < 8) { $result_5_15[$i%4] += intval(mysqli_result($query_result, $i, 0)); }
				elseif($i >= 8 && $i < 12) { $result_15_30[$i%4] += intval(mysqli_result($query_result, $i, 0)); }
				elseif($i >= 12 && $i < 16){	$result_30_1h[$i%4] += intval(mysqli_result($query_result, $i, 0)); }
				elseif($i >= 16 && $i < 20){	$result_1h[$i%4] += intval(mysqli_result($query_result, $i, 0)); }
			}
		
		}
		
		$cpt++;
	}
	
	array_push($result, $result_0_5);
	array_push($result, $result_5_15);
	array_push($result, $result_15_30);
	array_push($result, $result_30_1h);
	array_push($result, $result_1h);
	
	// response for the Ajax call
	echo json_encode($result);
}
?>
