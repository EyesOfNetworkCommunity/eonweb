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

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>EyesOfNetwork</title>
	<?php include("../../include/include_module.php"); ?>
</head>

<body id="main">

<?php
$dashboard = $xmlmodules->getElementsByTagName("about");
$descriptions=$dashboard->item(0)->getElementsByTagName("descriptions")->item(0);
$links=$dashboard->item(0)->getElementsByTagName("links")->item(0);
$contacts=$dashboard->item(0)->getElementsByTagName("contacts")->item(0);
?>

<h1><?php echo $dashboard->item(0)->getAttribute("title")." version ".$version?></h1>

	<br><h2><?php echo $descriptions->getAttribute("name");?> :</h2>
	<p><?php echo $descriptions->getElementsByTagName("description")->item(0)->nodeValue;?></p>
	<p><?php echo $descriptions->getElementsByTagName("description")->item(1)->nodeValue;?></p>
	<br>
	<p><?php echo $descriptions->getElementsByTagName("description")->item(2)->nodeValue;?> :</p>
	<ul class="ul">
		<li><?php echo $descriptions->getElementsByTagName("description")->item(2)->getAttribute("value1");?></li>
		<li><?php echo $descriptions->getElementsByTagName("description")->item(2)->getAttribute("value2");?></li>
		<li><?php echo $descriptions->getElementsByTagName("description")->item(2)->getAttribute("value3");?></li>
		<li><?php echo $descriptions->getElementsByTagName("description")->item(2)->getAttribute("value4");?></li>
	</ul><br>
	
	<h2><?php echo $links->getAttribute("name");?> :</h2>
	<ul class="ul">
	<?php
		$links=$links->getElementsByTagName("link");
		foreach($links as $link)
			echo "<li><a href='".$link->getAttribute("url")."' target='".$link->getAttribute("target")."'>".$link->getAttribute("name")."</a> -  ".$link->getAttribute("description")."</li>";
	?>
		<li>...</li>
	</ul><br>

	<h2><?php echo $contacts->getAttribute("name");?> :</h2>
	<p><?php echo $contacts->getAttribute("description");?> <a href="mailto:<?php echo $contacts->getAttribute("mail");?>"> <?php echo $contacts->getAttribute("mail");?> </a></p>

	<br><br><b><u>EyesOfNetwork Dev Team</u></b><br><br><br>

</body>

</html>
