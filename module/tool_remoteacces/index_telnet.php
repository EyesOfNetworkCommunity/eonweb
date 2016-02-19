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
$CONF="./telnet/telnet.conf";
$HOST_NAME=$_GET['host_name'];	
$host=$_GET['host'];


echo "<h2>Remote Control : telnet  - Host : $HOST_NAME </h2><br>";

echo
"<applet archive='./telnet/jta25.jar' 
	 code='de.mud.jta.Applet' 
	 width='100%' 
	 height='92%'>
         <param name=config value='".$CONF."'>
         <param name='Socket.host' value='".$host."'>
         <param name='Terminal.resize' value='screen'>
</applet>";

?>

</body>

</html>
