<?php
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

include("../../include/config.php");
include("../../include/arrays.php");
include("../../include/function.php"); 
include("ged_functions.php");

extract($_GET);
if(!isset($queue)) { $queue="active"; } 
elseif(!in_array($queue,$array_ged_queues)) { $queue="active"; }

// get all GED filters
$default = "";
$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";
if(file_exists($file)){
	$xmlfilters = new DOMDocument("1.0","UTF-8");
	$xmlfilters->load($file);

	$xpath = new DOMXPath($xmlfilters);

	$g=$xmlfilters->getElementsByTagName("ged")->item(0);

	//Default filter detection
	$default=$g->getElementsByTagName("default")->item(0)->nodeValue;
}

?>

<form id="ged-table" method="POST" onsubmit="return false;" class="form-inline">

	<div class="form-group datatable_actions_top">
		<div id="ged-action" class="btn-group">
		<?php
		global $database_eonweb;
		$itsm_button = "";
		$itsm = sql($database_eonweb,"SELECT value FROM configs WHERE name=\"itsm\"");
		$itsm = $itsm[0]["value"];
		
		if($queue == "active"){
			if(isset($itsm) && $itsm == "on" ){
				$actions = $array_action_itsm;
			}else{
				$actions = $array_action_option;
			}
		} else {
			$actions = $array_resolve_action_option;
		}
		foreach ($actions as $key => $value) {
			echo "<button id=\"$key\" class=\"btn btn-sm btn-default\" type=\"submit\" name=\"action\">".getLabel($value)."</button>";
		}
		
		?>
		</div>
		
		<button id="select-all1" class="btn btn-sm btn-primary"><?php echo getLabel("action.select_all"); ?></button>
		<button id="unselect-all1" class="btn btn-sm btn-primary hidden"><?php echo getLabel("action.unselect_all"); ?></button>
	</div>
	
	<div class="dataTable_wrapper">
		<table id="events-table" class="table table-striped datatable-eonweb-ajax table-condensed table-hover">
			<thead>
				<tr>
					<th class="col-md-1"><?php echo getLabel("label.state") ?></th>
					<?php
					foreach ($array_ged_packets as $key => $value) {
						if($value["col"] == true && $key != "state"){
							echo "<th>".ucfirst($key)."</th>";
						}
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					if($_GET["type"] == 0){
						$sql = "SELECT pkt_type_id,pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100'";
						$gedsql_result1=sql($database_ged,$sql);
					} else {
						$sql = "SELECT pkt_type_id,pkt_type_name FROM pkt_type WHERE pkt_type_id=? AND pkt_type_id<'100'";
						$prepare=array("i",(int)$_GET["type"]);
						$gedsql_result1=sql($database_ged,$sql,$prepare);
					}
										
					foreach($gedsql_result1 as $ged_type){

						// request for ged events according to queue and filters
						$sql = createSelectClause($ged_type["pkt_type_name"], $queue);
						$mysql_prepare=array("");
						
						// time periods (only in active events);
						if($time_period != ""){
							// define all times needed (for each range)
							$actual_time = time();
							$five_minutes = $actual_time - (60 * 5);
							$fifteen_minutes = $actual_time - (60 * 15);
							$thirty_minutes = $actual_time - (60 * 30);
							$one_hour = $actual_time - (60 * 60);

							switch ($time_period) {
								case '0-5m':
									$sql .= " AND o_sec <= ? AND o_sec > ?";
									$mysql_prepare[0].="ii";
									$mysql_prepare[]=(int)$actual_time;
									$mysql_prepare[]=(int)$five_minutes;
									break;
								case '5-15m':
									$sql .= " AND o_sec <= ? AND o_sec > ?";
									$mysql_prepare[0].="ii";
									$mysql_prepare[]=(int)$five_minutes;
									$mysql_prepare[]=(int)$fifteen_minutes;
									break;
								case '15-30m':
									$sql .= " AND o_sec <= ? AND o_sec > ?";
									$mysql_prepare[0].="ii";
									$mysql_prepare[]=(int)$fifteen_minutes;
									$mysql_prepare[]=(int)$thirty_minutes;
									break;
								case '30m-1h':
									$sql .= " AND o_sec <= ? AND o_sec > ?";
									$mysql_prepare[0].="ii";
									$mysql_prepare[]=(int)$thirty_minutes;
									$mysql_prepare[]=(int)$one_hour;
									break;
								case 'more':
									$sql .= " AND o_sec <= ?";
									$mysql_prepare[0].="i";
									$mysql_prepare[]=(int)$one_hour;
									break;
							}
						}

						if($ack_time != ""){
							$sql .= " AND a_sec - o_sec >= ?";
							$mysql_prepare=array("i",(int)$ack_time);
						}

						// if there's a default filter
						$array_filters = [];
						$array_filters_exploded = [];
						if($default!=""){
							$g_filters = $xpath->query("//ged/filters[@name='$default']/filter");

							$xmlcpt=0;
							foreach($g_filters as $g_filter){
								$array_filters[$xmlcpt][$g_filter->getAttribute("name")] = $g_filter->nodeValue;
								$xmlcpt++;
							}
						}

						// XML filters if activated for the user
						if( count($array_filters) > 0 ){
							$sqlcpt=0;
							for($i=0;$i<$xmlcpt;$i++) {
								foreach ($array_filters[$i] as $key => $value) {
									// advanced search (with *)
									$like = "";
									if( substr($value, 0, 1) === '*' ){
										$like .= "%";
									}
									$like .= trim($value, '*');
									if ( substr($value, -1) === '*' ) {
										$like .= "%";
									}
									if($sqlcpt=="0") {
										$first_node=$like.",%";
										$middle_node="%,".$like.",%";
										$last_node="%,".$like;
										$sql .= " AND ($key LIKE ? OR $key LIKE ? OR $key LIKE ? OR $key LIKE ?";
										$mysql_prepare[0].="ssss";
										$mysql_prepare[]=(string)$like;
										$mysql_prepare[]=(string)$first_node;
										$mysql_prepare[]=(string)$middle_node;
										$mysql_prepare[]=(string)$last_node;
									} else {
										$array_filters_exploded = explode(",",$like);
										foreach($array_filters_exploded as $filter_group) {
											$first_node=$filter_group.",%";
											$middle_node="%,".$filter_group.",%";
											$last_node="%,".$filter_group;
											$sql .= " OR $key LIKE ? OR $key LIKE ? OR $key LIKE ? OR $key LIKE ?";
											$mysql_prepare[0].="ssss";
											$mysql_prepare[]=(string)$filter_group;
											$mysql_prepare[]=(string)$first_node;
											$mysql_prepare[]=(string)$middle_node;
											$mysql_prepare[]=(string)$last_node;
										}
									}
									$sqlcpt++;
								}
							}
							$sql .= ")";
						}

						$sql .= createWhereClause($owner,$filter,$search,$daterange,$ok,$warning,$critical,$unknown);
						$request = sql($database_ged, $sql, array($mysql_prepare[1], $mysql_prepare[2], $mysql_prepare[3], $mysql_prepare[4]), 2);
						foreach($request as $event){
							$event_state = getEventState($event["state"]);
							$row_class = getClassRow($event_state);

							echo '<tr class="'.$row_class.'" name="'.$ged_type["pkt_type_name"].'">';
							createTableRow($event, $event_state, $queue);
							echo "</tr>";
						}
					}
				?>
			</tbody>
		</table>
	</div>

	<div class="form-group">
		<div id="ged-action" class="btn-group">
		<?php
		if($queue == "active"){
			if(isset($itsm) && $itsm == "on" ){
				$actions = $array_action_itsm;
			}else{
				$actions = $array_action_option;
			}
		} else {
			$actions = $array_resolve_action_option;
		}
		foreach ($actions as $key => $value) {
			echo "<button id=\"$key\" class=\"btn btn-sm btn-default\" type=\"submit\" name=\"action\">".getLabel($value)."</button>";
		}
		?>
		</div>
		<button id="select-all2" class="btn btn-sm btn-primary"><?php echo getLabel("action.select_all"); ?></button>
		<button id="unselect-all2" class="btn btn-sm btn-primary hidden"><?php echo getLabel("action.unselect_all"); ?></button>
	</div>
</form>

<script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
<script src="/bower_components/datatables-responsive/js/dataTables.responsive.js"></script>
<script>
	$('.datatable-eonweb-ajax').DataTable({
		responsive: true,
		lengthMenu: [ [25, 50, 100, 250, -1], [25, 50, 100, 250, dictionnary['label.all']] ],
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
</script>
