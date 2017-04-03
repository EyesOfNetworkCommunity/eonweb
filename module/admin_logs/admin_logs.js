/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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
	$("#loading").hide();
});

function submitFormAjax()
{
	txt = $("#logs-form").serialize();
	
	$('#result').ajaxStart(function(){
		$('#result').empty();
		$('#loading').show();
	});

	$.ajax({
		type: "POST",
		url: "ajax_logs.php",
		data: txt,
		success: function(res){
			$('#result').html(res);
		}
	});
	
	$('#result').ajaxStop(function(){
		$('#loading').hide();
		$("#result").show();
	});

	return false;
}
