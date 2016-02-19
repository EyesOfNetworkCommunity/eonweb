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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>EyesOfNetwork</title>
<?php include("./include/include.php"); ?>

<script type="text/javascript">
$(document).ready(function(){
        $("#login").draggable({
                handle: '.handle',
                opacity: '0.5',
                containment: 'document'
        });
        $("input:text").focus();
});
</script>
</head>

<body id="main">
<?php
	logging("logout","User logged out");
	$sessid=$_COOKIE["session_id"];
	setcookie("session_id",FALSE);
	setcookie("user_name",FALSE);
	setcookie("user_id",FALSE);
	setcookie("user_limitation",FALSE);
	setcookie("group_id",FALSE);
	setcookie("active_tab",FALSE);
	setcookie("active_page",FALSE);
        setcookie("nagvis_session",FALSE,0,"/nagvis");
        setcookie("Cacti",FALSE);
        setcookie("clickedFolder",FALSE);
        setcookie("highlightedTreeviewLink",FALSE);
	
	sqlrequest($database_eonweb,"DELETE FROM sessions where session_id='$sessid'");

        echo "<div id='login'>";
        echo "<div>";
        echo "<p>". $xmlmenus->getElementsByTagName("login")->item(0)->nodeValue ."</p><br/>";
        echo "<h2>" .$xmlmenus->getElementsByTagName("login")->item(0)->getAttribute("logout"). "</h2><br><br>";
        echo "<a href='/login.php'> ".$xmlmenus->getElementsByTagName("login")->item(0)->getAttribute("connect")." .... </a>";
        echo "</div>";
        echo "</div>";
?>
</body>

</html>
