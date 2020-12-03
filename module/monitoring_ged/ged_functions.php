<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.2
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

include("../../include/config.php");
include("../../include/arrays.php");

function getEventState($event)
{
	switch ($event) {
		case 0: $event_state = "OK";		break;
		case 1: $event_state = "WARNING";	break;
		case 2: $event_state = "CRITICAL";	break;
		case 3: $event_state = "UNKNOWN";	break;
	}

	return $event_state;
}

function getClassRow($event_state)
{
	switch ($event_state) {
		case "OK"		: $row_class = "success";	break;
		case "WARNING"	: $row_class = "warning";	break;
		case "CRITICAL"	: $row_class = "danger"; 	break;
		case "UNKNOWN"	: $row_class = "info"; 		break;
	}

	return $row_class;
}

function createTableRow($event, $event_state, $queue)
{
	global $array_ged_queues;
	global $dateformat;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }
	
	foreach ($event as $key => $value) {
		$class = "";

		if($key == "equipment"){
			if($event->src == "0.0.0.0" || $event->src == "0.0.0.0/0") {
				$url_host = preg_replace("/^".getEonConfig("ged_prefix")."/","",$value,1);
				$thruk_url = urlencode("/thruk/cgi-bin/extinfo.cgi?type=1&host=$url_host");
				$value = '<a href="../module_frame/index.php?url='.$thruk_url.'">'.$value.'</a>';
			} else { 
				$value = '<a class="nodecor">'.$value.'</a>';
			}
			$class = 'class="host"';
		}
		if($key == "service"){
			if($event->src == "0.0.0.0" || $event->src == "0.0.0.0/0") {
				$url_host = preg_replace("/^".getEonConfig("ged_prefix")."/","",$event->equipment,1);
				$thruk_url = urlencode("/thruk/cgi-bin/extinfo.cgi?type=2&host=".$url_host."&service=$value");
				$value = '<a href="../module_frame/index.php?url='.$thruk_url.'">'.$value.'</a>';
			} else {
				$value = '<a class="nodecor">'.$value.'</a>';
			}
			$class = 'class="service"';
		}
		if ($key == "state" || $key == "comments" || $key == "src") {
			continue;
		}
		if($key == "o_sec" || $key == "l_sec"){
			if($queue == "active"){
				$value = strTime(time() - $value);
			} else {
				$value = date($dateformat, $value);
			}
		}
		if($key == "id"){
			$value = "<input type='hidden' value='".$value."'>";
			$class = 'class="text-center"';
			if($event->comments != ""){
				$value .= ' <i class="glyphicon glyphicon-comment" title="'.$event->comments.'"></i>';
			}
			if($event->owner != ""){
				$value .= ' <i class="glyphicon glyphicon-floppy-disk"></i>';
			}
		}
		
		echo "<td $class>$value</td>";
	}	
}

function createSelectClause($ged_type, $queue)
{
	global $array_ged_queues; 
	global $array_ged_packets;
	global $database_ged;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }

	$sql = "SELECT id,";
	foreach ($array_ged_packets as $key => $value) {
		if($value["col"] == true){
			if(isset($value["db_col"])){
				$sql .= $value["db_col"].',';
			} else {
				$sql .= $key.',';
			}
		}
	}
	$sql .= "comments,src";
	//$sql = trim($sql, ",");
	$sql .= " FROM ".$ged_type."_queue_".$queue;
	$sql .= " WHERE id > 0";

	return $sql;
}

