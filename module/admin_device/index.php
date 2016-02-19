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
        <script type="text/javascript">
        $(document).ready(function() {
                if($('#snmp_version').val() == "3")
                        $('#v3').show();
        });
        function changeSnmp(){
                if($('#snmp_version').val() == "3")
                        $('#v3').show();
                else
                        $('#v3').hide();
        }
        </script>
</head>
<body id="main">
<?php
	global $database_cacti;

	# --- Section Import device from nagios to cacti
	if(isset($_POST['Import'])){
		for($i=0;isset($_POST['hosts'][$i]);$i++){

			$host_name=$_REQUEST['hosts'][$i];

			# ---Get ip address
			$request="select address from nagios_host where name='".$host_name."'";
                        $result=sqlrequest($database_lilac,$request);
			$host_address=mysqli_result($result,"0");

			if($_REQUEST['cacti_hostname']!="0") { $host_name=$host_address; }

			$command="";
			if($_REQUEST['snmp_version']=="3"){
                		$username = $_REQUEST['username'];
		                $password = $_REQUEST['password'];
		                $snmp_auth_protocol = $_REQUEST['snmp_auth_protocol'];
		                $snmp_priv_passphrase = $_REQUEST['snmp_priv_passphrase'];
		                $snmp_priv_protocol = $_REQUEST['snmp_priv_protocol'];
		                $snmp_context = $_REQUEST['snmp_context'];

		                if($snmp_context=="")
        	                	$snmp_context="";
                		else
        	        	        $snmp_context="--context=$snmp_context";
	
		                if($snmp_priv_passphrase=="")
        		                $snmp_priv_protocol="--privproto=[None]";
				else
					$snmp_priv_protocol="--privproto=$snmp_priv_protocol --privpass=$snmp_priv_passphrase";

		                $command="--username=$username --password=$password --authproto=$snmp_auth_protocol $snmp_priv_protocol $snmp_context";
			}
			exec("/usr/bin/php $path_eon/cacti/cli/add_device.php --description='".$_REQUEST['hosts'][$i]."' --ip='".$host_name."' --template='".$_REQUEST['snmp_template']."' --avail='snmp' --community='".$_REQUEST['snmp_community']."' --port='".$_REQUEST['snmp_port']."' --version='".$_REQUEST['snmp_version']."' ".$command."");
		}
	}
?>
<?php
	# --- Section delete host and any other data (data source, graph) from cacti 
	if(isset($_POST['Remove'])){
		for($i=0;isset($_POST['hosts_cacti'][$i]);$i++){
			exec("/usr/bin/php $path_eon/cacti/cli/remove_device.php --device-id=".$_REQUEST['hosts_cacti'][$i]."");
		}
	}
