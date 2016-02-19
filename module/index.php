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

$module=exec("rpm -q ".$_GET["module"]." |grep '.eon' |wc -l");
if($module!=0)
	header('Location: '.$_GET["link"].'');
else {
?>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>EyesOfNetwork</title>

	<?php
	include("../include/config.php");
	include("../include/arrays.php");
	include("../include/function.php");
	?>

	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache" content="no store" />
	<meta http-equiv="Expires" content="0" />

	<link rel="stylesheet" type="text/css" href="../../css/menu.css" />
	<link rel="stylesheet" type="text/css" href="../../css/style.css" />
	<link rel="stylesheet" type="text/css" href="../../css/design.css" />
</head>

<body id="main">
<h1>EyesOfNetwork Installation</h1>
<?php message(0," : Module ".$_GET["module"]." is not installed",'warning'); ?>
</body>

</html>
<?php
}
?>
