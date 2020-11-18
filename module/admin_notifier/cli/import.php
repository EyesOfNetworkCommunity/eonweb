#!/usr/bin/php
<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION 5.2
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

(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die("<br><strong>This script is only meant to run at the command line.</strong>");

$_COOKIE["user_name"]="admin";
$path_eonweb="/srv/eyesofnetwork/eonweb";
include("$path_eonweb/include/config.php");
include("$path_eonweb/include/function.php");
setcookie("user_name",FALSE);

// Open notifier files
$xml_rules=simplexml_load_file($path_notifier_rules);
$xml_methods=simplexml_load_file($path_notifier_methods);

// Empty tables
sql($database_notifier,"DELETE FROM rule_method");
sql($database_notifier,"DELETE FROM rules");
sql($database_notifier,"DELETE FROM configs");
sql($database_notifier,"DELETE FROM methods");
sql($database_notifier,"DELETE FROM timeperiods");

// Insert configs
sql($database_notifier,"INSERT INTO configs (name, type, value) VALUES('debug','cfg',?)", array(trim($xml_methods->debug)));
sql($database_notifier,"INSERT INTO configs (name, type, value) VALUES('debug_rules','rules',?)", array(trim($xml_rules->debug_rules)));
sql($database_notifier,"INSERT INTO configs (name, type, value) VALUES('log_file','cfg',?)", array(trim($xml_methods->log_file)));
sql($database_notifier,"INSERT INTO configs (name, type, value) VALUES('logrules_file','rules',?)", array(trim($xml_rules->logrules_file)));
sql($database_notifier,"INSERT INTO configs (name, type, value) VALUES('notifsent_file','rules',?)", array(trim($xml_rules->notifsent_file)));

// Insert methods
function notifier_import_methods($type) {
	
	global $database_notifier;
	global $xml_methods;
	
	$methods_type = explode(PHP_EOL,$xml_methods->commands->$type);
	foreach($methods_type as $method_type) {
		if(!empty($method_type)) {
			$method = explode("=",trim($method_type),2);
			if(isset($method[0]) and isset($method[1])) {
				$method_name = trim($method[0]);
				$method_line = addslashes(trim($method[1]));
				$sql_method = "INSERT INTO methods (name, type, line) VALUES(?,?,?)";
				$method_id = sql($database_notifier,$sql_method, array($method_name, $type, $method_line));
				echo "INFO : create $type method ".$method_name." ($method_id)\n";
			}
		}
	}
}

notifier_import_methods("host");
notifier_import_methods("service");

// Insert rules
function notifier_import_rules($type) {
	
	global $database_notifier;
	global $xml_rules;
	
	$rules_type = explode(PHP_EOL,$xml_rules->$type);
	foreach($rules_type as $rule_type) {
		if(!empty($rule_type)) {
			$rule = explode(":",$rule_type,10);		 
			if(count($rule) == 10 || count($rule) == 9 ) {
				$debug = trim($rule[0]);
				$contact = trim($rule[1]);
				$host = trim($rule[2]);
				$service = trim($rule[3]);
				$state = trim($rule[4]);
				$notificationnumber = trim($rule[7]);
				
				if(isset($rule[9])){
					$tracking = trim($rule[9]);
				}else{
					$tracking = 0;
				}
				
				// Timeperiod
				$daysofweek = trim($rule[5]);
				$timeperiod = trim($rule[6]);
				
				$timeperiod_exist = sql($database_notifier,"SELECT id,name from timeperiods where daysofweek=? and timeperiod=?", array($daysofweek, $timeperiod));
				$timeperiod_result = $timeperiod_exist;
							
				if(!$timeperiod_result[0]) {
					$timeperiod_id=sql($database_notifier,"INSERT into timeperiods (daysofweek, timeperiod) VALUES(?,?)", array($daysofweek, $timeperiod));
					$timeperiod_name = "TP_".$timeperiod_id."";
					sql($database_notifier,"UPDATE timeperiods set name=? where id=?", array($timeperiod_name, $timeperiod_id));

					echo "INFO : create timeperiod ".$timeperiod_name." ($timeperiod_id)\n";
				} else {
					$timeperiod_id=$timeperiod_result[0];
					echo "INFO : use timeperiod ".$timeperiod_result[1]." (".$timeperiod_result[0].")\n";
				}
				
				// Insert rule
				$rule_sort_sql=sql($database_notifier,"select max(sort_key) as sort_key FROM rules where type=?",array($type));
				$rule_sort_keys= $rule_sort_sql;
				if(isset($rule_sort_keys["sort_key"])) {
					$rule_sort_key=$rule_sort_keys["sort_key"]+1;
				} else {
					$rule_sort_key=0;
				}
        		$sql_rule = "INSERT INTO rules (type, debug, contact, host, service, state, notificationnumber, timeperiod_id, tracking, sort_key) VALUES(?,?,?,?,?,?,?,?,?,?)";
				$rule_type_id = sql($database_notifier,$sql_rule, array($type, $debug, $contact, $host, $service, $state, $notificationnumber, $timeperiod_id, $tracking, $rule_sort_key));
				$rule_name = "RULE_".strtoupper($type)."_".$rule_type_id."";
				sql($database_notifier,"UPDATE rules set name=? where id=?", array($rule_name, $rule_type_id));
				echo "INFO : create $type rule ".$rule_name." ($rule_type_id)\n";
				
				// Methods
				$methods = explode(",",trim($rule[8]));
				foreach($methods as $method) {
					$method_exist = sql($database_notifier,"SELECT id from methods where name=? and type=?", array(trim($method), $type));
					$method_res = $method_exist;
					if(isset($method_res[0])) {
						sql($database_notifier,"INSERT INTO rule_method (rule_id, method_id) VALUES(?,?)", array($rule_type_id, $method_res[0]));
						echo "INFO : use method $method (".$method_res[0].")\n";
						
					} else {
						echo "ERROR : method $method not found\n";
					}
				}
				
			}
		}
	}
}

notifier_import_rules("host");
notifier_import_rules("service");

?>
