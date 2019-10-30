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
include("../../module/admin_itsm/function_itsm.php");
include("../../module/admin_itsm/classes/Itsm.php");
include("../../module/admin_itsm/classes/ItsmPeer.php");
include_once("../../include/function.php"); 
include("./ged_functions.php");

// create variables from $_GET
extract($_GET);
if(!isset($queue)) { $queue="active"; } 
elseif(!in_array($queue,$array_ged_queues)) { $queue="active"; }

if(!isset($group)) { $group=null; }
// execute actions

/*
 * 0 = details
 * 1 = edit
 * 2 = own
 * 3 = disown
 * 4 = acknowledge
 * 5 = delete
 */

 if(isset($action) && $action != "" && (isset($selected_events) && count($selected_events) > 0) || isset($filter_name) || isset($filter) ){
	//error_log("ged_action.php :".$action." \n", 3 , "/srv/eyesofnetwork/eonweb/module/admin_itsm/uploaded_file/log");
	switch ($action) {
		case "0":
			details($selected_events, $queue);
			break;
		case "1":
			edit($selected_events, $queue);
			break;
		case "6":
			edit($selected_events, $queue);
			break;
		case 'edit':
			editAllEvents($selected_events, $queue, $comments);
			if($global_action == "4"){
				$CustomActions->ged_acknowledge($selected_events, $queue);				
				acknowledge($selected_events, $queue, $checkBoxNagios);
			} elseif($global_action == "2") {
				ownDisown($selected_events, $queue, $global_action);
				$CustomActions->ged_own($selected_events, $queue, $global_action);
			}elseif($global_action == "6"){
				$CustomActions->ged_acknowledge($selected_events, $queue);				
				acknowledge($selected_events, $queue,$checkBoxNagios);
			}
			break;
		case 'confirm':
			if($global_action == "4"){
				$CustomActions->ged_acknowledge($selected_events, $queue);
				acknowledge($selected_events, $queue,$checkBoxNagios);
			} elseif($global_action == "5") {
				delete($selected_events, $queue);
			} elseif($global_action == "2" || $global_action == "3") {
				ownDisown($selected_events, $queue, $global_action);
				$CustomActions->ged_own($selected_events, $queue, $global_action);
			}elseif($global_action == "6"){
				$CustomActions->ged_acknowledge($selected_events, $queue);				
				acknowledge($selected_events, $queue, $checkBoxNagios);
			}
			break;
		case 'changeGedFilter':
			changeGedFilter($filter_name);
			break;
		case 'advancedFilterSearch':
			advancedFilterSearch($queue, $filter);
			break;
	}
}
?>