function createWhereClause($owner, $filter, $search, $daterange, $ok, $warning, $critical, $unknown)
{
	
	global $mysql_prepare;
	
	$where_clause = "";
	
	// owner
	if($owner == "owned"){ $where_clause .= " AND owner != ''"; }
	elseif($owner == "not owned"){ $where_clause .= " AND owner = ''"; }

	// advanced search (with *)
	if($search != ""){
		$like = "";
		if( substr($search, 0, 1) === '*' ){
			$like .= "%";
		}
		$like .= trim($search, '*');
		if ( substr($search, -1) === '*' ) {
			$like .= "%";
		}

		$where_clause .= " AND $filter LIKE ?";
		$mysql_prepare[0].="s";
		$mysql_prepare[]=(string)$like;
	}

	// daterange
	if($daterange != ""){
		$daterange_parts = explode(" - ", $daterange);
		$start = $daterange_parts[0];
		$end = $daterange_parts[1];

		// modify start and end timestamp (1 Jan 1970 = -3600).
		// perhaps a little bug from DateRangePicker
		$start = strtotime($start);
		$start += 3600;
		$end = strtotime($end);
		$end += 86400 + 3600;
		$where_clause .= " AND o_sec > ? AND o_sec < ?";
		$mysql_prepare[0].="ii";
		$mysql_prepare[]=(int)$start;
		$mysql_prepare[]=(int)$end;
	}

	// states
	$states_list = "";
	if($ok != "")		{ $states_list .= "?,"; $mysql_prepare[0].="i"; $mysql_prepare[]=0; }
	if($warning != "")	{ $states_list .= "?,"; $mysql_prepare[0].="i"; $mysql_prepare[]=1; }
	if($critical != "")	{ $states_list .= "?,"; $mysql_prepare[0].="i"; $mysql_prepare[]=2; }
	if($unknown != "")	{ $states_list .= "?,"; $mysql_prepare[0].="i"; $mysql_prepare[]=3; }
	$states_list = trim($states_list, ",");
	
	if($states_list != ""){
		$where_clause .= " AND state IN ($states_list)";
	}

	$where_clause .= " ORDER BY l_sec DESC LIMIT ".getEonConfig("maxlines");
	return $where_clause;
}

function createDetailRow($event, $db_col_name, $row_name)
{
	global $dateformat;

	// display a good date format
	if($db_col_name == "o_sec" || $db_col_name == "l_sec" || $db_col_name == "a_sec"){
		if($db_col_name == "a_sec" && $event["queue"] == "a"){
			return false;
		}
		if($db_col_name == "a_sec" && $event[$db_col_name] == 0){
			$event[$db_col_name] = "";
		}
		$event[$db_col_name] = date($dateformat, $event[$db_col_name]+0);
	}

	// display a good state format
	if($db_col_name == "state"){
		switch($event[$db_col_name]){
			case 0: $event[$db_col_name] = "OK"; break;
			case 1: $event[$db_col_name] = "WARNING"; break;
			case 2: $event[$db_col_name] = "CRITICAL"; break;
			case 3: $event[$db_col_name] = "UNKNOWN"; break;
		}
	}

	echo '<tr>';
		echo '<th scope="row">'.getLabel($row_name).'</th>';
		echo '<td style="word-break: break-all;">'.$event[$db_col_name].'</td>';
	echo '</tr>';
}

function details($selected_events, $queue)
{
	global $array_ged_queues; 
	global $database_ged;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }

	// get all needed infos into variables
	$value_parts = explode(":", $selected_events);
	$id = $value_parts[0];
	$ged_type = $value_parts[1];
	if($ged_type == "nagios" || $ged_type == "snmptrap" ){
	} else {
		$ged_type = null;
	}
	if($queue == "active" || $queue == "history" || $queue == "sync"){
	} else {
		$queue = null;
	}

	$sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = ?";
	$result = sql($database_ged, $sql, array($id));
	$event = $result[0];

	// display event's details
	echo '<table class="table table-hover table-condensed">';
		echo '<tbody>';
			createDetailRow($event, "equipment", "label.host");
			createDetailRow($event, "host_alias", "label.host_alias");
			createDetailRow($event, "ip_address", "label.ip_address");
			createDetailRow($event, "service", "label.service");
			createDetailRow($event, "state", "label.state");
			createDetailRow($event, "description", "label.desc");
			createDetailRow($event, "occ", "label.occurence");
			createDetailRow($event, "o_sec", "label.o_time");
			createDetailRow($event, "l_sec", "label.l_time");
			createDetailRow($event, "a_sec", "label.a_time");
			createDetailRow($event, "hostgroups", "label.hostgroups");
			createDetailRow($event, "servicegroups", "label.servicegroups");
			createDetailRow($event, "src", "label.source");
			createDetailRow($event, "owner", "label.owner");
			createDetailRow($event, "comments", "label.comments");
		echo '</tbody>';
	echo '</table>';
}

