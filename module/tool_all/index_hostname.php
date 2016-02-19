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
        <script type="text/javascript">
        $(document).ready(function() {
                if($('#snmp_version').val() == "3")
                        $('#v3').show();
        });
        function changeSnmp(){
                if($('#snmp_version').val() == "3"){
                        $('#v1').hide();
                        $('#v3').show();
                }
                else{
                        $('#v3').hide();
                        $('#v1').show();
                }
        }
        </script>
</head>
<body id="main">

<h1><?php echo $xmlmodules->getElementsByTagName("tools")->item(0)->getAttribute("title");?></h1>

<form action="./select_tool.php" method="POST" target="right">
	<h2>Host Name / IP :</h2>
	<input type=text NAME=hostname onFocus='$(this).autocomplete(<?php echo get_host_list();?>)'><br><br>
	<?php get_tool_listbox();?><br>
	<div id="v1">
		<h2>SNMP Community</h2> 
		(show interface only) :<br>
		<input type=text NAME=snmp_com><br><br>
	</div>
	<h2>SNMP Version</h2>
	(show interface only) :<br>
	<select id="snmp_version" name="snmp_version" size=1 onchange="changeSnmp();">
  		<option value="1">version 1</OPTION>
  		<option value="2c" selected="selected">version 2c</OPTION>
                <option value="3">version 3</OPTION>
        </select><br><br>
        <div id="v3" style="display:none;">
                <h2>Username :</h2>
                <input type="text" NAME="username"><br><br>
                <h2>Password :</h2>
                <input type="password" NAME="password"><br><br>
                <h2>Auth protocol :</h2>
                <select id="snmp_auth_protocol" name="snmp_auth_protocol">
                        <option value="MD5" selected="selected">MD5 (default)</option>
                        <option value="SHA">SHA</option>
                </select><br><br>
                <h2>Privacy Passphrase :</h2>
                <input id="snmp_priv_passphrase" name="snmp_priv_passphrase" value="" type="password"><br><br>
                <h2>Privacy Protocol :</h2>
                <select id="snmp_priv_protocol" name="snmp_priv_protocol">
                        <option value="" selected="selected">[None]</option>
                        <option value="DES">DES (default)</option>
                        <option value="AES128">AES</option>
                </select><br><br>
                <h2>Context :</h2>
                <input id="snmp_context" name="snmp_context" value="" type="text"><br><br>
        </div>
	<?php get_toolport_ports();?><br><br>
	<input class="button" type=submit name=run value='Run it !'> 
</form>

<iframe NORESIZE src="right_frame.php" name="right" frameborder="0" style="position:absolute;top:50px;left:300px;bottom:10px;width:65%;height:90%;">

</body>
</html>
