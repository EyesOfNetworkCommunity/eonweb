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

<?php
include("../../include/include_module.php");
include("LogBrowser.php");
?>

<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.ui.js"></script>
<script type="text/javascript" src="/js/ui/i18n/ui.datepicker-<?php echo $langformat?>.js"></script>
<script type="text/javascript" src="/js/jquery.date.js"></script>
<script type="text/javascript" src="/js/jquery.metadata.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="/js/jquery.tablesorter.pager.js"></script>
<script type="text/javascript" src="/js/jquery.contextmenu.js"></script>
<script type="text/javascript" src="logs.js"></script>

</head>

<body id="main">

<h1><?php echo $xmlmodules->getElementsByTagName("admin_logs")->item(0)->getAttribute("title")?></h1>
<div id="logs_messages" align="right">
        <i>no screen refresh</i>
	<br><br>
</div>

<form method="post" onsubmit="return submitFormAjax()">

<!-- LOGBROWSER MODULE -->

<?php
$result=sqlrequest($database_eonweb,"select * from logs order by id desc;");
$LogBrowser=new LogBrowser();
$LogBrowser->showSearch($result);
$LogBrowser->showMsg();
$LogBrowser->showTable();
$LogBrowser->showTablePager();
?>

<!-- END LOGBROWSER MODULE -->

</form>
</body>

</html>
