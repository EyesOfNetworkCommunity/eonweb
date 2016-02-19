function ajaxCharts()
{
	// get the number of host ordered by state (first pie chart)
	$.ajax({
		url: "/include/livestatus/query.php",
		cache: false,
		data: {
			action: "hosts_state"
		},
		dataType: "JSON",
		success: function(response){
			var title = '<a style="font-size:18px;color:#333333;text-decoration:none;" class="graph_title" style="text-decoration:none;" href="/thruk/cgi-bin/status.cgi?hostgroup=all&style=hostdetail">Equipements Nagios</a>';
			drawPieChart("container_hosts_state", title, response, "hostState");
		},
		error: function(){}
	});
	
	// get the number of services ordered by state (second pie chart)
	$.ajax({
		url: "/include/livestatus/query.php",
		cache: false,
		data: {
			action: "services_state"
		},
		dataType: "JSON",
		success: function(response){
			var title = '<a style="font-size:18px;color:#333333;text-decoration:none;" class="graph_title" style="text-decoration:none;" href="/thruk/cgi-bin/status.cgi?host=all">Services Nagios</a>';
			drawPieChart("container_services_state", title, response, "serviceState");
		},
		error: function(){}
	});
	
	// get the number of event ordrered by state (third pie chart)
	$.ajax({
		url: "/include/livestatus/query.php",
		cache: false,
		data: {
			action: "event_state"
		},
		dataType: "JSON",
		success: function(response){
			var title = '<a style="font-size:18px;color:#333333;text-decoration:none;" class="graph_title" style="text-decoration:none;" href="/module/monitoring_ged/ged.php?q=active">Evenements actifs</a>';
			drawPieChart("container_event_state_nbr", title, response, "eventState");
		},
		error: function(){}
	});
	
	// get the number of event ordrered by state, time and owner (column chart)
	$.ajax({
		url: "/include/livestatus/query.php",
		cache: false,
		data: {
			action: "event_state_time"
		},
		dataType: "JSON",
		success: function(response){
			var title = '<a style="font-size:18px;color:#333333;text-decoration:none;" class="graph_title" style="text-decoration:none;" href="/module/monitoring_ged/ged.php?q=active">Evenements actifs</a>';
			drawColumnChart("container_event_state_nbr_by_time", title, response);
		},
		error: function(){}
	});
}


/**
 * draw the pie chart with selected title, values, in a HTML target
 *
 * @param div_id 	-> (String) the HTML target's id
 * @param title 	-> (String) the title of the Chart
 * @param datas 	-> (Json) Chart's values in a json array, that's the Ajax response
 */
function drawPieChart(div_id, title, datas, column_type)
{
	if(column_type == "hostState")
	{
		var begin_url = '/thruk/cgi-bin/status.cgi?';
		var columns = [ 
			["up", "down", "unreachable", "pending"],
			['#00CC33', '#FF3300', '#CC77C6', 'grey'],
			['hostgroup=all&style=hostdetail&hoststatustypes=2', 'hostgroup=all&style=hostdetail&hoststatustypes=4', 'hostgroup=all&style=hostdetail&hoststatustypes=8', 'hostgroup=all&style=hostdetail&hoststatustypes=1']
		];
	}
	else if(column_type == "serviceState")
	{
		var begin_url = '/thruk/cgi-bin/status.cgi?';
		var columns = [ 
			["ok", "warning", "critical", "unknown"],
			['#00CC33', '#FFA500', '#FF3300', '#CC77C6'],
			['servicestatustypes=2&servicestatustype=8&style=detail&hostgroup=all&hoststatustypes=2&hoststatustypes=15', 'servicestatustypes=4&servicestatustype=8&style=detail&hostgroup=all&hoststatustypes=2&hoststatustypes=15', 'servicestatustypes=16&servicestatustype=8&style=detail&hostgroup=all&hoststatustypes=2&hoststatustypes=15', 'servicestatustypes=8&servicestatustype=8&style=detail&hostgroup=all&hoststatustypes=2&hoststatustypes=15']
		];
	}
	else if(column_type == "eventState")
	{
		var begin_url = '/module/monitoring_ged/ged.php?';
		var columns = [ 
			["ok", "warning", "critical", "unknown"],
			['#00CC33', '#FFA500', '#FF3300', '#CC77C6'],
			["q=active&status=0", "q=active&status=1", "q=active&status=2", "q=active&status=3"]
		];
	}
	
	$('#'+div_id).highcharts({
		chart: {
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			height: 350,
			width: 400
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			useHTML: true,
			text: title
		},
		tooltip: {
			pointFormat: '{series.name} : <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				size: 170,
				cursor: 'pointer',
				point:{
					events: {
						click: function(){
							location.href = this.options.url;
						}
					}
				},
				dataLabels: {
					enabled: true,
					format: '<b>{point.name} : {point.y:.0f}</b>',
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
					name: columns[0][0],
					y: datas[0],
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 1 },
						stops: [
							[0, columns[1][0]],
							[1, Highcharts.Color(columns[1][0]).brighten(-0.3).get('rgb')] // darken
						]
					},
					url: begin_url+columns[2][0]
				},
				{
					name: columns[0][1],
					y: datas[1],
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 1 },
						stops: [
							[0, columns[1][1]],
							[1, Highcharts.Color(columns[1][1]).brighten(-0.3).get('rgb')] // darken
						]
					},
					url: begin_url+columns[2][1]
				},
				{
					name: columns[0][2],
					y: datas[2],
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 1 },
						stops: [
							[0, columns[1][2]],
							[1, Highcharts.Color(columns[1][2]).brighten(-0.3).get('rgb')] // darken
						]
					},
					url: begin_url+columns[2][2]
				},
				{
					name: columns[0][3],
					y: datas[3],
					color: {
						radialGradient: { cx: 0.5, cy: 0.5, r: 1 },
						stops: [
							[0, columns[1][3]],
							[1, Highcharts.Color(columns[1][3]).brighten(-0.3).get('rgb')] // darken
						]
					},
					url: begin_url+columns[2][3]
				}
			]
		}]
	})
}

