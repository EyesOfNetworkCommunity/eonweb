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
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
</head>
<body id="main">

<h1><?php echo $xmlmodules->getElementsByTagName("report_performance")->item(0)->getAttribute("title")?></h1><br>

<?php
$timespan["end_now"] = time();
$end_year = date("Y",$timespan["end_now"]);
$end_month = date("m",$timespan["end_now"]);
$end_day = date("d",$timespan["end_now"]);
$end_hour = date("H",$timespan["end_now"]);
$end_min = date("i",$timespan["end_now"]);
$end_sec = 00;
?>


<form action='display.php' method='GET'>
	<div id="search">
        <h2>Select the periode :</h2><br>
	<select name='date'>
		<option value='today'>Today</option>
		<option value='lastday'>Last Day</option>
                <option value='lastweek'>Last Week</option>
                <option value='last2week'>Last 2 Week</option>
                <option value='lastmonth'>Last Month</option>
                <option value='last2month'>Last 2 Month</option>
                <option value='lastyear'>Last Year</option>
        </select><br><br>
        <h2>Search for title :</h2><br>
	<input type="text" name="title" class="value" autocomplete="off" onFocus='$(this).autocomplete(<?php echo get_title_list_from_cacti();?>)'><br><br>
	<input class=button type=submit value=Display></input>
	</div>
<form>
</body>
</html>
