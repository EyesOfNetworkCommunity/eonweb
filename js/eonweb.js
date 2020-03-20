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

$.ajaxSetup({ cache: false });

var path_frame="/module/module_frame/index.php?url=";
var path_nagios_cgi="/thruk/cgi-bin";
var path_nagios_status=path_nagios_cgi+"/status.cgi";

var graph_color = {
	ok: "#00CC33",
	warning: "#FFA500",
	critical: "#FF3300",
	unknown: "#CC77C6"
}

var barChart_active_categories = [
	'0 ~ 5min',
	'5 ~ 15min',
	'15 ~ 30min',
	'30min ~ 1h',
	'more'
];


var barChart_history_categories = [
	'day',
	'week',
	'month',
	'year',
	'more'
];
