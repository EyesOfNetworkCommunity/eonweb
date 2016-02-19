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
include("EventBrowser.php");

$EventBrowser=new EventBrowser();
$type=null;
$filter=null;

if(isset($_GET["q"]))
        $q=$_GET["q"];
else
       	$q=$_POST["q"];

?>
<script "text/javascript">
$(document).ready(function() { 
	changeRefresh();
	setContextMenu(); 
        $("table")
        .tablesorter({widthFixed: false})
        .tablesorterPager({container: $("#pager")});
	return false;
});
</script>
<?php

# --- events actions
if(isset($_POST["action"])) {

	if(isset($_POST["line"])){
		$_POST["actioncheck"][0]=$_POST["line"];
	}
	
	switch ($_POST["action"]) {
	# --- add comment
	  case "1":
	    $EventBrowser->updateEvent($_POST["actioncheck"],$_POST["comment"]);
 	    break;
	# --- add owner
	  case "2":
  	    $EventBrowser->updateEvent($_POST["actioncheck"],"own");
 	    break;
	# --- delete owner
       	  case "3":	
      	    $EventBrowser->updateEvent($_POST["actioncheck"],"disown");
       	    break;
	# --- acknowledge
          case "4":
	    if($q=="active")
  	    	$EventBrowser->updateEvent($_POST["actioncheck"],"own");
   	    $EventBrowser->deleteEvent($q,$_POST["actioncheck"]);
      	    break;
	# --- add comment + acknowledge
	  case "5":
	    $EventBrowser->updateEvent($_POST["actioncheck"],$_POST["comment"],true);
	    $EventBrowser->deleteEvent($q,$_POST["actioncheck"]);
	    break;
	}

}
	
# --- events search
if(isset($_POST["value"])){

	$type=$_POST["type"];
        $filter=array(
       		"field"         => $_POST["field"],
               	"value"         => $_POST["value"],
	        "datepicker"    => $_POST["datepicker"],
		"duration"	=> (isset($_POST["duration"])) ? $_POST["duration"] : false,
	        "ok"            => (isset($_POST["ok"])) ? $_POST["ok"] : false,
	        "warning"       => (isset($_POST["warning"])) ? $_POST["warning"] : false,
	        "critical"      => (isset($_POST["critical"])) ? $_POST["critical"] : false,
	        "unknown"       => (isset($_POST["unknown"])) ? $_POST["unknown"] : false,
	);

}

$EventBrowser->showTable($q,$type,$filter);
$EventBrowser->showTablePager();

?>
