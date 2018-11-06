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
sqlrequest($database_notifier,"DELETE FROM rule_method");
sqlrequest($database_notifier,"DELETE FROM rules");
sqlrequest($database_notifier,"DELETE FROM configs");
sqlrequest($database_notifier,"DELETE FROM methods");
sqlrequest($database_notifier,"DELETE FROM timeperiods");

// Insert configs
sqlrequest($database_notifier,"INSERT INTO configs VALUES('','debug','cfg','".trim($xml_methods->debug)."')");
sqlrequest($database_notifier,"INSERT INTO configs VALUES('','debug_rules','rules','".trim($xml_rules->debug_rules)."')");
sqlrequest($database_notifier,"INSERT INTO configs VALUES('','log_file','cfg','".trim($xml_methods->log_file)."')");
sqlrequest($database_notifier,"INSERT INTO configs VALUES('','logrules_file','rules','".trim($xml_rules->logrules_file)."')");
sqlrequest($database_notifier,"INSERT INTO configs VALUES('','notifsent_file','rules','".trim($xml_rules->notifsent_file)."')");

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
				$sql_method = "INSERT INTO methods VALUES('','".$method_name."','".$type."','".$method_line."')";
				$method_id = sqlrequest($database_notifier,$sql_method,true);
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
				
				$timeperiod_exist = sqlrequest($database_notifier,"SELECT id,name from timeperiods where daysofweek='".$daysofweek."' and timeperiod='".$timeperiod."'");
				$timeperiod_result = mysqli_fetch_array($timeperiod_exist);
							
				if(!$timeperiod_result[0]) {
					$timeperiod_id=sqlrequest($database_notifier,"INSERT into timeperiods VALUES('','','".$daysofweek."','".$timeperiod."')",true);
					$timeperiod_name = "TP_".$timeperiod_id."";
					sqlrequest($database_notifier,"UPDATE timeperiods set name='".$timeperiod_name."' where id='".$timeperiod_id."'");
					echo "INFO : create timeperiod ".$timeperiod_name." ($timeperiod_id)\n";
				} else {
					$timeperiod_id=$timeperiod_result[0];
					echo "INFO : use timeperiod ".$timeperiod_result[1]." (".$timeperiod_result[0].")\n";
				}
				
				// Insert rule
				$rule_sort_sql=sqlrequest($database_notifier,"select max(sort_key) as sort_key FROM rules where type='".$type."'");
				$rule_sort_keys= mysqli_fetch_array($rule_sort_sql);
				if(isset($rule_sort_keys["sort_key"])) {
					$rule_sort_key=$rule_sort_keys["sort_key"]+1;
				} else {
					$rule_sort_key=0;
				}
				$sql_rule = "INSERT INTO rules VALUES('','','".$type."','".$debug."','".$contact."','".$host."','".$service."','".$state."','".$notificationnumber."','".$timeperiod_id."','".$tracking."','".$rule_sort_key."')";
				$rule_type_id = sqlrequest($database_notifier,$sql_rule,true);
				$rule_name = "RULE_".strtoupper($type)."_".$rule_type_id."";
				sqlrequest($database_notifier,"UPDATE rules set name='".$rule_name."' where id='".$rule_type_id."'");
				echo "INFO : create $type rule ".$rule_name." ($rule_type_id)\n";
				
				// Methods
				$methods = explode(",",trim($rule[8]));
				foreach($methods as $method) {
					$method_exist = sqlrequest($database_notifier,"SELECT id from methods where name='".trim($method)."' and type='".$type."'");
					$method_res = mysqli_fetch_array($method_exist);
					if(isset($method_res[0])) {
						sqlrequest($database_notifier,"INSERT INTO rule_method VALUES('".$rule_type_id."','".$method_res[0]."')");
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
