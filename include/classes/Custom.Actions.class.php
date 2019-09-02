<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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
/**
 * Actions class for all eonweb's pages
 */
class CustomActions
{

	/**
	 * Ged Acknowledge
	 */
	public function ged_acknowledge($selected_events, $queue)
	{
        $status_itsm = get_itsm_var("itsm");

        if(isset($status_itsm) && $status_itsm == "on"){
            foreach ($selected_events as $value) {
                $value_parts = explode(":", $value);
                $id = $value_parts[0];
                $ged_type = $value_parts[1];
                if($ged_type == "nagios"){ $ged_type_nbr = 1; }
                if($ged_type == "snmptrap"){ $ged_type_nbr = 2; }
        
                $event_to_delete = [];
                array_push($event_to_delete, $value);
        
                $sql = "SELECT * FROM ".$ged_type."_queue_".$queue." WHERE id = ?";
                $result = sqlrequest($database_ged, $sql, false, array("s",(string)$id));
                $event = mysqli_fetch_assoc($result);
                $detail = $event["equipment"]." / ".$event["service"];
                $description = "<H1>Détail de l'incident:</H1> <br>Nom d'équipement: ".$event["equipment"]."<br> Service: ".$event["service"]."<br>Adresse IP: ".$event["ip_address"]."<br><br>Description: ".$event["description"]."<br><br>Commentaire: ".$event["comments"];
            }
            $result = report_itsm($detail,$description);
            insert_itsm_var("log_itsm",$result);
            return $result;
        }else return false;
	}

	/**
	 * Ged Edit
	 */
	public function ged_edit($selected_events, $queue, $comments)
	{
		return true;
	}
	
	/**
	 * Ged Own
	 */
	public function ged_own($selected_events, $queue, $global_action)
	{
		return true;
    }
    
}

?>
