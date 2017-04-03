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

// quand on selectionne un user a importer
var import_list = [];


$(document).ready(function() {
	toggleLDAP( $("[name=group_mgt_list]").val() );

	$("#group_location").autocomplete({
		source: "search.php?request=search_group",
		minLength: 3
	});

	$("[name=group_mgt_list]").on('change', function(){
		toggleLDAP( $(this).val() );
	});

	// demande de display des LDAP users
	$("#show_ldap_users").on('click', function(event){
		event.preventDefault();

		import_list = [];

		uncheckSqlGroups();
		var grp_names = getSelectedGroups();
		
		if(grp_names.length < 1){
			$("#errors").html("<p class='alert alert-dismissible alert-warning fade in'>"
								+	"<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"
								+	  "<span aria-hidden='true'>&times;</span>"
								+	"</button>"
								+	"<i class='fa fa-warning'> "
								+	" Aucun groupe n'a été choisit"
							   +"</p>");
			return;
		}

		$.ajax({
			url: 'ajax.php',
			type: 'POST',
			data: {
				group_names: grp_names
			},
			beforeSend: function(){
				$("#loading-modal").modal({
					show: true,
					keyboard: false,
					backdrop: 'static'
				});
			},
			success: function(response){
				$("#loading-modal").modal('hide');

				// detect if ajax's response contains en error message
				var p = $("<div>" + response + "</div>").find('p');
				if( p.hasClass("alert-warning") ){
					$("#errors").html(response);
				} else {
					$("#result").html(response);
				}
			}
		});
	});

		
	$(document).on('click', "[name='user_import[]']", function(){
		var username = $(this).val();
		var group = $(this).parent().parent().find('td:last').html();
		var dn = $(this).parent().parent().find('input[type="hidden"]').val();
		var mail = $(this).parent().next().next().next().html();
		var value = username+"::"+group+"::"+dn+"::"+mail;

		if( $(this).prop('checked') == true ){
			import_list.push(value);
		} else {
			var i = import_list.indexOf(value);
			if(i != -1) {
				import_list.splice(i, 1);
			}
		}

		$("#import_list").empty();
		var usernames = [];
		$.each(import_list, function(key, value){
			var infos = value.split('::');
			var username = infos[0];
			var groupname = infos[1];
			
			if( $.inArray(username, usernames) != -1 ){
				var row_class = "danger";
			} else {
				var row_class = "success";
				usernames.push(username);
			}

			var content = '<tr class="' + row_class + '">';
			content += '<td>'
						+ '<input name="import_list[]" type="hidden" value="' + value + '">' + username + '</td>'
						+ '<td>' + groupname + '</td>'
						+ '</tr>';
			$("#import_list").append(content);
		});
		
	}); 
});

function disable()
{
	if(document.form_group.group_name.disabled){
		document.form_group.group_name.disabled=false;
		document.form_group.group_location.disabled=true;
	}
	else{
		document.form_group.group_name.disabled=true;
		document.form_group.group_location.disabled=false;
	}
}

function toggleLDAP(value)
{
	if( value == "import_user" ){
		// show the import button and hide submit button
		$("#show_ldap_users").removeClass('hidden');
		$("#mgt_group_submit").addClass('hidden');


		// on grise tous les groupes MySQL
		$('.MySQL').each(function( index ){
			$(this).find('input').prop('disabled', true);
		});
	} else {
		// hide the import button and show submit button
		$("#show_ldap_users").addClass('hidden');
		$("#mgt_group_submit").removeClass('hidden');

		$('.MySQL').each(function( index ){
			$(this).find('input').prop('disabled', false);
		});
	}
}

function getSelectedGroups()
{
	var grp_names = [];
	$("input:checkbox:checked").each(function(index){
		var grp_name = $(this).parent().parent().find('td').find('a').html();
		grp_names.push(grp_name);
	});

	return grp_names;
}

function uncheckSqlGroups()
{
	$("input:checkbox:checked").each(function(index){
		var type = $(this).parent().parent().attr('class');
		if(type == "MySQL"){
			$(this).prop('checked', false);
		}
	});
}
