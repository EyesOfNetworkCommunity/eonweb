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

include("../../header.php");
include("../../side.php");
include("ged_functions.php");

$queue = "active";
if(isset($_GET["q"]) && $_GET["q"] == "history"){
	$queue = "history";
}

// test is gedd is working
$gedd = true;
if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
	$gedd = false;
}

?>

<div id="page-wrapper">

	<?php
	echo "<pre>";
	var_dump($_GET, $_POST);
	echo "</pre>";
	?>

	<div class="row">
		<div class="col-lg-12">
			<?php
			$title_label = "label.monitoring_ged.title_".$queue;
			?>
			<h1 class="page-header"><?php echo getLabel($title_label); ?></h1>
		</div>
	</div>

	<!-- display messages here -->
	<div id="messages">
		<?php 
		if(!$gedd) {
			message(0," : ged daemon must be dead","critical");
		}
		?>
	</div>

	<?php if($gedd){ ?>
	<!-- filter form -->
	<form id="events-filter">
		<input id="queue" type="hidden" value="<?php echo $queue?>" name="q" />
		
		<div id="form-container" class="row">
			
			<div class="col-md-8">
			
				<div class="row">
					<div class="form-group col-md-6">
						<label>Type</label>
						<select class="form-control focus-to-search" id="type" name="type">
						<?php
						for($i=0;$i<count($array_ged_types);$i++)
							echo "<option value='".$i."'>".$array_ged_types[$i]."</option>";
						?>
						</select>
					</div>
					
					<div class="form-group col-md-6">
						<label>Owner</label>
						<select class="form-control focus-to-search" id="owner" name="owner">
								<option>All</option>
								<option>owned</option>
								<option>not owned</option>
						</select>
					</div>
				</div>
				
				<div class="row">
					<div class="form-group col-md-6">
						<label>Filter</label>
						<select class="form-control focus-to-search" id="filter" name="field">
						<?php
						foreach($array_ged_filters as $key => $value){
							echo "<option>$value</option>";
						}
						?>
						</select>
					</div>
					
					<div class="form-group col-md-6">
						<label>date range</label>
						<input id="daterange" name="datepicker" class="daterangepicker-eonweb form-control" type="text" autocomplete="off" />
					</div>
				</div>
			</div>
			
			<div class="col-md-4">
				<label>State</label>
				<div class="checkbox">
					<?php 
					foreach($array_ged_states as $col => $val){
					echo '
						<div class="checkbox">
							<label>
							<input type="checkbox" class="checkbox focus-to-search" id="'.$col.'" name="'.$col.'" checked />
							'.$col.'
							</label>
						</div>';
					}
					?>
				</div>
			</div>
			
			<div class="col-md-12">
				<div class="row">
					<?php if($queue=="history") { ?>
					<div class="form-group col-md-3">
						<label>Ack time</label>
						<select class="form-control focus-to-search" id="duration" name="duration">
								<option value="" selected>Ack time</option>
								<option value="300">>=5min</option>
								<option value="600">>=10min</option>
								<option value="1200">>=20min</option>
								<option value="3600">>=1h</option>
						</select>
					</div>
					<?php } ?>
					<div class="form-group col-md-4">
						<label>Search</label>
						<div class="input-group">
							<input id="search" name="search" class="form-control" placeholder="Rechercher..." type="text" autocomplete="off" onFocus='$(this).autocomplete({source:<?php echo get_host_list_from_nagios();?>})' />
							<span class="input-group-btn">
								<input type="submit" class="btn btn-primary" value="search" />
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div id="result">
		<?php if($queue == "active"){ ?>
		<form id="ged-table" method="POST" onsubmit="return false;">
			<div class="dataTable_wrapper">
				<table class="table table-striped datatable-eonweb table-condensed table-hover">
					<thead>
						<tr>
							<?php
							foreach ($array_ged_packets as $key => $value) {
								if($value["col"] == true && $key != "state"){
									echo "<th>".ucfirst($key)."</th>";
								}
							}
							?>
							<th class="col-md-1">Select</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$gedsql_result1=sqlrequest($database_ged,"SELECT pkt_type_id,pkt_type_name FROM pkt_type WHERE pkt_type_id!='0' AND pkt_type_id<'100';");
							while($ged_type = mysqli_fetch_assoc($gedsql_result1)){
								// request for ged events according to queue and filters
								$sql = createSelectClause($ged_type["pkt_type_name"], $queue);
								
								$request = sqlrequest($database_ged, $sql);

								while($event = mysqli_fetch_object($request)){
									$event_state = getEventState($event);
									$row_class = getClassRow($event_state);

									echo '<tr class="'.$row_class.'" name="'.$ged_type["pkt_type_name"].'">';
									createTableRow($event, $event_state);
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
								echo "<option value='$value'>$value</option>";
							}
						?>
					</select>
				</div>
				<button id="exec-ged-action" class="btn btn-primary" type="submit" name="action" value="submit"><?php echo getLabel("action.submit"); ?></button>
			</div>
		</form>
		<?php } ?>
	</div>
	<?php } ?>

	<div id="loader" style="visibility: hidden;">
		<img src="/images/loader.gif" alt="loading">
	</div>

	<!-- modal for GED actions -->
	<div id="ged-modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Modal title</h4>
				</div>
				<div class="modal-body">
					<div id="event-message">huhuihui</div>
					<div id="content"></div>
				</div>
				<div class="modal-footer">
					<button id="details-prev" type="button" class="btn btn-primary">
						<i class="fa fa-arrow-circle-left"> </i> <?php echo getLabel("label.prev"); ?>
					</button>
					<button id="details-next" type="button" class="btn btn-primary">
						<?php echo getLabel("label.next"); ?> <i class="fa fa-arrow-circle-right"> </i>
					</button>
					<button id="edit-event" type="button" class="btn btn-primary">
						<?php echo getLabel("action.edit"); ?>
					</button>
					<button id="edit-all-event" type="button" class="btn btn-primary">
						<?php echo getLabel("action.edit_all"); ?>
					</button>
					<button id="event-validation" type="button" class="btn btn-primary">
						<?php echo getLabel("action.apply"); ?>
					</button>
					<button id="action-cancel" type="button" class="btn btn-default" data-dismiss="modal">
						<?php echo getLabel("action.cancel"); ?>
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>

<?php include("../../footer.php"); ?>
