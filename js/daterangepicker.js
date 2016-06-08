/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
# VERSION : 5.0
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

$('.daterangepicker-eonweb').daterangepicker({
	autoUpdateInput: false,
	locale: {
		format: 'MMMM D, YYYY',
		applyLabel: dictionnary['action.apply'],
		cancelLabel: dictionnary['action.clear'],
		customRangeLabel: dictionnary['label.custom'],
		applyClass: "btn-primary",
		daysOfWeek: [
            dictionnary["calendar.sunday"],
            dictionnary["calendar.monday"],
            dictionnary["calendar.tuesday"],
            dictionnary["calendar.wednesday"],
            dictionnary["calendar.thursday"],
            dictionnary["calendar.friday"],
            dictionnary["calendar.saturday"]
        ],
        monthNames: [
			dictionnary["calendar.january"],
			dictionnary["calendar.february"],
			dictionnary["calendar.march"],
			dictionnary["calendar.april"],
			dictionnary["calendar.may"],
			dictionnary["calendar.june"],
			dictionnary["calendar.july"],
			dictionnary["calendar.august"],
			dictionnary["calendar.september"],
			dictionnary["calendar.october"],
			dictionnary["calendar.november"],
			dictionnary["calendar.december"]
        ]
	},
	ranges: {
		[dictionnary["label.today"]]: [moment(), moment()],
		[dictionnary["label.yesterday"]]: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		[dictionnary["label.last_7_days"]]: [moment().subtract(6, 'days'), moment()],
		[dictionnary["label.last_30_days"]]: [moment().subtract(29, 'days'), moment()],
		[dictionnary["label.this_month"]]: [moment().startOf('month'), moment().endOf('month')],
		[dictionnary["label.last_month"]]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	}
});
	
$('.daterangepicker-eonweb').on('apply.daterangepicker', function(ev, picker) {
	$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('.daterangepicker-eonweb').on('cancel.daterangepicker', function(ev, picker) {
	$(this).val('');
});
