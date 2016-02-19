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
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<?php include("../../include/include_module.php"); ?>
</head>
<body id="main">

<h1><?php echo $xmlmodules->getElementsByTagName("capacity_per_device")->item(0)->getAttribute("title");?></h1>

<form action='capa_d_right.php' method='GET' target='right'>
  	<h2>host :</h2>
	<?php get_host_listbox_from_cacti();?><br>
	<h2>date :</h2>
	<select name='date'>
		<option value='1'>day</option>
		<option value='2'>week</option>
		<option value='3'>month</option>
		<option value='4'>year</option>
	</select><br><br>
	<input class="button" type=submit name=submit value='Show Graph'>
</form>

<iframe NORESIZE src="capa_d_right.php" name="right" frameborder="0" style="position:absolute;top:50px;left:300px;bottom:0px;" height="90%" width="70%">

</body>
</html>
