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

function get_bps() { 

	$path_thruk_bps="/srv/eyesofnetwork/thruk/bp/";
	$thruk_bps = scandir($path_thruk_bps);
	$thruk_bps_json=array();
	$thruk_bps_deps=array();

	foreach($thruk_bps as $thruk_bp) {
		if(preg_match("#.tbp$#", $thruk_bp)) {
			$thruk_bp_json=json_decode(file_get_contents($path_thruk_bps.$thruk_bp),true);
			$thruk_bps_json[]=$thruk_bp_json;
		}
	}
	 
	foreach($thruk_bps_json as $thruk_bp_json) {
		$thruk_bps_deps[$thruk_bp_json["name"]]=array();
		foreach($thruk_bps_json as $thruk_bp_json_tmp) {
			if($thruk_bp_json_tmp["name"]!=$thruk_bp_json["name"]) {
				foreach($thruk_bp_json_tmp["nodes"] as $thruk_bp_node) {
					if($thruk_bp_node["label"] == $thruk_bp_json["name"]) {
						$thruk_bps_deps[$thruk_bp_json["name"]][]=$thruk_bp_json_tmp["name"];
					}
				}
			}
		}
	}

	ksort($thruk_bps_deps);
	return $thruk_bps_deps;
	
}

function set_downtime($bp,$start,$end,$user,$comment) {
	$CommandFile="/srv/eyesofnetwork/nagios/var/log/rw/nagios.cmd";
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	$cmdline = '['.$timestamp.'] SCHEDULE_HOST_DOWNTIME;'.$bp.';'.$start.';'.$end.';1;0;;'.$user.';'.$comment.''.PHP_EOL;
	file_put_contents($CommandFile, $cmdline, FILE_APPEND);
}

?>
