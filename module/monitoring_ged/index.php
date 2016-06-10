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
include("external_functions.php");

$queue = "active";
if(isset($_GET["q"]) && $_GET["q"] == "history"){
	$queue = "history";
}

// test is gedd is working
$gedd = true;
if(exec($array_serv_system["Ged agent"]["status"])==NULL) {
	$gedd = false;
}

$list_status = "";
$status_parts = [];
if(isset($_GET["status"])){
	$status_parts = explode("-", $_GET["status"]);
	foreach($status_parts as $status){
		$list_status .= $status.",";
	}
	$list_status = trim($list_status, ",");
}

?>

<div id="page-wrapper">

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
	<div class="panel panel-default">
		<div class="panel-heading" id="headingOne">
			<h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					<?php echo getLabel("label.ged_sorter"); ?>
				</a>
			</h4>
		</div>
		<div id="collapseOne" class="panel-collapse collapse out" role="tabpanel" aria-labelledby="headingOne">
			<div class="panel-body">
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
										echo "<option value='".$i."'>".getLabel($array_ged_types[$i])."</option>";
									?>
									</select>
								</div>

								<div class="form-group col-md-6">
									<label><?php echo getLabel("label.owner") ?></label>
									<select class="form-control focus-to-search" id="owner" name="owner">
											<option><?php echo getLabel("label.all"); ?></option>
											<option <?php if(isset($_GET["own"]) && $_GET["own"] == "yes"){ echo "selected='selected'";} ?>><?php echo getLabel("label.owned"); ?></option>
											<option <?php if(isset($_GET["own"]) && $_GET["own"] == "no"){ echo "selected='selected'";} ?>><?php echo getLabel("label.not_owned"); ?></option>
									</select>
								</div>
							</div>
							
							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo getLabel("label.filter") ?></label></label>
									<select class="form-control focus-to-search" id="filter" name="field">
									<?php
									foreach($array_ged_filters as $key => $value){
										echo "<option value='$key'>$value</option>";
									}
									?>
									</select>
								</div>
								
								<div class="form-group col-md-6">
									<label><?php echo getLabel("label.date_range") ?></label></label>
									<input id="daterange" name="datepicker" class="daterangepicker-eonweb form-control" type="text" autocomplete="off" />
								</div>
							</div>
						</div>
						
						<div class="col-md-4">
							<label><?php echo getLabel("label.state") ?></label></label>
							<div class="checkbox">
								<?php 
								foreach($array_ged_states as $col => $val){
								$checked = "";
								if(count($status_parts) > 0 && in_array($val, $status_parts)){ $checked = "checked"; }
								echo '
									<div class="checkbox">
										<label>
										<input type="checkbox" class="checkbox focus-to-search" id="'.$col.'" name="'.$col.'" '.$checked.' />
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
								<div class="form-group col-md-4">
									<label><?php echo getLabel("label.ack_time") ?></label></label>
									<select class="form-control focus-to-search" id="duration" name="duration">
											<option value=""><?php echo getLabel("label.ack_time") ?></option>
											<option value="300">>=5min</option>
											<option value="600">>=10min</option>
											<option value="1200">>=20min</option>
											<option value="3600">>=1h</option>
									</select>
								</div>
								<?php } else { ?>
								<div class="form-group col-md-4">
									<label><?php echo getLabel("label.o_time") ?></label>
									<select class="form-control focus-to-search" id="time" name="duration">
											<?php
											$time = false;
											if(isset($_GET["time"])){
												$time = $_GET["time"];
											}
											?>
											<option value=""><?php echo getLabel("label.all") ?></option>
											<option <?php if($time && $time == "0-5m"){echo "selected";} ?> value="0-5m">0 - 5min</option>
											<option <?php if($time && $time == "5-15m"){echo "selected";} ?> value="5-15m">5 - 15min</option>
											<option <?php if($time && $time == "15-30m"){echo "selected";} ?> value="15-30m">15 - 30min</option>
											<option <?php if($time && $time == "30m-1h"){echo "selected";} ?> value="30m-1h">30min - 1h</option>
											<option <?php if($time && $time == "more"){echo "selected";} ?> value="more"><?php echo getLabel("label.more"); ?></option>
									</select>
								</div>
								<?php } ?>
								<div class="form-group col-md-4">
									<label><?php echo getLabel("action.search") ?></label></label>
									<div class="input-group">
										<input id="search" name="search" class="form-control" placeholder="<?php echo getLabel("label.input.placeholder.search"); ?>" type="text" autocomplete="off" onFocus='$(this).autocomplete({source:<?php echo get_host_list_from_nagios();?>})' />
										<span class="input-group-btn">
											<input type="submit" class="btn btn-primary" value="<?php echo getLabel("action.search"); ?>" />
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="result">
		<?php if($queue == "active"){ ?>
		<form id="ged-table" method="POST" onsubmit="return false;">
			<div class="dataTable_wrapper">
				<table id="events-table" class="table table-striped datatable-eonweb table-condensed table-hover">
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
								
								if($list_status != ""){
									$sql .= " AND state IN ($list_status)";
								}

								if(isset($_GET["own"])){
									if($_GET["own"] == "yes"){
										$sql .= " AND owner != ''";
									} else {
										$sql .= " AND owner = ''";
									}
								}

								if(isset($_GET["time"])){
									// define all times needed (for each range)
									$actual_time = time();
									$five_minutes = $actual_time - (60 * 5);
									$fifteen_minutes = $actual_time - (60 * 15);
									$thirty_minutes = $actual_time - (60 * 30);
									$one_hour = $actual_time - (60 * 60);

									switch ($_GET["time"]) {
										case '0-5m':
											$sql .= "AND o_sec <= ". $actual_time ." AND o_sec > ". $five_minutes;
											break;
										case '5-15m':
											$sql .= "AND o_sec <= ". $five_minutes ." AND o_sec > ". $fifteen_minutes;
											break;
										case '15-30m':
											$sql .= "AND o_sec <= ". $fifteen_minutes ." AND o_sec > ". $thirty_minutes;
											break;
										case '30m-1h':
											$sql .= "AND o_sec <= ". $thirty_minutes ." AND o_sec > ". $one_hour;
											break;
										case 'more':
											$sql .= "AND o_sec <= ". $one_hour;
											break;
									}
								}
								
								
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
								echo "<option value='$key'>".getLabel("$value")."</option>";
							}
						?>
					</select>
				</div>
				<div class="col-md-3">
					<button id="exec-ged-action" class="btn btn-primary" type="submit" name="action" value="submit"><?php echo getLabel("action.submit"); ?></button>
				</div>
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
					<div id="event-message"></div>
					<div id="content"></div>
				</div>
				<div class="modal-footer">
					<div class="btn-group" id="modal-nav">
						<button id="details-prev" type="button" class="btn btn-primary">
							<i class="fa fa-arrow-circle-left"> </i> <?php //echo getLabel("label.prev"); ?>
						</button>
						<button id="details-next" type="button" class="btn btn-primary">
							<?php //echo getLabel("label.next"); ?> <i class="fa fa-arrow-circle-right"> </i>
						</button>
					</div>

					<?php if($queue == "active"){ ?>
					<div id="edit-btns" class="btn-group">
						<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.edit"); ?> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li id="edit-event"><a href="#"><?php echo getLabel("label.this"); ?></a></li>
							<li id="edit-all-event"><a href="#"><?php echo ucfirst(getLabel("label.all")); ?></a></li>
						</ul>
					</div>
					<div id="ack-btns" class="btn-group">
						<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.ack"); ?> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li id="ack-event"><a href="#"><?php echo getLabel("label.this"); ?></a></li>
							<li id="ack-all-event"><a href="#"><?php echo ucfirst(getLabel("label.all")); ?></a></li>
						</ul>
					</div>
					<?php } ?>

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

	<?php if($queue == "active"){ ?>
	<!-- modal for confirmation -->
	<div id="confirmation-modal" class="modal fade" tabindex="-1" role="dialog">
		<div id="confirmation-modal-dialog" class="modal-dialog">
			<div id="confirmation-modal-content" class="modal-content">
				<div id="confirmation-modal-header" class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="confirmation-modal-title">Title</h4>
				</div>
				<div id="confirmation-modal-body" class="modal-body">
					<?php echo getLabel("message.confirmation"); ?>
				</div>
				<div id="confirmation-modal-footer" class="modal-footer">
					<button id="confirmation-event-validation" type="button" class="btn btn-primary">
						<?php echo getLabel("action.apply"); ?>
					</button>
					<button id="confirmation-action-cancel" type="button" class="btn btn-default" data-dismiss="modal">
						<?php echo getLabel("action.cancel"); ?>
					</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<?php } ?>
</div>

<?php include("../../footer.php"); ?>
