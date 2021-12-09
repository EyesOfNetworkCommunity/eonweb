/*
#########################################
#
# Copyright (C) 2021 EyesOfNetwork Team
# DEV NAME : Julien Gonzalez
# VERSION : 6.0
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

function generateReport(id, name){
	var formData = new FormData();
	formData.append('id', id);
	formData.append('action',"generateReport");
	$("#report-id-" + id).find(".btn").text("Generating...");
	$.ajax({
		type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
		url		    : 'ajax.php', // the url where we want to POST
		data		: formData, // our data object
		dataType	: 'html', // what type of data do we expect back from the server
		processData: false,
		contentType: false,
		success : function(result){ // success est toujours en place, bien sûr !
			$("#report-id-" + id).find(".btn").text("Generated");
			$("#pdf-url-" + id).append("<a href=\"py/ressources/reports/" + name + "_report.pdf\">PDF</a>");
		},
		error : function(result, statut, erreur){
			$("#result").html('<div class="alert alert-danger" role="alert">'+erreur+'</div>');
		}
	});
}

function deleteReport(id){
	var formData = new FormData();
	formData.append('id', id);
	formData.append('action',"deleteReport");
	$.ajax({
		type		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
		url		    : 'ajax.php', // the url where we want to POST
		data		: formData, // our data object
		dataType	: 'html', // what type of data do we expect back from the server
		processData: false,
		contentType: false,
		success : function(result){ // success est toujours en place, bien sûr !
			$("#line-id-" + id).remove();
		},
		error : function(result, statut, erreur){
			$("#result").html('<div class="alert alert-danger" role="alert">'+erreur+'</div>');
		}
	});
}
