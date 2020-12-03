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
include("../../include/function.php");

// Search function for Jquery an exit
if(isset($_GET['term']) && isset($_GET['request']) && $_GET['request'] == "search_user") {
	$sql="select * from ldap_users_extended where (user LIKE ?) OR (login LIKE ?) order by user";
	$result=sql($database_eonweb,$sql,array("%".$_GET['term']."%","%".$_GET['term']."%"));
	
	$array = array();
	foreach($result as $line){
		array_push($array, $line[0]);
	}
	echo json_encode($array);
}

?>
