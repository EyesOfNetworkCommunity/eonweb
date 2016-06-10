<?php
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

include("../../include/config.php");
include("../../include/arrays.php");
include("../../include/function.php"); 
include("ged_functions.php");

extract($_GET);

?>



<form id="ged-table" method="POST" onsubmit="return false;">
	<div class="dataTable_wrapper">
		<table id="events-table" class="table table-striped datatable-eonweb-ajax table-condensed table-hover">
			<thead>
				<tr>
					<th class="col-md-1">Select</th>
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
					$gedsql_result1=sqlrequest($database_ged,"SELECT pkt_type_id,pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100';");
					
					while($ged_type = mysqli_fetch_assoc($gedsql_result1)){
						// request for ged events according to queue and filters
						$sql = createSelectClause($ged_type["pkt_type_name"], $queue);
						
						
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
									$sql .= " AND o_sec <= ". $actual_time ." AND o_sec > ". $five_minutes;
									break;
								case '5-15m':
									$sql .= " AND o_sec <= ". $five_minutes ." AND o_sec > ". $fifteen_minutes;
									break;
								case '15-30m':
									$sql .= " AND o_sec <= ". $fifteen_minutes ." AND o_sec > ". $thirty_minutes;
									break;
								case '30m-1h':
									$sql .= " AND o_sec <= ". $thirty_minutes ." AND o_sec > ". $one_hour;
									break;
								case 'more':
									$sql .= " AND o_sec <= ". $one_hour;
									break;
							}
						}

						if($ack_time != ""){ 
							$sql .= " AND a_sec - o_sec >= $ack_time";
						}

						$sql .= createWhereClause($owner,$filter,$search,$daterange,$ok,$warning,$critical,$unknown);

						$request = sqlrequest($database_ged, $sql);
						while($event = mysqli_fetch_object($request)){
							$event_state = getEventState($event);
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

	<div class="row">
		<div class="form-group col-md-3">
			<select id="ged-action" class="form-control" name="ged_actions">
				<?php
					if($queue == "active"){
						$actions = $array_action_option;
					} else {
						$actions = $array_resolve_action_option;
					}
					foreach ($actions as $key => $value) {
						echo "<option value=\"$key\">".getLabel("$value")."</option>";
					}
				?>
			</select>
		</div>
		<div class="col-md-3">
			<button id="exec-ged-action" class="btn btn-primary" type="submit" name="action" value="submit"><?php echo getLabel("action.submit"); ?></button>
		</div>
	</div>
</form>

<script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
<script src="/bower_components/datatables-responsive/js/dataTables.responsive.js"></script>
<script type="text/javascript">
	$('.datatable-eonweb-ajax').DataTable({
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
		}
	});
</script>