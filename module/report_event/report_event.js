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

/* ########## FUNCTIONS DECLARATION ########## */
/* ~~~~~~~~~~~~~~~~~~~~ NORMAL GRAPH ~~~~~~~~~~~~~~~~~~~~ */
/**
 * Draw the pie chart with selected title, values, in a HTML target
 *
 * @param div_id (String)	-> The HTML target's id
 * @param datas	 (Json)		-> Chart's values in a json array, that's the Ajax response
 */
function drawPieChart(div_id, datas)
{
	$('#'+div_id).highcharts({
		chart: {
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			renderto: 'container',
			margin: '40'
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			text: null
		},
		tooltip: {
			pointFormat: '{series.name} : <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b> : {point.y:.0f}',
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
					}
				}
			},
			connectorColor: 'silver'
		},
		series: [{
			type: 'pie',
			name: 'Value',
			data: [
				{
					name: 'WARNING',
					y: datas.warning,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, graph_color.warning],
							[1, Highcharts.Color(graph_color.warning).brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: 'CRITICAL',
					y: datas.critical,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, graph_color.critical],
							[1, Highcharts.Color(graph_color.critical).brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: 'UNKNOWN',
					y: datas.unknown,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, graph_color.unknown],
							[1, Highcharts.Color(graph_color.unknown).brighten(-0.3).get('rgb')] // darken
						]
					}
				}
			]
		}]
	})
}

/**
 * Draw the column chart with selected title, values, in a HTML target
 *
 * @param div_id (String)	-> The HTML target's id
 * @param datas  (Json)		-> Chart's values in a json array, that's the Ajax response
 */
function drawBarChart(div_id, datas, queue)
{
	// define categories for xAxis
	var categories = barChart_active_categories;
	if(queue == "history"){ categories = barChart_history_categories; }
	
	$('#'+div_id).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			renderto: 'container',
			marginTop: '80',
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			text: null
		},
		xAxis: {
			title: {
				text: false
			},
			categories: categories,
			crosshair: true,
			label: {
				overflow: 'justify'
			}
		},
		yAxis: {
			allowDecimals: false,
			min: 0,
			title: {
				text: false
			}
		},
		tooltip: {
			positioner: function () {
				return { x: 0, y: 0 };
			},
			headerFormat: '<span style="font-size:10px">{point.key}</span><table style="min-width: 150px;">',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			'<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true,
			hideDelay: 200
		},
		plotOptions: {
			column: {
				pointPadding: 0.1,
				borderWidth: 0
			}
		},
		series: [{
			name: 'WARNING',
			data: [datas[0].warning, datas[1].warning, datas[2].warning, datas[3].warning, datas[4].warning],
			color: graph_color.warning
		}, {
			name: 'CRITICAL',
			data: [datas[0].critical, datas[1].critical, datas[2].critical, datas[3].critical, datas[4].critical],
			color: graph_color.critical
		}, {
			name: 'UNKNOWN',
			data: [datas[0].unknown, datas[1].unknown, datas[2].unknown, datas[3].unknown, datas[4].unknown],
			color: graph_color.unknown
		}]
	});
}



/* ~~~~~~~~~~~~~~~~~~~~ SLA GRAPH ~~~~~~~~~~~~~~~~~~~~ */
/**
 * Draw the pie chart with selected title, values, in a HTML target
 *
 * @param div_id (String)	-> The HTML target's id
 * @param datas  (Json)		-> Chart's values in a json array, that's the Ajax response
 */
