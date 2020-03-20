/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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

// Switch beetween MYSQL and LDAP display
function disable(){
	if(document.getElementById('ldap').checked){
		document.form_auth.ldap_ip.disabled=false;
		document.form_auth.ldap_port.disabled=false;
		document.form_auth.ldap_search.disabled=false;
		document.form_auth.ldap_user.disabled=false;
		document.form_auth.ldap_password.disabled=false;
		document.form_auth.ldap_rdn.disabled=false;
		//document.form_auth.ldap_filter.disabled=false;
		document.form_auth.ldap_user_filter.disabled=false;
		document.form_auth.ldap_group_filter.disabled=false;
	}
	else{
		document.form_auth.ldap_ip.disabled=true;
		document.form_auth.ldap_port.disabled=true;
		document.form_auth.ldap_search.disabled=true;
		document.form_auth.ldap_user.disabled=true;
		document.form_auth.ldap_password.disabled=true;
		document.form_auth.ldap_rdn.disabled=true;
		//document.form_auth.ldap_filter.disabled=true;
		document.form_auth.ldap_user_filter.disabled=true;
		document.form_auth.ldap_group_filter.disabled=true;
	}
}

disable();
