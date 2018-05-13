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
include("../../include/function.php");

// create variables from $_POST
extract($_POST);

//url where the tool is searched.
if($tool_list != "")
{
	$url_tool = $tool_list;
	
	if($snmp_version == "3")
		$snmp_com = "nothing";
	if($snmp_com == "" AND $url_tool == "../tool_interface/index.php"){
		message(4,"Could not determine snmp community","critical"); die;
	}else if($snmp_version=="3" AND $url_tool!="tools/port.php" AND ($username=="" OR $password=="")){
		message(4,"Could not determine username and password","critical"); die;
	}else if($snmp_version==""){
		message(4,"Could not determine snmp version","critical"); die;
	}

	if( $page != "" )
	{	
		if( $host_list == "" ){
			message(4,"Please select a host","critical");
		}
		else{
			//hostname selected.
			$tab_host=explode(",",$host_list);
			$host_name = $tab_host[0];
			$host = $host_name;
			
			if($host == "") message(4,"Please select a host","critical");
			else{
				include($url_tool);
			}
		}
	}
	else
	{
		if($host == ""){
			message(4,"Could not determine host","critical");
		}else {	
			//Redirection.
			echo "<meta http-equiv='Refresh' content='0;URL=$url_tool?host=$host&host_name=$host&snmp_version=$snmp_version&snmp_com=$snmp_com& max_port=$max_port&min_port=$min_port&username=".$username."&password=".$password."&snmp_auth_protocol=".$snmp_auth_protocol."&snmp_priv_passphrase=".$snmp_priv_passphrase."&snmp_priv_protocol=".$snmp_priv_protocol."&snmp_context=".$snmp_context."'>";
			echo "</head>";
			echo "<body id='main'>";		 
			echo "<h2>Loading, please wait ...</h2>";
		}
	}	
}
else
{
	message(4,"Please select a tool","critical");
}

?>