?>
	
	<center>
	<form action="index.php" method="post">
	<table class="table">
		<tr>
			<td class="blanc">
				<table class="table">
					<tr>
						<td class="blanc" colspan="2"><h1><?php echo $xmlmodules->getElementsByTagName("admin_device")->item(0)->getAttribute("title")?></h1></td>
					</tr>
					<tr>
						<td class="blanc"><h2>Select host to import:<h2><br><center>
						<?php
							# --- Retrieve hosts array from nagios
							$request="select nagios_host.name,nagios_host_template.name
							from lilac.nagios_host,lilac.nagios_host_template,lilac.nagios_host_template_inheritance 
							where nagios_host.id=nagios_host_template_inheritance.source_host 
							and nagios_host_template.id=nagios_host_template_inheritance.target_template 
							and nagios_host.name NOT IN (select hostname from cacti.host) 
							and nagios_host.address NOT IN (select hostname from cacti.host) 
							order by nagios_host_template.name,nagios_host.name;";
						        $result=sqlrequest($database_cacti,$request);
										
						        print "<SELECT name='hosts[]' class='select' size=15 multiple='multiple'>";

						        while ($line = mysqli_fetch_array($result))
						        {
						                print "<OPTION value='$line[0]'>&nbsp;$line[1] ($line[0])&nbsp;</OPTION>\n";
						        }
						        print "</SELECT>";
						?>	
						<input class="button" type='submit' name ='Import' value='Import'>
						</center>		
						</td>
						<td class="blanc">
							<table class="table">
								<tr>
									<td colspan="2" class="blanc"><center><h2>Select import parameters:</h2></center></td>
								</tr>
								<tr>
									<td><b>Hostname</b><br><font size='1px'>Get hostname or @ip from Nagios</font></td>
									<td>
										<SELECT name="cacti_hostname">
											<OPTION value="0" selected>&nbsp;hostname&nbsp;</OPTION>
											<OPTION value="1">&nbsp;ip address&nbsp;</OPTION>
										</SELECT>
									</td>
								</tr>
								<tr>
									<td><b>Host Template</b><br><font size='1px'>Host template to use</font></td>
									<td>
										<?php
										# --- Retrieve Host template from cacti
										$result=sqlrequest($database_cacti,"SELECT id,name FROM host_template ORDER BY name ASC");
										print "<SELECT name=\"snmp_template\">";
										while ($line = mysqli_fetch_array($result))
										{
											print "<OPTION value='$line[0]'>&nbsp;$line[1]&nbsp;</OPTION>\n";
										}
										print "</SELECT>";
										?>
									</td>
								</tr>
								<tr>
									<?php
                                                                        # --- Retrieve SNMP community from cacti
                                                                        $result=sqlrequest($database_cacti,"SELECT value FROM settings where name='snmp_community'");
									?>
									<td><b>Community</b><br><font size='1px'>Community name to use</font></td>
									<td><input type='text' name='snmp_community' value='<?php echo mysqli_result($result,0,'value')?>'></td>
								</tr>
                                                                <tr>        
									<td><b>Port</b><br><font size='1px'>Port to use</font></td>
                                                                        <td><input type='text' name='snmp_port' value='161'></td>
                                                                </tr>
								<tr>
									<td><b>SNMP Version </b><br><font size='1px'>Snmp version to use</font></td>
									<td>
										<select id='snmp_version' name='snmp_version' onchange='changeSnmp();'>
											<option value='1'>&nbsp;Version 1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
											<option value='2' selected='selected'>&nbsp;Version 2c&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
											<option value='3'>&nbsp;Version 3&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
										</select>
									</td>
								</tr>
                                                                <tr id="v3" style="display:none;">
                                                                        <td><b>SNMP v3 </b><br><font size='1px'>Snmp v3 parameters</font></td>
                                                                        <td>

									                <i>Username :</i><br>
									                <input type="text" name="username"><br><br>
									                <i>Password :</i><br>
									                <input type="password" name="password"><br><br>
									                <i>Auth protocol :</i><br>
									                <select id="snmp_auth_protocol" name="snmp_auth_protocol">
									                        <option value="MD5" selected="selected">MD5 (default)</option>
									                        <option value="SHA">SHA</option>
									                </select><br><br>
									                <i>Privacy Passphrase :</i><br>
									                <input id="snmp_priv_passphrase" name="snmp_priv_passphrase" value="" type="password"><br><br>
									                <i>Privacy Protocol :</i><br>
									                <select id="snmp_priv_protocol" name="snmp_priv_protocol">
									                        <option value="" selected="selected">[None]</option>
									                        <option value="DES">DES (default)</option>
									                        <option value="AES128">AES</option>
									                </select><br><br>
									                <i>Context :</i><br>
											<input id="snmp_context" name="snmp_context" value="" type="text"><br><br>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td class="blanc">
				<table class="table">
					<tr>
						<td class="blanc"><h1><?php echo $xmlmodules->getElementsByTagName("admin_device")->item(0)->getAttribute("title0")?></h1></td>
					</tr>
					<tr>
						<td class="blanc"><h2>Select cacti host to remove:<h2><br><center>
					<?php
							# --- Retrieve host array from cacti
							$result=sqlrequest($database_cacti,"SELECT DISTINCT host.id,hostname,description FROM host ORDER BY hostname ASC");
							print "<SELECT name=\"hosts_cacti[]\" class=\"select\" size=15 multiple>";
							while ($line = mysqli_fetch_array($result))
							{
							    print "<OPTION value='$line[0]'>&nbsp;$line[1] ($line[2])&nbsp;</OPTION>\n";
							}
							print "</SELECT>";
						?>			
						<input class="button" type='submit' name ='Remove' value='Remove'>
						</center>			
						</td>
					</tr>
					<tr>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</center>
	</form>
</body>
</html>
