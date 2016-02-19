<?php
/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION 4.2
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

$array_msg = array (
	0 => "EON - Standard Error ",
	1 => "EON - Could not connect to Database ",
	2 => "EON - Could not find file ",
	3 => "EON - Could not write in file (verify access) ",
	4 => "EON - Could not get the value in parameters : ",
	5 => "EON - Error uploading file ",
	6 => "EON - Operation successful",
	7 => "EON - Form error ",
	8 => "EON - User / Group ",
	9 => "EON - Graph ",
	10 => "EON - Name Error");

$array_tools = array (
	"ssh access" => "../tool_remoteacces/index_ssh.php",
	"telnet access" => "../tool_remoteacces/index_telnet.php",
	"show interface" => "../tool_interface/index.php",
	"show port" => "../tool_port/index.php");

$array_group_mgt = array (
        "add group" => "add_group",
	"delete group" => "delete_group");

$array_user_mgt = array (
	"add user" => "add_user",
	"delete user" => "delete_user");

$array_bp_mgt = array (
	"add" => "add_process",
	"delete" => "delete_process",
	"delete on cascade" => "cascade_delete",
	"delete all" => "delete_all",
	"duplicate" => "duplicate",
	"back-up file" => "backup");

$array_ged_types = array(
	0 => "all",
        1 => "services",
        2 => "snmp trap",
        /* 3 => "performances"); */
);

$array_ged_packets = array (
	"equipment"		=>	array("type"=>true,"key"=>true,"col"=>true),
	"service"		=>	array("type"=>true,"key"=>true,"col"=>true),
	"state"			=>	array("type"=>true,"key"=>true,"col"=>true),
	"owner"			=>	array("type"=>true,"key"=>false,"col"=>true),
	"description"		=>	array("type"=>true,"key"=>false,"col"=>true),
	"ip_address"		=>	array("type"=>true,"key"=>false,"col"=>false),
	"host_alias"		=>	array("type"=>true,"key"=>false,"col"=>false),
	"hostgroups"		=>	array("type"=>true,"key"=>false,"col"=>false),
	"servicegroups"		=>	array("type"=>true,"key"=>false,"col"=>false),
	"comments"		=>	array("type"=>true,"key"=>false,"col"=>false),
	"original-time"		=>	array("type"=>false,"key"=>false,"col"=>true),
	"last-time"		=>	array("type"=>false,"key"=>false,"col"=>true),
	"acknowledge-time"	=>	array("type"=>false,"key"=>false,"col"=>false),
	"occurences"		=>	array("type"=>false,"key"=>false,"col"=>true),
	"source"		=>	array("type"=>false,"key"=>false,"col"=>false),
	"type"			=>	array("type"=>false,"key"=>false,"col"=>false),
	"id"			=>	array("type"=>false,"key"=>false,"col"=>false)
);

$array_ged_filters = array (
	"equipment",
        "service",
        "description",
	"hostgroups",
        "servicegroups",
        "owner",
        /* "s" */
);

$array_ged_states = array (
	"ok"		=>	"0",
	"warning"	=>	"1",
	"critical"	=>	"2",
	"unknown"	=>	"3",
);

$array_action_option = array(
        0 => "details",
        1 => "edit",
        2 => "own",
        3 => "disown",
        4 => "acknowledge",
);

$array_resolve_action_option = array(
        0 => "details",
	4 => "delete",
);

$array_serv_system = array (
	"Nagios" => array (
			"status" => "pidof -o $$ -o %PPID -x nagios",
			"proc_act" => array (
				"start" => "sudo /etc/init.d/nagios start",
				"stop" => "sudo /etc/init.d/nagios stop",
				"restart" => "sudo /etc/init.d/nagios restart",
				"reload" => "sudo /etc/init.d/nagios reload",
				"verify" => "$path_nagios_bin -v $path_nagios_etc")),
	"Ged agent" => array (
			"status" => "pidof -o $$ -o %PPID -x ged",
			"proc_act" => array (
				"start" => "sudo /etc/init.d/gedd start",
				"stop" => "sudo /etc/init.d/gedd stop",
				"restart" => "sudo /etc/init.d/gedd restart")),
	"SNMP agent" => array (
			"status" => "pidof -o $$ -o %PPID -x snmpd",
			"proc_act" => array (
				"start" => "sudo /etc/init.d/snmpd start",
				"stop" => "sudo /etc/init.d/snmpd stop",
				"restart" => "sudo /etc/init.d/snmpd restart",
				"reload" => "sudo /etc/init.d/snmpd reload")),
	"SNMP trap agent" => array (
			"status" => "pidof -o $$ -o %PPID -x snmptrapd",
			"proc_act" => array (
				"start" => "sudo /etc/init.d/snmptrapd start",
				"stop" => "sudo /etc/init.d/snmptrapd stop",
				"restart" => "sudo /etc/init.d/snmptrapd restart",
				"reload" => "sudo /etc/init.d/snmptrapd reload")),
	"SNMP trap traductor" => array (
                        "status" => "pidof -o $$ -o %PPID -x snmptt",
                        "proc_act" => array (
                                "start" => "sudo /etc/init.d/snmptt start",
                                "stop" => "sudo /etc/init.d/snmptt stop",
                                "restart" => "sudo /etc/init.d/snmptt restart",
                                "reload" => "sudo /etc/init.d/snmptt reload")),
);

?>
