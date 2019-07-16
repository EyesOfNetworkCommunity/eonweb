/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Quentin HOARAU
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

$('.datatable-eonweb').DataTable({
	responsive: true,
	lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, dictionnary['label.all']] ],
	language: {
		lengthMenu: dictionnary['action.display'] + " _MENU_ " + dictionnary['label.entries'],
		search: dictionnary['action.search']+":",
		paginate: {
			first:      dictionnary['action.first'],
			previous:   dictionnary['action.previous'],
			next:       dictionnary['action.next'],
			last:       dictionnary['action.last']
		},
		info:           dictionnary['label.datatable.info'],
		infoEmpty:      dictionnary['label.datatable.infoempty'],
		infoFiltered:   dictionnary['label.datatable.infofiltered'],
		zeroRecords: 	dictionnary['label.datatable.zerorecords']
	},
	aaSorting: []
});
