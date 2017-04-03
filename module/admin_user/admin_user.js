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

function disable(){
	if($('#form_user').length > 0){
		if(document.form_user.user_name.disabled){
			document.form_user.user_name.disabled=false;
			document.form_user.user_password1.disabled=false;
			document.form_user.user_password2.disabled=false;
			document.form_user.user_location.disabled=true;
		}
		else{
			document.form_user.user_name.disabled=true;
			document.form_user.user_password1.disabled=true;
			document.form_user.user_password2.disabled=true;
			document.form_user.user_location.disabled=false;
		}
	}
}

function disable_group(){
	if($('#form_user').length > 0){
		if(document.form_user.user_group.disabled){
			document.form_user.user_group.disabled=false;
		}
		else{
			document.form_user.user_group.disabled=true;
		}
	}
}

$(function() {
	if($('#form_user').length > 0){
		/*$("#user_location").autocomplete("search.php?request=search_user");
		$("#user_location").result(function(event, data, formatted) {
			if (data)
				$(this).parent().find("input").val(data[1]);
		});*/
		$("#user_location").autocomplete({
			source: "search.php?request=search_user",
			minLength: 3
		});
	}
});
