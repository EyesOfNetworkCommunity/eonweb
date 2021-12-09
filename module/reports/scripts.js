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

$(document).ready(function(){
	$('.js-example-basic-multiple').select2();

	$("#dropdownCron").change(function () {
		var input = $("#dropdownCron option:selected").text();
		var val = $(this).val();

		if(input == "custom") {
			$("#dynamic_fields_var4").append('<div id="custom-cron-input" class="form-group"><label class="control-label col-sm-2" for="report_cron_custom"></label><div class="col-sm-6"><input type="text" class="form-control" id="report_cron_custom" name="report_cron_custom" placeholder="* * * * *" value="' + val + '" required></div></div>');
		} else {
			$("#custom-cron-input").remove();
		}
	});
});
