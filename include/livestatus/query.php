<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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

require_once(dirname(__FILE__)."/../config.php");
require_once(dirname(__FILE__)."/../arrays.php");
require_once(dirname(__FILE__)."/../function.php");
require_once(dirname(__FILE__)."/Client.php");
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
	$sockets = getEonConfig("sockets","array");

	$result = array();
	$nbr_services_pending = 0;
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
		
		// check if socket disabled
		if(isset($socket_parts[4])) {
			continue;
		}

		// check if socket is up
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

			// get all service PENDING
			$nbr_pending = $client
				->get('services')
				->stat('has_been_checked = 0')
				->filter('host_contacts >= '. $_SERVER["REMOTE_USER"])
				->execute();

			// construct mklivestatus request, and get the response
			$response = $client
				->get('services')
				->stat('state = 0')
				->stat('state = 1')
				->stat('state = 2')
				->stat('state = 3')
				->filter('has_been_checked = 1')
				->filter('host_contacts >= '. $_SERVER["REMOTE_USER"])
				->execute();

			$nbr_services_pending += $nbr_pending[0][0];
			$nbr_services_ok += $response[0][0];
			$nbr_services_warning += $response[0][1];
			$nbr_services_critical += $response[0][2];
			$nbr_services_unknown += $response[0][3];
		}
	}

	// fill an empty array with previous response, in order to have a beautiful JSON to use
	array_push($result, $nbr_services_pending);
	array_push($result, $nbr_services_ok);
	array_push($result, $nbr_services_warning);
	array_push($result, $nbr_services_critical);
	array_push($result, $nbr_services_unknown);
	
	// response for the Ajax call
	echo json_encode($result);
}

/**
 * Get the number of hosts, ordered by state, and according to the logged user
 */
function getHostsStateNbr()
{
	$sockets = getEonConfig("sockets","array");

	$result = array();
	$nbr_hosts_pending = 0;
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
		
		// check if socket disabled
		if(isset($socket_parts[4])) {
			continue;
		}

		// check if socket is up
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
			
			// get all host PENDING
			$nbr_pending = $client
				->get('hosts')
				->stat('has_been_checked = 0')
				->filter('host_contacts >= '. $_SERVER["REMOTE_USER"])
				->execute();
			
			// construct mklivestatus request, and get the response
			$response = $client
				->get('hosts')
				->stat('state = 0')
				->stat('state = 1')
				->stat('state = 2')
				->stat('state = 3')
				->filter('has_been_checked = 1')
				->filter('host_contacts >= '. $_SERVER["REMOTE_USER"])
				->execute();
				
			$nbr_hosts_pending += $nbr_pending[0][0];
			$nbr_host_ok += $response[0][0];
			$nbr_host_warning += $response[0][1];
			$nbr_host_critical += $response[0][2];
			$nbr_host_unknown += $response[0][3];
		}
	}	

	array_push($result, $nbr_hosts_pending);
	array_push($result, $nbr_host_ok);
	array_push($result, $nbr_host_warning);
	array_push($result, $nbr_host_critical);
	array_push($result, $nbr_host_unknown);
	
	// response for the Ajax call
	echo json_encode($result);
}

/**
 * Get number of event ordered by state (in DB GED), according to the default filter if there is.
 */
function getEventStateNbr($return=false)
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
	
	$pkt_type = sql("ged", "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id != 0 AND pkt_type_id < 100");
	foreach($pkt_type as $row)
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
				$filter_value = $filter->nodeValue;
				if($name == "host"){ $name = "equipment"; }
				if($name == "service_group"){ $name = "servicegroups"; }

				//advanced search value (with *)
				$text = "";
				if( substr($filter_value, 0, 1) === '*' ){
					$text .= "%";
				}
				$text .= trim($filter_value, '*');
				if ( substr($filter_value, -1) === '*' ) {
					$text .= "%";
				}
				if(!isset($requete_filter)) $requete_filter="";	
				$requete_filter .= $name . " LIKE '%".$text."%' OR ";
				$i++;
			}
		}
		
		// foreach event state (0 to 3), will finish the SQL request, and get a result in order to fill the empty array
		for($i = 0; $i <= 3; $i++)
		{
			$requete_finale = $requete . "state = ". $i;
			if(isset($requete_filter)) {
				$requete_finale = $requete_finale." AND (".preg_replace("/OR $/", "", $requete_filter).")";
			}
			#echo "<br>" . $requete_finale . "<br>";
			$query_result = sql($database_ged, $requete_finale);
			switch($i)
			{
				case 0: $nbr_ok += intval($query_result[0][0]); break;
				case 1: $nbr_warning += intval($query_result[0][0]); break;
				case 2: $nbr_critical += intval($query_result[0][0]); break;
				case 3: $nbr_unknown += intval($query_result[0][0]); break;
			}
		}

	}
	
	// push the final results into the json array
	array_push($result, $nbr_ok);
	array_push($result, $nbr_warning);
	array_push($result, $nbr_critical);
	array_push($result, $nbr_unknown);
	
	// response for the Ajax call
	if($return) {
		return $result;
	} else {
		echo json_encode($result);
	}
	
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
			$filter_value = $filter->nodeValue;
			if($name == "host"){ $name = "equipment"; }
			if($name == "service_group"){ $name = "servicegroups"; }

			//advanced search value (with *)
			$text = "";
			if( substr($filter_value, 0, 1) === '*' ){
				$text .= "%";
			}
			$text .= trim($filter_value, '*');
			if ( substr($filter_value, -1) === '*' ) {
				$text .= "%";
			}
			if(!isset($requete_filter)) $requete_filter="";	
			$requete_filter .= $name . " LIKE '%".$text."%' OR ";
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
	$pkt_type = sql($database_ged, "SELECT pkt_type_name FROM pkt_type WHERE pkt_type_id != 0 AND pkt_type_id < 100");
	foreach($pkt_type as $row)
	{
		$temp_result_0_5 = array();
		$temp_result_5_15 = array();
		$temp_result_15_30 = array();
		$temp_result_30_1h = array();
		$temp_result_1h = array();
	
		if(isset($requete_filter)) {
			$milieu_requete = "(".preg_replace("/OR $/", ") AND", $requete_filter)."";
		}

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

		$query_result = sql($database_ged, $sql);
		
		if($cpt == 0)
		{	
			$i = 0;
			foreach($query_result as $row)
			{
				if($i >= 0 && $i < 4) { array_push($result_0_5, intval($row[0])); }
				elseif($i >= 4 && $i < 8) { array_push($result_5_15, intval($row[0])); }
				elseif($i >= 8 && $i < 12) { array_push($result_15_30, intval($row[0])); }
				elseif($i >= 12 && $i < 16){	array_push($result_30_1h, intval($row[0])); }
				elseif($i >= 16 && $i < 20){	array_push($result_1h, intval($row[0])); }
				$i++;
			}
		}
		else
		{
			$i = 0;
			foreach($query_result as $row)
			{
				if($i >= 0 && $i < 4) { $result_0_5[$i] += intval($row[0]); }
				elseif($i >= 4 && $i < 8) { $result_5_15[$i%4] += intval($row[0]); }
				elseif($i >= 8 && $i < 12) { $result_15_30[$i%4] += intval($row[0]); }
				elseif($i >= 12 && $i < 16){	$result_30_1h[$i%4] += intval($row[0]); }
				elseif($i >= 16 && $i < 20){	$result_1h[$i%4] += intval($row[0]); }
				$i++;
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