function edit($selected_events, $queue)
{
	global $array_ged_queues; 
	global $database_ged;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }

	// get all needed infos into variables
	$value_parts = explode(":", $selected_events);
	$id = $value_parts[0];
	$ged_type = $value_parts[1];

	if($ged_type == "nagios" || $ged_type == "snmptrap" ){
	} else {
		$ged_type = null;
	}
	if($queue == "active" || $queue == "history" || $queue == "sync"){
	} else {
		$queue = null;
	}
	$sql = "SELECT comments FROM ".$ged_type."_queue_".$queue." WHERE id = ?";
	$result = sql($database_ged, $sql, array($id));
	$event = $result[0];
	// <label for='group'>Affectation : </label>
	// 		<select class='form-control' id='group'>
    //                  	   <option value='2' selected>GROUPE_SYSTEME</option>
    //                  	   <option value='292'>GROUPE_RESEAU</option>
    //                  	   <option value='3'>GROUPE_DBA</option>
    //                  	   <option value='113'>GROUPE_ESHOP_RUN</option>
    //                  	 </select>
	// 		
	echo "
	<form id='edit-event-form'>
		<div class='form-group'>
			<label>".getLabel("label.add_comment")."</label>
			<textarea id='event-comments' class='form-control textarea' rows='10'>".$event["comments"]."</textarea>
		</div>
	</form>";
}

function editAllEvents($selected_events, $queue, $comments)
{
	global $array_ged_queues;
	global $database_ged;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }
	
	$success = true;
	foreach ($selected_events as $key => $value) {
		// get all needed infos into variables
		$value_parts = explode(":", $value);
		$id = $value_parts[0];
		$ged_type = $value_parts[1];
		if($ged_type == "nagios" || $ged_type == "snmptrap" ){
		} else {
			$ged_type = null;
		}
		if($queue == "active" || $queue == "history" || $queue == "sync"){
		} else {
			$queue = null;
		}
		$sql = "UPDATE ".$ged_type."_queue_".$queue." SET comments=? WHERE id = ?";
		$result = sql($database_ged, $sql, array($comments, $id));
		if(!$result){
			$success = false;
		}
	}

	// display the final message
	if($success){
		message(11, " : ".getLabel("message.event_edited"), "ok");
	} else {
		message(11, " : ".getLabel("message.event_edited_error"), "danger");
	}
}

function ownDisown($selected_events, $queue, $global_action)
{
	global $database_ged;
	global $array_ged_packets;
	global $array_ged_queues;
	global $path_ged_bin;
	global $array_serv_system;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }
	
	if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
		return message(0," : ged daemon must be dead","critical");
	}

	if($global_action == 2){
		$owner = $_COOKIE['user_name']."@".getenv("SERVER_NAME");
	} else {
		$owner = "";
	}

	foreach ($selected_events as $value) {
		$value_parts = explode(":", $value);
		$id = $value_parts[0];
		$ged_type = $value_parts[1];
		if($ged_type == "nagios"){ $ged_type_nbr = 1; }
		if($ged_type == "snmptrap"){ $ged_type_nbr = 2; }
		if($ged_type == "nagios" || $ged_type == "snmptrap" ){
		} else {
			$ged_type = null;
		}
		if($queue == "active" || $queue == "history" || $queue == "sync"){
		} else {
			$queue = null;
		}
		$sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = ?";
		$result = sql($database_ged, $sql, array($id));
		$event = $result[0];

		$ged_command = "-update -type $ged_type_nbr ";
		foreach ($array_ged_packets as $key => $value) {
			if($value["type"] == true){
				if($key == "owner"){
					$event[$key] = $owner;
				}
				$ged_command .= escapeshellarg($event[$key])." ";
			}
		}
		$ged_command = trim($ged_command, " ");
		
		shell_exec($path_ged_bin." ".$ged_command);
		logging("ged_update",$ged_command);
	}
}

