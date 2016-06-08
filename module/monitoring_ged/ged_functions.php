<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

include("../../include/config.php");
include("../../include/arrays.php");
//require_once("../../include/function.php"); 

function getEventState($event)
{
	switch ($event->state) {
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

function createTableRow($event, $event_state)
{
	global $dateformat;

	foreach ($event as $key => $value) {
		$class = "";

		if($key == "equipment"){
			$thruk_url = urlencode("/thruk/cgi-bin/extinfo.cgi?type=1&host=$value");
			$value = '<a href="../module_frame/index.php?url='.$thruk_url.'">'.$value.'</a>';
		}
		if($key == "service"){
			$thruk_url = urlencode("/thruk/cgi-bin/extinfo.cgi?type=2&host=".$event->equipment."&service=$value");
			$value = '<a href="../module_frame/index.php?url='.$thruk_url.'">'.$value.'</a>';
		}
		if ($key == "state") {
			continue;
		}
		if($key == "o_sec" || $key == "l_sec"){
			$value = date($dateformat, $value);
		}
		if($key == "id"){
			$value = "<input type='checkbox' name='events_selected' value='".$event->id."'>";
			$class = 'class="text-center"';
		}

		echo "<td $class>$value</td>";
	}	
}

function createSelectClause($ged_type, $queue)
{
	global $array_ged_packets;
	global $database_ged;

	$sql = "SELECT ";
	foreach ($array_ged_packets as $key => $value) {
		if($value["col"] == true){
			if(isset($value["db_col"])){
				$sql .= $value["db_col"].',';
			} else {
				$sql .= $key.',';
			}
		}
	}
	$sql .= "id";
	$sql .= " FROM ".$ged_type."_queue_".$queue;

	return $sql;
}

function createWhereClause($owner, $filter, $search, $daterange, $ok, $warning, $critical, $unknown)
{
	$where_clause = " WHERE id > 0";

	// owner
	if($owner == "owned"){ $where_clause .= " AND owner != ''"; }
	elseif($owner == "not owned"){ $where_clause .= " AND owner = ''"; }

	// filter and search
	if($search != ""){
		$where_clause .= " AND $filter LIKE '%$search%'";
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
		$where_clause .= " AND o_sec > $start AND o_sec < $end";
	}

	// states
	$states_list = "";
	if($ok != "")		{ $states_list .= "0,"; }
	if($warning != "")	{ $states_list .= "1,"; }
	if($critical != "")	{ $states_list .= "2,"; }
	if($unknown != "")	{ $states_list .= "3,"; }
	$states_list = trim($states_list, ",");
	
	if($states_list != ""){
		$where_clause .= " AND state IN ($states_list)";
	}

	$where_clause .= " LIMIT 500";
	return $where_clause;
}

function createDetailRow($event, $db_col_name, $row_name)
{
	global $dateformat;

	// display a good date format
	if($db_col_name == "o_sec" || $db_col_name == "l_sec" || $db_col_name == "a_sec"){
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
		echo '<td>'.$event[$db_col_name].'</td>';
	echo '</tr>';
}

function details($selected_events, $queue)
{
	global $database_ged;
	
	// get all needed infos into variables
	$value_parts = explode(":", $selected_events);
	$id = $value_parts[0];
	$ged_type = $value_parts[1];

	$sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = $id";
	$result = sqlrequest($database_ged, $sql);
	$event = mysqli_fetch_assoc($result);

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
	global $database_ged;

	// get all needed infos into variables
	$value_parts = explode(":", $selected_events);
	$id = $value_parts[0];
	$ged_type = $value_parts[1];

	$sql = "SELECT comments FROM ".$ged_type."_queue_".$queue." WHERE id = $id";
	$result = sqlrequest($database_ged, $sql);
	$event = mysqli_fetch_assoc($result);

	echo "
	<form id='edit-event-form'>
		<div class='form-group'>
			<label>".getLabel("label.add_comment")."</label>
			<textarea id='event-comments' class='form-control'>".$event["comments"]."</textarea>
		</div>
	</form>";
}

function editEvent($selected_events, $queue, $comments)
{
	global $database_ged;

	// get all needed infos into variables
	$value_parts = explode(":", $selected_events);
	$id = $value_parts[0];
	$ged_type = $value_parts[1];

	// format comment string to avoid errors
	$comments = str_replace("'", "\'", $comments);
	$comments = str_replace("#", "\#", $comments);

	$sql = "UPDATE ".$ged_type."_queue_".$queue." SET comments='$comments' WHERE id = $id";
	$result = sqlrequest($database_ged, $sql);
	if($result){
		message(11, " : ".getLabel("message.event_edited"), "ok");
	} else {
		message(11, " : ".getLabel("message.event_edited_error"), "danger");
	}
}

function editAllEvents($selected_events, $queue, $comments)
{
	global $database_ged;

	$success = true;
	foreach ($selected_events as $value) {
		// get all needed infos into variables
		$value_parts = explode(":", $value);
		$id = $value_parts[0];
		$ged_type = $value_parts[1];

		// format comment string to avoid errors
		$comments = str_replace("'", "\'", $comments);
		$comments = str_replace("#", "\#", $comments);

		$sql = "UPDATE ".$ged_type."_queue_".$queue." SET comments='$comments' WHERE id = $id";
		$result = sqlrequest($database_ged, $sql);
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
	global $path_ged_bin;
	global $array_serv_system;

	if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
		return message(0," : ged daemon must be dead","critical");
	}

	if($global_action == "own"){
		$owner = $_COOKIE['user_name']."@".getenv("SERVER_NAME");
	} else {
		$owner = "";
	}

	foreach ($selected_events as $value) {
		$value_parts = explode(":", $value);
		$id = $value_parts[0];
		$ged_type = $value_parts[1];

		$sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = $id";
		$result = sqlrequest($database_ged, $sql);
		$event = mysqli_fetch_assoc($result);

		$ged_command = "-update -type 1 ";
		foreach ($array_ged_packets as $key => $value) {
			if($value["type"] == true){
				if($key == "owner"){
					$event[$key] = $owner;
				}
				$ged_command .= "\"".$event[$key]."\" ";
			}
		}
		$ged_command = trim($ged_command, " ");

		shell_exec($path_ged_bin." ".$ged_command);
		logging("ged_update",$ged_command);
	}
	
	//-update -type 1 "test" "memory" "3" "admin@192.168.83.133" "ERROR: netsnmp : Send failure: Network is unreachable." "12.14.15.13" "fefefefrfrfr" "LINUX-BDD,LINUX" "" ""
	//-update -type 1 "test" "HOST DOWN" "2" "admin@192.168.83.133" "CRITICAL - Network Unreachable (12.14.15.13)" "12.14.15.13" "fefefefrfrfr" "LINUX-BDD,LINUX" "" ""
	//-drop -id 121 -queue history
}

?>