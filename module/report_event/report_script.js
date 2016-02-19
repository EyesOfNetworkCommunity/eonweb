/* ~~~~~~~~~~~~~~~~~~~~ NORMAL GRAPH ~~~~~~~~~~~~~~~~~~~~ */

/**
 * Will display the graph for normal reports
 *
 * @params graph_type	(String)	-> The type of the graph (pie or bar)
 * @params id_html		(String)	-> The html tag's id
 * @params file			(String)	-> The file which define graph's infos (legends, ect...)
 * @params start_date	(timestamp) -> The begin of the report time range
 * @params end_date		(timestamp) -> The end of the report time range
 * @params event_state	(String) 	-> The state of events (active or history)
 * @params filter_field	(String)	-> The name of the field we want to filter
 * @params filter_value	(String)	-> The value of the field we have filtered
 * @params special		(String)	-> Is this a special graph(for legends) ??
 */
function getGraph(graph_type, id_html, file, start_date, end_date, event_state, filter_field, filter_value, special)
{
	$.ajax({
		url: "../../include/report.php",
		cache: false,
		data: {
			"file": file,
			"start_date": start_date,
			"end_date": end_date,
			"event_state": event_state,
			"filter_field": filter_field,
			"filter_value": filter_value
		},
		dataType: "JSON",
		success: function(response){
			var length = 0;
			for(i in response){ length++; }
			if(length > 1)
			{
				if(graph_type == "pie"){ drawPieChart(id_html, response); }
				else{
					if(special == "yes"){ drawBarChartHistory(id_html, response); }
					else{ drawBarChart(id_html, response); }
				}
			}
			else
			{
				$('#'+id_html).append("<h2 style=\"text-align:center;vertical-align:middle;display:inline-block;margin-top:200px;\">No data to display ...</h2>");
			}
		},
		error: function(){ }
	});
}

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
			width: 430,
			height: 350
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
				size: 170,
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
							[0, datas.warning_color[0]],
							[1, Highcharts.Color(datas.warning_color[0]).brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: 'CRITICAL',
					y: datas.critical,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, datas.critical_color[0]],
							[1, Highcharts.Color(datas.critical_color[0]).brighten(-0.3).get('rgb')] // darken
						]
					}
				},
				{
					name: 'UNKNOWN',
					y: datas.unknown,
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 0.8 },
						stops: [
							[0, datas.unknown_color[0]],
							[1, Highcharts.Color(datas.unknown_color[0]).brighten(-0.3).get('rgb')] // darken
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
function drawBarChart(div_id, datas)
{
	$('#'+div_id).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			width: 400,
			height: 350
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
				text: 'Plage Horaire'
			},
			categories: [
				'0 ~ 5min',
				'5 ~ 15min',
				'15 ~ 30min',
				'30min ~ 1h',
				'more'
			],
			label: {
				overflow: 'justify'
			}
		},
		yAxis: {
			allowDecimals: false,
			min: 0,
			title: {
				text: 'Nbr Events'
			}
		},
		tooltip: {
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
			name: 'WARNING',
			data: [datas.warning['5m'], datas.warning['15m'], datas.warning['30m'], datas.warning['1h'], datas.warning.more],
			color: '#FFA500'
		}, {
			name: 'CRITICAL',
			data: [datas.critical['5m'], datas.critical['15m'], datas.critical['30m'], datas.critical['1h'], datas.critical.more],
			color: '#FF3300'
		}, {
			name: 'UNKNOWN',
			data: [datas.unknown['5m'], datas.unknown['15m'], datas.unknown['30m'], datas.unknown['1h'], datas.unknown.more],
			color: '#CC77C6'
		}]
	});
}

/**
 * Draw the column chart for history events
 *
 * @param div_id (String)	-> The HTML target's id
 * @param datas  (Json)		-> Chart's values in a json array, that's the Ajax response
 */
