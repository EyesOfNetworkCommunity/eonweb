<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.2
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

include("../../header.php");
include("../../side.php");

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_device.title"); ?></h1>
		</div>
	</div>
	
	<?php
		global $database_cacti;

		# --- Section Import device from nagios to cacti
		if(isset($_POST['Import'])){
			for($i=0;isset($_POST['hosts'][$i]);$i++){
				
				$host_name=$_REQUEST['hosts'][$i];
				
				# ---Get ip address
				$request = "select address from nagios_host where name=?";
				$result = sql($database_lilac, $request, array($host_name));
				$result = $result[0];
				$host_address=$result["0"];
				
				if($_REQUEST['cacti_hostname']!="0") { $host_name=$host_address; }
				
				$command="";
				if($_REQUEST['snmp_version']=="3"){
					$username = escapeshellarg($_REQUEST['username']);
					$password = escapeshellarg($_REQUEST['password']);
					$snmp_auth_protocol = escapeshellarg($_REQUEST['snmp_auth_protocol']);
					$snmp_priv_passphrase = escapeshellarg($_REQUEST['snmp_priv_passphrase']);
					$snmp_priv_protocol = escapeshellarg($_REQUEST['snmp_priv_protocol']);
					$snmp_context = escapeshellarg($_REQUEST['snmp_context']);
					
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
				exec("/usr/bin/php $path_eon/cacti/cli/add_device.php --description=".escapeshellarg($_REQUEST['hosts'][$i])." --ip=".escapeshellarg($host_name)." --template=".escapeshellarg($_REQUEST['snmp_template'])." --avail='snmp' --community=".escapeshellarg($_REQUEST['snmp_community'])." --port=".escapeshellarg($_REQUEST['snmp_port'])." --version=".escapeshellarg($_REQUEST['snmp_version'])." ".$command."");
			}
		}

		# --- Section delete host and any other data (data source, graph) from cacti
		if(isset($_POST['Remove'])){
			for($i=0;isset($_POST['hosts_cacti'][$i]);$i++){
				exec("/usr/bin/php $path_eon/cacti/cli/remove_device.php --device-id=".escapeshellarg($_REQUEST['hosts_cacti'][$i])."");
			}
		}
	?>
	
	<form action="index.php" method="post">
		<div class="row">
			<div class="col-md-6">
				<h4><?php echo getLabel("label.admin_device.import_settings_title"); ?></h4>
				<div class="row form-group">
					<label class="col-md-4">Hostname</label>
					<div class="col-md-8">
						<select class="form-control" name="cacti_hostname">
							<option value="0" selected>hostname</option>
							<option value="1">ip address</option>
						</select>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-4">Host Template</label>
					<div class="col-md-8">
						<?php
							# --- Retrieve Host template from cacti
							$result=sql($database_cacti,"SELECT id,name FROM host_template ORDER BY name ASC");
							echo "<select class='form-control' name='snmp_template'>";
							foreach($result as $line){
								echo "<option value='$line[0]'>$line[1]</option>";
							}
							echo "</select>";
							?>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-4">Community</label>
					<div class="col-md-8">
						<?php
							# --- Retrieve SNMP community from cacti
							$result=sql($database_cacti,"SELECT value FROM settings where name='snmp_community'");
						?>
						<input class="form-control" type="text" name="snmp_community" value='<?php echo $result[0]["value"]?>'>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-4">Port</label>
					<div class="col-md-8">
						<input class="form-control" type="text" name="snmp_port" value="161">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-4">SNMP Version</label>
					<div class="col-md-8">
						<select class="form-control" id="snmp_version" name="snmp_version">
							<option value="1">Version 1</option>
							<option value="2" selected="selected">Version 2c</option>
							<option value="3">Version 3</option>
						</select>
					</div>
				</div>
			</div>
			<div id="v3" class="col-md-6" style="display: none;">
				<h4><?php echo getLabel("label.admin_device.snmp_v3_title"); ?></h4>
				<div class="row form-group">
					<label class="col-md-5">Username</label>
					<div class="col-md-7">
						<input class="form-control" type="text" name="username">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-5">Password</label>
					<div class="col-md-7">
						<input class="form-control" type="password" name="password">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-5">Auth protocol</label>
					<div class="col-md-7">
						<select class="form-control" id="snmp_auth_protocol" name="snmp_auth_protocol">
							<option value="MD5" selected="selected">MD5 (default)</option>
							<option value="SHA">SHA</option>
						</select>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-5">Privacy Passphrase</label>
					<div class="col-md-7">
						<input class="form-control" id="snmp_priv_passphrase" name="snmp_priv_passphrase" value="" type="password">
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-5">Privacy Protocol</label>
					<div class="col-md-7">
						<select class="form-control" id="snmp_priv_protocol" name="snmp_priv_protocol">
							<option value="" selected="selected">[None]</option>
							<option value="DES">DES (default)</option>
							<option value="AES128">AES</option>
						</select>
					</div>
				</div>
				<div class="row form-group">
					<label class="col-md-5">Context</label>
					<div class="col-md-7">
						<input class="form-control" id="snmp_context" name="snmp_context" value="" type="text">
					</div>
				</div>
			</div>
		</div>
	
		<div class="row">
			<div class="col-md-6">
				<h4><?php echo getLabel("label.admin_device.import_host"); ?></h4>
				<div class="form-group">
					<?php
						# --- Retrieve hosts array from nagios
						$request="select nagios_host.name,nagios_host_template.name
								  from lilac.nagios_host,lilac.nagios_host_template,lilac.nagios_host_template_inheritance 
								  where nagios_host.id=nagios_host_template_inheritance.source_host 
								  and nagios_host_template.id=nagios_host_template_inheritance.target_template 
								  and nagios_host.name NOT IN (select hostname from cacti.host) 
								  and nagios_host.address NOT IN (select hostname from cacti.host) 
								  order by nagios_host_template.name,nagios_host.name;";
						$result=sql($database_cacti,$request);
						
						echo "<select name='hosts[]' class='form-control' size=15 multiple='multiple'>";
						foreach($result as $line){
							echo "<option value='$line[0]'>&nbsp;$line[1] ($line[0])&nbsp;</option>\n";
						}
						echo "</select>";
					?>
				</div>
				<div class="form-group">
					<button class="btn btn-primary" type='submit' name ='Import' value='Import'><?php echo getLabel("action.import"); ?></button>
				</div>
			</div>
			
			
			<div class="col-md-6">
				<h4><?php echo getLabel("label.admin_device.remove_cacti"); ?></h4>
				<div class="form-group">
				<?php
					# --- Retrieve host array from cacti
					$result=sql($database_cacti,"SELECT DISTINCT host.id,hostname,description FROM host ORDER BY hostname ASC");
					echo "<select class='form-control' name='hosts_cacti[]' size=15 multiple='multiple'>";
					foreach($result as $line){
						echo "<option value='$line[0]'>$line[1] ($line[2])</option>";
					}
					echo "</select>";
				?>
				</div>
				<div class="form-group">
					<button class="btn btn-danger" type="submit" name ="Remove" value="Remove"><?php echo getLabel("action.delete"); ?></button>
				</div>
			</div>
		</div>
	</form>

</div>

<?php include("../../footer.php"); ?>