function drawSlaPieChart(div_id, datas)
{
	$('#'+div_id).highcharts({
		chart: {
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			renderto: 'container',
			margin: '40'
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			text: null
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.y:.0f}',
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
					}
				}
			},
			connectorColor: 'silver'
		},
		series: [{
			type: 'pie',
			name: 'Value',
			data: [
				{
					name: '0-5min',
					y: datas.first,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, '#00CC33'],
							[1, Highcharts.Color('#00CC33').brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: '5-10min',
					y: datas.second,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, '#FFA500'],
							[1, Highcharts.Color('#FFA500').brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: '10-20min',
					y: datas.third,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, '#CC77C6'],
							[1, Highcharts.Color('#CC77C6').brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: '>=20min',
					y: datas.fourth,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, '#FF3300'],
							[1, Highcharts.Color('#FF3300').brighten(-0.3).get('rgb')] // darken
						]
					}
				}
			]
		}]
	})
}

/**
 * Draw the column chart for history events
 *
 * @param div_id (String)	-> The HTML target's id
 * @param datas  (Json)		-> Chart's values in a json array, that's the Ajax response
 */
function drawSlaBarChart(div_id, datas)
{
	var categories = barChart_history_categories;
	$('#'+div_id).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			renderto: 'container',
			marginTop: '80',
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			text: null
		},
		xAxis: {
			title: {
				text: false
			},
			categories: categories,
			crosshair: true,
			label: {
				overflow: 'justify'
			}
		},
		yAxis: {
			allowDecimals: false,
			min: 0,
			title: {
				text: false
			}
		},
		tooltip: {
			positioner: function () {
				return { x: 0, y: 0 };
			},
			headerFormat: '<span style="font-size:10px">{point.key}</span><table style="min-width: 150px;">',
			pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			'<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
			footerFormat: '</table>',
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0
			}
		},
		series: [{
			name: '0-5min',
			data: [datas[0].first, datas[1].first, datas[2].first, datas[3].first, datas[4].first],
			color: '#00CC33'
		}, {
			name: '5-10min',
			data: [datas[0].second, datas[1].second, datas[2].second, datas[3].second, datas[4].second],
			color: '#FFA500'
		}, {
			name: '10-20min',
			data: [datas[0].third, datas[1].third, datas[2].third, datas[3].third, datas[4].third],
			color: '#CC77C6'
		}, {
			name: '>20min',
			data: [datas[0].fourth, datas[1].fourth, datas[2].fourth, datas[3].fourth, datas[4].fourth],
			color: '#FF3300'
		}]
	});
}
/**
 * will adapt autocomplete list according to <select> value
 */
function changeAutocomplete(){
	var field = $('#field').val();
	var type = $('#type').val();
	var queue = '';

	if(type === 'sla'){
		queue = 'history';
	} else {
		queue = 'active';
	}
	
	var datas;
	$.ajax({
		url: 'ajax.php',
		dataType: 'json',
		data: {
			field: field,
			queue: queue
		},
		type: 'POST',
		success: function(response){
			$("#value").autocomplete({ source: response });
		}
	});

	$('#value').focus();
}
/* ########## END OF FUNCTIONS DECLARATION ########## */


$(document).ready(function(){
	changeAutocomplete();
	$('[data-toggle="tooltip"]').tooltip();
});

// <select> change event
$('#field').on('change', function(){
	changeAutocomplete();
});


// form submit event
$("#report-form").on('submit', function(event){
	event.preventDefault();
	
	var type = $("#type").val();
	var field = $("#field").val();
	var field_value = $("#value").val();
	
	var by_day = "";
	var by_week = "";
	var by_month = "";
	var by_year = "";
	
	if($("#by_day").is(':checked')){
		by_day = $("#by_day").val();
	}
	if($("#by_week").is(':checked')){
		by_week = $("#by_week").val();
	}
	if($("#by_month").is(':checked')){
		by_month = $("#by_month").val();
	}
	if($("#by_year").is(':checked')){
		by_year = $("#by_year").val();
	}
	
	$.ajax({
		url: 'graph.php',
		type: 'POST',
		data: {
			type: type,
			field: field,
			'value': field_value,
			by_day: by_day,
			by_week: by_week,
			by_month: by_month,
			by_year: by_year,
			display: "Display"
		},
		success: function(response){
			$("#result").html(response);
		},
		error: function(){ }
	});	
});