function drawBarChartHistory(div_id, datas)
{
	$('#'+div_id).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			width: 400,
			height: 350
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
				text: 'Plage Horaire'
			},
			categories: [
				'day',
				'week',
				'month',
				'year',
				'more'
			],
			label: {
				overflow: 'justify'
			}
		},
		yAxis: {
			allowDecimals: false,
			min: 0,
			title: {
				text: 'Nbr Events'
			}
		},
		tooltip: {
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
			name: 'WARNING',
			data: [datas.warning.day, datas.warning.week, datas.warning.month, datas.warning.year, datas.warning.more],
			color: '#FFA500'
		}, {
			name: 'CRITICAL',
			data: [datas.critical.day, datas.critical.week, datas.critical.month, datas.critical.year, datas.critical.more],
			color: '#FF3300'
		}, {
			name: 'UNKNOWN',
			data: [datas.unknown.day, datas.unknown.week, datas.unknown.month, datas.unknown.year, datas.unknown.more],
			color: '#CC77C6'
		}]
	});
}



/* ~~~~~~~~~~~~~~~~~~~~ SLA GRAPH ~~~~~~~~~~~~~~~~~~~~ */

/**
 * Will display the graph for sla reports
 *
 * @params graph_type	(String)	-> The type of the graph (pie or bar)
 * @params id_html		(String)	-> The html tag's id
 * @params file			(String)	-> The file which define graph's infos (legends, ect...)
 * @params start_date	(timestamp)	-> The begin of the report time range
 * @params end_date		(timestamp)	-> The end of the report time range
 * @params event_state	(String)	-> The state of events (active or history)
 * @params filter_field	(String)	-> The name of the field we want to filter
 * @params filter_value	(String)	-> The value of the field we have filtered
 */
function getSlaGraph(graph_type, id_html, file, start_date, end_date, event_state, filter_field, filter_value)
{
	$.ajax({
		url: "../../include/report.php",
		cache: false,
		data: {
			"file": file,
			"start_date": start_date,
			"end_date": end_date,
			"event_state": event_state,
			"filter_field": filter_field,
			"filter_value": filter_value
		},
		dataType: "JSON",
		success: function(response){
			var length = 0;
			for(i in response){ length++; }
			if(length > 1)
			{
				if(graph_type == "pie"){ drawSlaPieChart(id_html, response); }
				else{ drawSlaBarChart(id_html, response); }
			}
			else
			{
				$('#'+id_html).append("<h2 style=\"text-align:center;vertical-align:middle;display:inline-block;margin-top:200px;\">No data to display ...</h2>");
			}
		},
		error: function(){ }
	});
}

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
			width: 430,
			height: 350
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
				size: 170,
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
					y: datas["0-5min"],
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
					y: datas["5-10min"],
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
					y: datas["10-20min"],
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
					y: datas[">=20min"],
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
	$('#'+div_id).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			width: 400,
			height: 350
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
				text: 'Plage Horaire'
			},
			categories: [
				'day',
				'week',
				'month',
				'year',
				'more'
			],
			label: {
				overflow: 'justify'
			}
		},
		yAxis: {
			allowDecimals: false,
			min: 0,
			title: {
				text: 'Nbr Events'
			}
		},
		tooltip: {
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
			data: [datas["0-5min"].day, datas["0-5min"].week, datas["0-5min"].month, datas["0-5min"].year, datas["0-5min"].more],
			color: '#00CC33'
		}, {
			name: '5-10min',
			data: [datas["5-10min"].day, datas["5-10min"].week, datas["5-10min"].month, datas["5-10min"].year, datas["5-10min"].more],
			color: '#FFA500'
		}, {
			name: '10-20min',
			data: [datas["10-20min"].day, datas["10-20min"].week, datas["10-20min"].month, datas["10-20min"].year, datas["10-20min"].more],
			color: '#CC77C6'
		}, {
			name: '>20min',
			data: [datas[">20min"].day, datas[">20min"].week, datas[">20min"].month, datas[">20min"].year, datas[">20min"].more],
			color: '#FF3300'
		}]
	});
}
