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

$(document).ready(function() {
	if($('#snmp_version').val() == "3")
		$('#v3').show();
	else
		$('#v3').hide();
	
	hideShowFormParts();
	$("#loading").hide();
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

// define the tool choosen
function getToolName(){
	var tool_url = $("#tool_list").val();
	var tool_url_part = tool_url.split("/");
	var tool_name = tool_url_part[1].split(".");
	var tool = tool_name[0];
	
	return tool;
}

function hideShowFormParts(){
	
	tool = getToolName();
	
	// hide and show
	if(tool == "snmpwalk" || tool == "interface"){
		$("#port").hide();
		$("#snmp").show();
		if($('#snmp_version').val() == "3"){
			$('#v3').show();
		}else{
			$('#v3').hide();
		}
	}else if(tool == "port"){
		$("#snmp").hide();
		$("#v3").hide();
		$("#port").show();
	}
}

// hide and show form parts according to tool
$("#tool_list").on('change', function(){
	hideShowFormParts();
});

// ajax when form submit
$("#tool-form").on('submit', function(event){
	// stop submit action
	event.preventDefault();
	
	var page = "bylistbox";
	var host_list = $("#host_list").val();
	var tool_list = $("#tool_list").val();
	var snmp_com = $("#snmp_com").val();
	var snmp_version = $("#snmp_version").val();
	var min_port = $("#min_port").val();
	var max_port = $("#max_port").val();
	var username = $("#username").val();
	var password = $("#password").val();
	var snmp_auth_protocol = $("#snmp_auth_protocol").val();
	var snmp_priv_passphrase = $("#snmp_priv_passphrase").val();
	var snmp_priv_protocol = $("#snmp_priv_protocol").val();
	var snmp_context = $("#snmp_context").val();
	
	$.ajax({
		url: 'select_tool.php',
		type: 'POST',
		timeout: 30000,
		data:{
			page: page,
			host_list: host_list,
			tool_list: tool_list,
			snmp_com: snmp_com,
			snmp_version: snmp_version,
			min_port: min_port,
			max_port: max_port,
			username: username,
			password: password,
			snmp_auth_protocol: snmp_auth_protocol,
			snmp_priv_passphrase: snmp_priv_passphrase,
			snmp_priv_protocol: snmp_priv_protocol,
			snmp_context: snmp_context
		},
		beforeSend: function(){
			$("#loading").show();
			$("#result").hide();
		},
		success: function(response){
			$("#error").html("");
			$("#loading").hide();

			if( response.indexOf("<p class='alert alert-danger'>") === 0 ){
				$("#error").html(response);
				$("#loading").hide();
				return;
			}
			
			$("#result").html(response);
			$("#result").show();
		},
		error: function(){
			$("#error").html("<p class='alert alert-danger'><i class='fa fa-exclamation-circle'></i> "+dictionnary["message.ajax_error"]+"</p>");
			$("#loading").hide();
		}
	});
});
