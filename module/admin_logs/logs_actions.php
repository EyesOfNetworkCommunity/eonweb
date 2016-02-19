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

include("../../include/include_module.php");
include("LogBrowser.php");

$LogBrowser=new LogBrowser();

?>
<script>
$(document).ready(function() {
        $("table")
        .tablesorter({widthFixed: false,widgets: ['zebra']})
        .tablesorterPager({container: $("#pager")});
});
</script>
<?php

$request="select * from logs where id like '%'";

foreach($_POST as $col => $val) {
	if($col!="date")
		$request=$request." and $col like '%$val%'";
	elseif($val!="") {
        	$date=explode(" - ",$val);
                $date_start=explode("/",$date[0]);
                $start=mktime("0","0","0",$date_start[1],$date_start[0],$date_start[2]);
		if(isset($date[1])){
                	$date_end=explode("/",$date[1]);
                	$end=mktime("24","00","00",$date_end[1],$date_end[0],$date_end[2]);
		}
                else
                	$end=mktime("24","00","00",$date_start[1],$date_start[0],$date_start[2]);
		$request=$request." and $col >= '$start' and $col <= '$end'";
	}
}

$request=$request." order by id desc;";

$result=sqlrequest($database_eonweb,$request);

$LogBrowser->showTable($result);
$LogBrowser->showTablePager();

?>
