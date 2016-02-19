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

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php include("./include/include.php"); ?>
<script type="text/javascript" src="./js/jquery.dateformat.min.js"></script>
<script type="text/javascript" src="./js/jquery.cookie.js"></script>
<script type="text/javascript" src="./js/jquery.epiclock.min.js"></script>

<script type="text/javascript">
	if (window !=top ) {top.location=window.location;}
	$(document).ready(function() {
		$(".zone_menuside").height($(window).height() - 97);
		$(".zone_main").height($(window).height() - 97);
		$(".zone_limited").height($(window).height() - 97);
		return false;
	});
	$(window).resize(function(){
	 	$(".zone_menuside").height($(window).height() - 97);
                $(".zone_main").height($(window).height() - 97);
                $(".zone_limited").height($(window).height() - 97);
		return false;
	});
</script>

</head>

<body class="index">

<div id="zone_header" class="zone_header">
	<?php include("header.php"); ?>
</div>

<?php 
if(isset($_COOKIE["active_tab"]))  
	$defaulttab=$_COOKIE["active_tab"];
if(isset($_COOKIE["active_page"]))  
	$defaultpage=$_COOKIE["active_page"];
?>

<?php if($_COOKIE["user_limitation"]!="1") { ?>
	<iframe class="zone_menuside" name="menuside" src="side.php?tabid=<?php echo $defaulttab?>" scrolling="auto" frameBorder="0"></iframe>
	<iframe class="zone_main" name="main" src="<?php echo $defaultpage?>" scrolling="auto" frameBorder="0"></iframe>
<?php 
} else { 
	if(!isset($_COOKIE["active_page"]))
        	$defaultpage="module/monitoring_ged/ged_dashboard.php";
?>
	<iframe class="zone_limited" name="main" src="<?php echo $defaultpage?>" scrolling="auto"></iframe>
<?php }?>

<div class="zone_footer">
	<a href="http://www.eyesofnetwork.com" target="_blank">EyesOfNetwork</a> <?php echo $xmlmenus->getElementsByTagName("footer")->item(0)->nodeValue?>
</div>

</body>

</html>
