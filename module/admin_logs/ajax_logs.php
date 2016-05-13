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
include("../../include/function.php");

// create WHERE clause according to parameters
extract($_POST);

$where_clause = "";
if($user != ""){ $where_clause .= " AND user LIKE '%$user%'"; }
if($module != ""){ $where_clause .= " AND module LIKE '%$module%'"; }
if($description != ""){ $where_clause .= " AND description LIKE '%$description%'"; }
if($source != ""){ $where_clause .= " AND source LIKE '%$source%'"; }

// period clause
if($date != ""){
	$times = explode(" - ", $date);
	$start = strtotime($times[0]);
	$end = strtotime($times[1]) + 86400;
	$where_clause .= " AND date >= $start AND date < $end";
}


$sql = "SELECT * FROM logs WHERE id!=0".$where_clause." ORDER BY id DESC";
$results = sqlrequest($database_eonweb,$sql);

?>

<div class="dataTable_wrapper">
	<table class="table table-striped datatable-eonweb table-condensed">
		<thead>
			<tr>
				<th class="col-md-4">Date</th>
				<th class="col-md-2"><?php echo getLabel("label.user"); ?></th>
				<th class="col-md-2">Module</th>
				<th>Description</th>
				<th>Source</th>
			</tr>
		</thead>
		<tbody>
			<?php while( $log = mysqli_fetch_assoc($results) ) { ?>
				<tr>
					<td><?php echo date($dateformat, $log["date"]); ?></td>
					<td><?php echo $log["user"]; ?></td>
					<td><?php echo $log["module"]; ?></td>
					<td><?php echo $log["description"]; ?></td>
					<td><?php echo $log["source"]; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

<?php include("admin_logs.php"); ?>