function acknowledge($selected_events, $queue, $checkBoxNagios)
{
	global $array_ged_queues;
	global $database_ged;
	global $array_ged_packets;
	global $path_ged_bin;
	global $array_serv_system;
	$nagios_default = (get_config_var("itsm_thruk") == false ) ? "" : get_config_var("itsm_thruk");
	$itsm = (get_config_var("itsm") == false ) ? "" : get_config_var("itsm");

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }
	
	if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
		return message(0," : ged daemon must be dead","critical");
	}

	$owner = $_COOKIE['user_name']."@".getenv("SERVER_NAME");

	foreach ($selected_events as $value) {
		$value_parts = explode(":", $value);
		$id = $value_parts[0];
		$ged_type = $value_parts[1];
		$hostName = $value_parts[3];
		$serviceName = $value_parts[4];
		$isHost = explode(" ", $serviceName);
		if($itsm == "on") {
			if($nagios_default == "true") {
				if($checkBoxNagios == "true") {
					$off = 0;
					$on = 1;
					$date = new DateTime();
					$timestamp = $date->getTimestamp();
					$CommandFile="/srv/eyesofnetwork/nagios/var/log/rw/nagios.cmd";
					if($isHost[0] == "HOST") {
						$cmdline = '['.$timestamp.'] ACKNOWLEDGE_HOST_PROBLEM;'.$hostName.';'.$on.';'.$on.';'.$off.';' .$owner. '; Acknowleged in Ged'.PHP_EOL;
					} else{
						$cmdline = '['. $timestamp .'] ACKNOWLEDGE_SVC_PROBLEM;'.$hostName.';'.$serviceName.';'.$on.';'.$on.';'.$off.';' .$owner. '; Acknowleged in Ged'.PHP_EOL;
					}
					file_put_contents($CommandFile, $cmdline,FILE_APPEND);
				}
			}
		} else {
			if($checkBoxNagios == "true") {
				$off = 0;
				$on = 1;
				$date = new DateTime();
				$timestamp = $date->getTimestamp();
				$CommandFile="/srv/eyesofnetwork/nagios/var/log/rw/nagios.cmd";
				if($isHost[0] == "HOST") {
					$cmdline = '['.$timestamp.'] ACKNOWLEDGE_HOST_PROBLEM;'.$hostName.';'.$on.';'.$off.';'.$on.';' .$owner. '; Acknowleged in Ged'.PHP_EOL;
				} else{
					$cmdline = '['. $timestamp .'] ACKNOWLEDGE_SVC_PROBLEM;'.$hostName.';'.$serviceName.';'.$on.';'.$off.';'.$on.';' .$owner. '; Acknowleged in Ged'.PHP_EOL;
				}
				file_put_contents($CommandFile, $cmdline,FILE_APPEND);
			}

		}
		if($ged_type == "nagios"){ $ged_type_nbr = 1; }
		if($ged_type == "snmptrap"){ $ged_type_nbr = 2; }

		$event_to_delete = [];
		array_push($event_to_delete, $value);
		if($ged_type == "nagios" || $ged_type == "snmptrap" ){
		} else {
			$ged_type = null;
		}
		if($queue == "active" || $queue == "history" || $queue == "sync"){
		} else {
			$queue = null;
		}
		$sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = ?";
		$result = sql($database_ged, $sql, array($id));
		$event = $result[0];

		$ged_command = "-update -type $ged_type_nbr ";
		foreach ($array_ged_packets as $key => $value) {
			if($value["type"] == true){
				if($key == "owner"){
					$event[$key] = $owner;
				}
				$ged_command .= escapeshellarg($event[$key])." ";
			}
		}
		$ged_command = trim($ged_command, " ");
		
		shell_exec($path_ged_bin." ".$ged_command);
		logging("ged_update",$ged_command);
		delete($event_to_delete, $queue);
	}
}

