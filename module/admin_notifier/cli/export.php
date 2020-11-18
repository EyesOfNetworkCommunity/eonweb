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
 
// Check if notifier files are writable
if(!fopen($path_notifier_methods, "w")) {
	echo "$path_notifier_methods is not writable\n";
	exit(3);
}
if(!fopen($path_notifier_rules, "w")) {
	echo "$path_notifier_rules is not writable\n";
	exit(3);
}

// Init methods file
$notifier_methods = new DOMDocument ();
$notifier_methods->preserveWhiteSpace = false;
$notifier_methods->formatOutput = true;
$node_configs = $notifier_methods->createElement("configs");
$notifier_methods->appendchild($node_configs);

// Init rules file
$notifier_rules = new DOMDocument ();
$notifier_rules->preserveWhiteSpace = false;
$notifier_rules->formatOutput = true;
$node_rules = $notifier_rules->createElement("rules");
$notifier_rules->appendchild($node_rules);

// Export configs
$configs_req = sql($database_notifier,"SELECT name,value,type from configs order by name");
foreach($configs_req as $config) {
	if($config["type"]=="cfg") {
		$node_configs->appendchild($notifier_methods->createElement($config["name"],$config["value"]));
	}
	elseif($config["type"]=="rules") {
		$node_rules->appendchild($notifier_rules->createElement($config["name"],$config["value"]));	
	}
}

// Export methods
$node_commands = $notifier_methods->createElement("commands");
$node_configs->appendchild($node_commands);
$node_host = $notifier_methods->createElement("host");
$node_commands->appendchild($node_host);
$node_service = $notifier_methods->createElement("service");
$node_commands->appendchild($node_service);

$methods_req = sql($database_notifier,"SELECT name,line,type from methods order by name");
foreach($methods_req as $method) {
	$method_line="\n\t".$method["name"]." = ".$method["line"];
	if($method["type"]=="host") {
		$method_host = $notifier_methods->createTextNode($method_line);
		$node_host->appendchild($method_host);
	}
	elseif($method["type"]=="service") {
		$method_service = $notifier_methods->createTextNode($method_line);
		$node_service->appendchild($method_service);
	}
}

$node_host->appendchild($notifier_methods->createTextNode("\n    "));
$node_service->appendchild($notifier_methods->createTextNode("\n    "));

// Export rules
$node_host = $notifier_rules->createElement("host");
$node_rules->appendchild($node_host);
$node_service = $notifier_rules->createElement("service");
$node_rules->appendchild($node_service);

$rules_sql="SELECT rules.id,rules.name as name,rules.type as type,debug,contact,host,service,state,notificationnumber,
	timeperiods.daysofweek as daysofweek, timeperiods.timeperiod as timeperiod, rules.tracking as tracking, GROUP_CONCAT(methods.name) as methods
	FROM rules,timeperiods,methods,rule_method
	WHERE rules.timeperiod_id=timeperiods.id
	AND rules.id = rule_method.rule_id
	AND methods.id = rule_method.method_id
	AND methods.type = rules.type
	GROUP BY rules.name
	ORDER by rules.sort_key";

$rules_req = sql($database_notifier,$rules_sql);
foreach($rules_req as $rule) {
	$rule_line="\n\t".$rule["debug"].":".$rule["contact"].":".$rule["host"].":".$rule["service"].":".$rule["state"];
	$rule_line.=":".$rule["daysofweek"].":".$rule["timeperiod"].":".$rule["notificationnumber"].":".$rule["methods"].":".$rule["tracking"];
	if($rule["type"]=="host") {
		$rule_host = $notifier_rules->createTextNode($rule_line);
		$node_host->appendchild($rule_host);
	}
	elseif($rule["type"]=="service") {
		$rule_service = $notifier_rules->createTextNode($rule_line);
		$node_service->appendchild($rule_service);
	}
}

$contacts_sql="SELECT DISTINCT name,debug from contacts ORDER BY name";

$contacts_req = sql($database_notifier,$contacts_sql);
foreach($contacts_req as $contact) {
	$rule_line_host = "\n\t".$contact["debug"].":".$contact["name"].":*:-:*:*:*:*:-";
	$rule_host = $notifier_rules->createTextNode($rule_line_host);
	$node_host->appendchild($rule_host);
	
	$rule_line_service = "\n\t".$contact["debug"].":".$contact["name"].":*:*:*:*:*:*:-";
	$rule_service = $notifier_rules->createTextNode($rule_line_service);
	$node_service->appendchild($rule_service);
}
	
$node_host->appendchild($notifier_rules->createTextNode("\n  "));
$node_service->appendchild($notifier_rules->createTextNode("\n  "));

// Write methods file
$notifier_methods_xml = $notifier_methods->saveXML($notifier_methods->documentElement);
file_put_contents($path_notifier_methods,$notifier_methods_xml."\n");

// Write rules file
$notifier_rules_xml = $notifier_rules->saveXML($notifier_rules->documentElement);
file_put_contents($path_notifier_rules,$notifier_rules_xml."\n");

?>
