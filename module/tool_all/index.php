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
			<h1 class="page-header"><?php echo getLabel("label.tool_all.title_hostlist"); ?></h1>
		</div>
	</div>
	
	<!-- error messages here ! -->
	<div id="error"></div>
	
	<div class="row">
	
		<form id="tool-form">
			
				<div class="col-md-4">

					<div class="form-group">
						<?php get_host_listbox_from_nagios(); ?>
					</div>
					
					<div class="form-group">
						<?php get_tool_listbox();?>
					</div>
					
					<div id="snmp">
						<div id="v1" class="form-group">
							<label><?php echo getLabel("label.tool_all.snmp_community"); ?></label>
							<input id="snmp_com" class="form-control" type="text" name="snmp_com">
						</div>
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.snmp_version"); ?></label>
							<select class="form-control" id="snmp_version" name="snmp_version" onchange="changeSnmp();">
								<option value="1">version 1</OPTION>
								<option value="2c" selected="selected">version 2c</OPTION>
								<option value="3">version 3</OPTION>
							</select>
						</div>
					</div>
					
					<div id="port" class="form-group" style="display:none;">
						<?php get_toolport_ports(); ?>
					</div>
					
					<!-- SNMP v3 form -->
					<div id="v3" style="display:none;">
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.username"); ?></label>
							<input id="username" class="form-control" type="text" NAME="username">
						</div>
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.password"); ?></label>
							<input id="password" class="form-control" type="password" NAME="password">
						</div>
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.auth_protocol"); ?></label>
							<select class="form-control" id="snmp_auth_protocol" name="snmp_auth_protocol">
								<option value="MD5" selected="selected">MD5 (default)</option>
								<option value="SHA">SHA</option>
							</select>
						</div>
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.privacy_passphrase"); ?></label>
							<input class="form-control" id="snmp_priv_passphrase" name="snmp_priv_passphrase" value="" type="password">
						</div>
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.privacy_protocol"); ?></label>
							<select class="form-control" id="snmp_priv_protocol" name="snmp_priv_protocol">
								<option value="" selected="selected">[None]</option>
								<option value="DES">DES (default)</option>
								<option value="AES128">AES</option>
							</select>
						</div>
						<div class="form-group">
							<label><?php echo getLabel("label.tool_all.context"); ?></label>
							<input class="form-control" id="snmp_context" name="snmp_context" value="" type="text">
						</div>
					</div>
				</div>
				
				<div class="col-md-8">
					<!-- Loading message -->
					<div id="loading" style="display:none;"><?php echo getLabel("message.loading"); ?></div>
					
					<!-- Result here ! -->
					<div id="result" style="display:none;"></div>
				</div>
				
		</form>	
		
	</div>
	
</div>

<?php include("../../footer.php"); ?>