/**
 * draw the column chart with selected title, values, in a HTML target
 *
 * @param div_id 	-> (String) the HTML target's id
 * @param title 	-> (String) the title of the Chart
 * @param datas 	-> (Json) Chart's values in a json array, that's the Ajax response
 */
function drawColumnChart(div_id, title, datas)
{
	var begin_url = "/module/monitoring_ged/ged.php?q=active";
	$('#'+div_id).highcharts({
		chart: {
			type: 'column',
			backgroundColor: 'rgba(255, 255, 255, 0.01)',
			plotShadow: false,
			height: 350,
			width:400
		},
		exporting: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		title: {
			useHTML: true,
			text: title
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
		legend: {
			layout: 'vertical'
		},
		tooltip: {
			positioner: function () {
				return { x: 40, y: 30 };
			},
			headerFormat: '<span style="font-size:10px">{point.key}</span><table style="min-width: 150px; margin:0;">',
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
			},
			series: {
				cursor: 'pointer',
				point: {
					events: {
						click: function() {
							location.href = this.options.url;
						}
					}
				}
			}
		},
		series: [{
			name: 'ok',
			data: [{y:datas[0][0], url:begin_url+'&status=0&time=0-5m'}, {y:datas[1][0], url:begin_url+'&status=0&time=5-15m'}, {y:datas[2][0], url:begin_url+'&status=0&time=15-30m'}, {y:datas[3][0], url:begin_url+'&status=0&time=30m-1h'}, {y:datas[4][0], url:begin_url+'&status=0&time=more'}],
			color: '#00CC33',
		}, {
			name: 'incidents',
			data: [{y:datas[0][1], url:begin_url+'&status=incident&time=0-5m'}, {y:datas[1][1], url:begin_url+'&status=incident&time=5-15m'}, {y:datas[2][1], url:begin_url+'&status=incident&time=15-30m'}, {y:datas[3][1], url:begin_url+'&status=incident&time=30m-1h'}, {y:datas[4][1], url:begin_url+'&status=incident&time=more'}],
			color: '#FFA500'
		}, {
			name: 'incidents not owned',
			data: [{y:datas[0][2], url:begin_url+'&status=incident&own=no&time=0-5m'}, {y:datas[1][2], url:begin_url+'&status=incident&own=no&time=5-15m'}, {y:datas[2][2], url:begin_url+'&status=incident&own=no&time=15-30m'}, {y:datas[3][2], url:begin_url+'&status=incident&own=no&time=30m-1h'}, {y:datas[4][2], url:begin_url+'&status=incident&own=no&time=more'}],
			color: '#FF3300'
		}, {
			name: 'incidents owned',
			data: [{y:datas[0][3], url:begin_url+'&status=incident&own=yes&time=0-5m'}, {y:datas[1][3], url:begin_url+'&status=incident&own=yes&time=5-15m'}, {y:datas[2][3], url:begin_url+'&status=incident&own=yes&time=15-30m'}, {y:datas[3][3], url:begin_url+'&status=incident&own=yes&time=30m-1h'}, {y:datas[4][3], url:begin_url+'&status=incident&own=yes&time=more'}],
			color: '#CC77C6'
		}]
	});
}