function delete($selected_events, $queue)
{
	global $array_ged_queues;
	global $database_ged;
	global $array_ged_packets;
	global $path_ged_bin;
	global $array_serv_system;

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }
	
	if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
		return message(0," : ged daemon must be dead","critical");
	}

	$id_list = "";
	foreach ($selected_events as $value) {
		$value_parts = explode(":", $value);
		$id = $value_parts[0];
		$ged_type = $value_parts[1];
		$ged_type_nbr = 0;
		if($ged_type == "nagios"){ $ged_type_nbr = 1; }
		if($ged_type == "snmptrap"){ $ged_type_nbr = 2; }
		if($ged_type == "nagios" || $ged_type == "snmptrap" ){
		} else {
			$ged_type = null;
		}
		if($queue == "active" || $queue == "history" || $queue == "sync"){
		} else {
			$queue = null;
		}
		$sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = $id";
		$result = sql($database_ged, $sql);
		$event = $result[0];

		if($queue == "active"){
			$ged_command = "-drop -type $ged_type_nbr -queue $queue ";
			foreach ($array_ged_packets as $key => $value) {
				if($value["key"] == true){
					$ged_command .= escapeshellarg($event[$key])." ";
				}
			}
			$ged_command = trim($ged_command, " ");
					
			shell_exec($path_ged_bin." ".$ged_command);
			logging("ged_update",$ged_command);
		} else {
			$id_list .= $id.",";
		}
	}

	if($queue == "history"){
		$id_list = trim($id_list, ",");
		$ged_command = "-drop -id ".escapeshellarg($id_list)." -queue history";
		
		shell_exec($path_ged_bin." ".$ged_command);
		logging("ged_update",$ged_command);
	}
}

// Open Xml function
function openXml($file=false)
{
	$dom = new DOMDocument("1.0","UTF-8");
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	if($file)
		$dom->load($file);
	return $dom;
}

function changeGedFilter($filter_name)
{
	$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";

	if(file_exists($file)){
		$xmlfilters = new DOMDocument("1.0","UTF-8");
		$xmlfilters->load($file);

		$root = $xmlfilters->getElementsByTagName("ged")->item(0);
		$root->removeChild($root->getElementsByTagName('default')->item(0));
		$default = $xmlfilters->createElement("default");
		$default = $root->appendChild($default);
		$default = $root->getElementsByTagName("default")->item(0);
		$default->appendChild($xmlfilters->createTextNode($filter_name));
		$xmlfilters->save($file);
	}
}

// advanced search autocomplete
function advancedFilterSearch($queue, $filter)
{
	global $array_ged_packets;
	global $array_ged_queues;
	global $database_ged;
	$datas = array();
	$filter = htmlspecialchars($filter);
	if($queue == "active" || $queue == "history" || $queue == "sync"){
	} else {
		$queue = null;
	}

	if(!in_array($queue,$array_ged_queues)) { $queue=$array_ged_queues[0]; }

	if($filter == "description"){
		echo json_encode($datas);
		return false;
	}

	$gedsql_result1=sql($database_ged,"SELECT pkt_type_id,pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100'");
	
	if(isset($array_ged_packets[$filter])) {
		foreach($gedsql_result1 as $ged_type){
			if($ged_type["pkt_type_name"] == "nagios" || $ged_type["pkt_type_name"] == "snmptrap" ){
			} else {
				$ged_type["pkt_type_name"] = null;
			}
			
			$sql = "SELECT DISTINCT $filter FROM ".$ged_type["pkt_type_name"]."_queue_".$queue;

			$results = sql($database_ged, $sql);
			foreach($results as $result){
				if( !in_array($result[$filter], $datas) && $result[$filter] != "" ){
					array_push($datas, $result[$filter]);
				}
			}
		}
	}

	echo json_encode($datas);
}

function edit_button(){
	global $database_eonweb;
	$itsm_button = "";
	$itsm = sql($database_eonweb,"SELECT value FROM configs WHERE name=\"itsm\"");
	$itsm = $itsm[0][0];

	if(isset($itsm) && $itsm == "on" && isset($_GET["q"]) && $_GET["q"] == "active" ){
		echo "
		<div id=\"itsm-btns\" class=\"btn-group\">
						<button id=\"itsm-choose\" class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
							".getLabel('label.admin_itsm.ged_btn_create')." <span class=\"caret\"></span>
						</button>
						<ul class=\"dropdown-menu\">
							<li id=\"itsm-event\"><a href=\"#\">".getLabel('label.this')."</a></li>
							<li id=\"itsm-all-event\"><a href=\"#\">".ucfirst(getLabel('label.all'))."</a></li>
						</ul>

						<span id=\"itsm-simple\">
						<button id=\"itsm-event\" class=\"btn btn-primary\" aria-haspopup=\"true\" aria-expanded=\"false\">
							".getLabel('label.admin_itsm.ged_btn_create')."
						</button>
						</span>
					</div>";
	}
}

?>
