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

// Xml menus 
$header = $xmlmenus->getElementsByTagName('header');
$menutabs = $xmlmenus->getElementsByTagName('menutab');
$nbrtab=count($menutabs);

// Cookies
$login=$_COOKIE['user_name'];
$usrid=$_COOKIE['user_id'];
$usrlimit=$_COOKIE['user_limitation'];
$grpid=$_COOKIE['group_id'];

if(isset($_COOKIE["active_tab"]))
        $defaulttab=$_COOKIE["active_tab"];
else if(!isset($_COOKIE["active_tab"]) && $usrlimit=="1")
	$defaulttab=1;
if(isset($_COOKIE["active_page"]))
	$defaultpage=$_COOKIE["active_page"];

$usersql=sqlrequest($database_eonweb,"select * from users where user_id = '$usrid';");

// Javascripts menus selections
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#clock').epiclock({mode: $.epiclock.modes.explicit, time: new Date('<?php echo date($dateformat); ?>')});
        $("#<?php echo $defaulttab?>").removeClass("link");
        $("#<?php echo $defaulttab?>").addClass("hover");
	return false;
});
function hoverMenu(menu) {
        $("a").addClass("link");
        $("a").removeClass("hover");
        $(menu).removeClass("link");
        $(menu).addClass("hover");
	return false;
}
</script>
<?php

// Display user informations
echo "<div id='headerinfo'>".$header->item(0)->getAttribute('user')." : ";


// IF NOT LDAP USER
if(mysqli_result($usersql,0,"user_type")!="1"){
	echo "<a href='/module/monitoring_passwd/index.php' target='main'>".$login."</a>";
}
else { echo $login; }

echo " - <a href='./logout.php'>".$header->item(0)->getAttribute('logout')."</a>";
echo "<div id='clock'></div>";
echo "</div>";

// Connect to group access right
if($usrlimit!="1"){
	$grprightsql=sqlrequest($database_eonweb,"select * from groupright where group_id = $grpid");
	$nbrright =  mysqli_num_rows($grprightsql);
	$rightrow = mysqli_fetch_array($grprightsql,MYSQL_NUM);

	if (($nbrtab == '0') || ($nbrright == '0')) {
		message(0,"Could not get tab menu or right in database");
		exit;
	}

	// Create the TAB Menu
	echo "<div id='headermenu'>";
	echo "<ul>";

	foreach($menutabs as $menutab){
		$tabid=$menutab->getAttribute("id");
	        $tabname=$menutab->getAttribute("name");

		// not right - not display
		if ($rightrow[$tabid] == '1') {
			echo "<li>";
			echo "<a id='$tabid' class='link' href='side.php?tabid=$tabid' target='menuside' onclick='hoverMenu(this);'>$tabname</a>";
			echo "</li>";
		}	
	}	

	echo "</ul>";
	echo "</div>";

	// Navigation bar
	$xpath = new DOMXPath($xmlmenus);
	$menutabs = $xpath->query("//menutab[@id='$defaulttab']");
	$menulink = $xpath->query("//link[@url='$defaultpage']");
	$tab_name = $menutabs->item(0)->getAttribute("name");
	$link_name = $menulink->item(0)->getAttribute("name");
	$subtab_name = $menulink->item(0)->parentNode->getAttribute("name");
	$headernav="&nbsp;<b>".ucfirst($tab_name)." -> <i>".ucfirst($subtab_name)."</b> --> ".$link_name."</i>&nbsp;";

}
// Connect to user limited
else {

	$headernav="&nbsp;<i>Limited Access</i>&nbsp;";

?>
	<div id='headermenu'>
        <ul>
        <li>
        	<a id='1' class='link' href='module/monitoring_ged/ged_dashboard.php' target='main' onclick='hoverMenu(this);$.cookie("active_tab","1");$.cookie("active_page","module/monitoring_ged/ged_dashboard.php");'> dashboard </a>
        </li>
        <li>
      		<a id='2' class='link' href='module/monitoring_ged/ged.php?q=active' target='main' onclick='hoverMenu(this);$.cookie("active_tab","2");$.cookie("active_page","module/monitoring_ged/ged.php?q=active");'> events </a>
	</li>
        <li>
        	<a id='3' class='link' href='module/monitoring_ged/ged.php?q=history' target='main' onclick='hoverMenu(this);$.cookie("active_tab","3");$.cookie("active_page","module/monitoring_ged/ged.php?q=history");'> history </a>
        </li>
        </ul>
        </div>
<?php
}

// Navigation bar
echo "<div id='headernav'>".$headernav."</div>";

?>
