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
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"
/>
        <?php include("../../include/include_module.php"); ?>
</head>

<body id="main">

<?php

# --- Remote action
if(isset($_GET['host_name'])){
	$HOST_NAME=$_GET['host_name'];
	$host=$_GET['host'];
	echo "<h2>Remote Control : ssh - Host : $HOST_NAME </h2><br>";
}
else{
	$HOST_NAME="localhost";
	$host=getenv("SERVER_ADDR");
        echo "<h1>Remote Control : ssh - Host : $HOST_NAME </h1>";
}
	


echo
'<applet width="100%" height="90%" archive="./ssh/SSHTermApplet-signed.jar,./ssh/SSHTermApplet-jdkbug-workaround-signed.jar,./ssh/SSHTermApplet-jdk1.3.1-dependencies-signed.jar" code="com.sshtools.sshterm.SshTermApplet" codebase=".">
 	<param name="sshapps.connection.host" value="'.$host.'">
	<param name="sshapps.connection.userName" value="root">
	<param name="sshapps.connection.connectImmediately" value="true">
	<param name="sshapps.connection.authenticationMethod" value="password">
</applet>';

?>

</body>

</html>
