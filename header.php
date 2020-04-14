<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.3
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
session_start();
# Global parameters
include("include/config.php");
include("include/arrays.php");
include("include/function.php");
global $database_eonweb;

# Logos
if(file_exists($path_eonweb.$path_logo_custom)) { $path_logo=$path_logo_custom; }
if(file_exists($path_eonweb.$path_logo_favicon_custom)) { $path_logo_favicon=$path_logo_favicon_custom; }
if(file_exists($path_eonweb.$path_logo_navbar_custom)) { $path_logo_navbar=$path_logo_navbar_custom; }

?>
<!DOCTYPE html>
<html lang="<?php echo getLabel("label._lang"); ?>">

<head>
	<title><?php echo getLabel("label.product.name"); ?></title>
	
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="EyesOfNetwork">
	<meta name="author" content="EyesOfNetwork Team">
	
	<link rel="icon" type="image/png" href="<?php echo $path_logo_favicon; ?>">
	
	<!-- Bootstrap Core CSS -->
	<link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- MetisMenu CSS -->
	<link href="/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
	<!-- DataTables CSS -->
	<link href="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
	<!-- DataTables Responsive CSS -->
	<link href="/bower_components/datatables-responsive/css/dataTables.responsive.css" rel="stylesheet">
	<!-- Custom CSS -->
	<link href="/bower_components/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css" rel="stylesheet">
	<!-- Custom Fonts -->
	<link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<!-- jQuery CSS -->
	<link href="/bower_components/jquery-ui/themes/base/jquery-ui.min.css" rel="stylesheet">
	<!-- DateRangePicker CSS -->
	<link href="/bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
	<!-- BootstrapSelect CSS -->
	<link href="/bower_components/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	
	<!-- EonWeb Custom CSS -->
	<link href="/css/eonweb.css" rel="stylesheet">
	<?php 
	$module_path=basename(dirname($_SERVER["PHP_SELF"]));
	$module_css=$module_path.".css";
	if(file_exists($module_css)) { 
	?><!-- EonWeb Module CSS -->
	<link href="<?php echo $module_css; ?>" rel="stylesheet">
	<?php } ?>

	<?php
	// Select theme
	startSessionTheme();
	$dir = "/srv/eyesofnetwork/eonweb/themes/";
	$listTheme = scandir($dir);
	$verif = 0;
	foreach($listTheme as $value)
	{
		if($value == $_SESSION["theme"]) {
			setcookie("thruk_theme", $_SESSION["theme"],time()+3600,"/thruk/");
			echo '<link rel="stylesheet" type="text/css" href="/themes/'. $_SESSION["theme"] .'/eonweb/custom.css">';
			$verif = 1;
		}
	}
	?>
</head>

<body <?php if($verif==1){ echo 'id='.$_SESSION["theme"];}?>>

<div id="wrapper">
