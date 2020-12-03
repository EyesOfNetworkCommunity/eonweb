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

include("../../header.php");
include("../../side.php");
include("ged_functions.php");

$nagios_default = sql("eonweb", "SELECT value FROM configs WHERE name=\"itsm_thruk\"");
$nagios_default = $nagios_default[0];
$itsm = sql("eonweb", "SELECT value FROM configs WHERE name=\"itsm\"");
$itms = $itsm[0];

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

// get all GED filters
$file="../../cache/".$_COOKIE["user_name"]."-ged.xml";

$filters_list = [];
$default = "";
if(file_exists($file)){
	$xmlfilters = new DOMDocument("1.0","UTF-8");
	$xmlfilters->load($file);

	$xpath = new DOMXPath($xmlfilters);
	$filter_names = $xpath->query("//ged/filters");
	
	foreach ($filter_names as $filter_name) {
		array_push($filters_list, $filter_name->getAttribute("name"));
	}

	$g=$xmlfilters->getElementsByTagName("ged")->item(0);

	//Default filter detection
	$default=$g->getElementsByTagName("default")->item(0)->nodeValue;
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
		message("", getLabel("label.search_limit")." ".getEonConfig("maxlines")." ".getLabel("label.entries"), "");
		?>
	</div>

	<?php if($gedd){ ?>
	<!-- filter form -->
	<div class="panel panel-default">
		<div class="panel-heading" id="headingOne">
			<h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"><i class="fa fa-filter fa-fw"></i></a>
				<?php echo getLabel("label.ged_sorter"); ?>
				<?php
				if($default == ""){
					if($_COOKIE["user_limitation"] == 0){
						$link = '<a id="filter-link" href="../module_filters/index.php">'.getLabel("label.none").'</a>';
					} else {
						$link = getLabel("label.none");
					}
				} else {
					if($_COOKIE["user_limitation"] == 0){
						$link = '<a id="filter-link" href="../module_filters/index.php?filter='.$default.'">'.$default.'</a>';
					} else {
						$link = $default;
					}
					
				}
				?>
				<span id="filter-info">(<?php echo getLabel("label.using_filter"); ?> : <?php echo $link ?>)</span>
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
										<option value=""><?php echo getLabel("label.all"); ?></option>
										<option value="owned" <?php if(isset($_GET["own"]) && $_GET["own"] == "yes"){ echo "selected='selected'";} ?>><?php echo getLabel("label.owned"); ?></option>
										<option value="not owned" <?php if(isset($_GET["own"]) && $_GET["own"] == "no"){ echo "selected='selected'";} ?>><?php echo getLabel("label.not_owned"); ?></option>
									</select>
								</div>
							</div>
							
							<div class="row">
								<div class="form-group col-md-6">
									<label><?php echo getLabel("label.filter") ?></label>
									<select class="form-control focus-to-search" id="filter" name="field">
									<?php
									foreach($array_ged_filters as $key => $value){
										echo "<option value='$key'>$value</option>";
									}
									?>
									</select>
								</div>
								
								<div class="form-group col-md-6">
									<label><?php echo getLabel("label.date_range") ?></label>
									<input id="daterange" name="datepicker" class="daterangepicker-eonweb form-control" type="text" autocomplete="off" />
								</div>
							</div>
						</div>
						
						<div class="col-md-4">
							<div class="form-group">
								<label><?php echo getLabel("label.state") ?></label>
								<select id="filter-state" class="selectpicker form-control" multiple title="<?php echo getLabel("label.choose_state") ?>">
									<?php 
									foreach($array_ged_states as $col => $val){
										if(isset($_GET["status"])) { $selected = ""; }
										else { $selected = "selected"; }
										if(count($status_parts) > 0 && in_array($val, $status_parts)){ $selected = "selected"; }
										echo '<option '.$selected.'>'.$col.'</option>';
									}
									?>
								</select>
							</div>
							<?php if($_COOKIE["user_limitation"] == 0){ ?>
							<div class="form-group">
								<label><?php echo getLabel("label.ged_filter") ?></label>
								<select id="filter-selection" class="selectpicker form-control">
									<option value=""><?php echo getLabel("label.no_filter") ?></option>
									<?php
									foreach($filters_list as $key => $val){
										$selected = "";
										if($val == $default){ $selected = "selected"; }
										echo '<option '.$selected.' value="'.$val.'">'.$val.'</option>';
									}
									?>
								</select>
							</div>
							<?php } ?>
						</div>
						
						<div class="col-md-12">
							<div class="row">
								<?php if($queue=="history") { ?>
								<div class="form-group col-md-4">
									<label><?php echo getLabel("label.ack_time") ?></label>
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
									<label><?php echo getLabel("action.search") ?></label>
									<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo getLabel("message.advanced_search_info"); ?>" style="cursor: pointer;"></i>
									<div class="input-group">
										<input id="ged-search" name="search" class="form-control" placeholder="*<?php echo getLabel("action.search"); ?>*" type="text" autocomplete="off" />
										<span class="input-group-btn">
											<input type="submit" class="btn btn-primary" value="<?php echo getLabel("action.search"); ?>" />
										</span>
									</div>
								</div>
								<?php if ($_GET["q"] != "history") { ?>
								<div class="col-md-4">
									<label><?php echo getLabel("label.refresh_button")?></label>
									<div>
									<input id="refresh_on" type="button" class="btn btn-primary" value="on" />
									<input id="refresh_off" type="button" class="btn btn-danger hidden" value="off" />
									</div>
								</div> 
								<?php }?>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="result"></div>
	<?php } ?>
	
	<div id="loader" style="visibility: hidden;">
		<img src="/images/loader.gif" alt="loading">
	</div>

	<!-- modal for GED actions -->
	<div id="ged-modal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content panel-default">
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
						<button id="edit-event-choix" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.edit"); ?> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li id="edit-event"><a href="#"><?php echo getLabel("label.this"); ?></a></li>
							<li id="edit-all-event"><a href="#"><?php echo ucfirst(getLabel("label.all")); ?></a></li>
						</ul>

						<span id="edit-event-simple">
						<button id="edit-event" class="btn btn-primary" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.edit"); ?> 
						</button>
						</span>
					</div>
					<div id="own-btns" class="btn-group">
						<button id="own-event-choix" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.own"); ?> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li id="own-event"><a href="#"><?php echo getLabel("label.this"); ?></a></li>
							<li id="own-all-event"><a href="#"><?php echo ucfirst(getLabel("label.all")); ?></a></li>
						</ul>
						<span id="own-event-simple">
						<button id="own-event" class="btn btn-primary" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.own");?>
						</button>
						</span>

					</div>

					<div id="ack-btns" class="btn-group">
						
						<button id ="ack-event-choix" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.ack"); ?> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li id="ack-event"><a href="#"><?php echo getLabel("label.this"); ?></a></li>
							<li id="ack-all-event"><a href="#"><?php echo ucfirst(getLabel("label.all")); ?></a></li>
						</ul>
						
						<span id="ack-event-simple">
						<button id = "ack-event" class="btn btn-primary"aria-haspopup="true" aria-expanded="false">
							<?php echo getLabel("action.ack"); ?> 
						</button>
						</span>
					</div>
					<?php } ?>
					
					
					<?php if($itsm == "on") {if($nagios_default == "true") { ?>
					<div id="check-nagios" class="form-check" style="display:inline; margin-right: 4rem">
						<label class="form-check-label" for="checkbox-nagios"><?php echo getLabel("label.ack_in_nagios");?>:</label>
						<input type="checkbox" class="form-check-input" id="checkbox-nagios" checked>
					</div>

					<?php 
					}} else { ?>
						<div id="check-nagios" class="form-check" style="display:inline; margin-right: 4rem">
						<label class="form-check-label" for="checkbox-nagios"><?php echo getLabel("label.ack_in_nagios");?>:</label>
						<input type="checkbox" class="form-check-input" id="checkbox-nagios" checked>
					</div>
					<?php }
						edit_button();
					 ?>

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
			<div id="confirmation-modal-content" class="modal-content panel-default">
				<div id="confirmation-modal-header" class="modal-header panel-heading">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 id="confirmation-modal-title" class="modal-title">Title</h4>
				</div>
				<div id="confirmation-modal-body" class="modal-body">
					<?php echo getLabel("message.confirmation"); ?>
				</div>
				<div id="confirmation-modal-footer" class="modal-footer">
				<?php if($itsm = "on") {if($nagios_default == "true") { ?>

					<div id="check-nagios-val" class="form-check"style="display:inline; margin-right: 4rem;">
						<label class="form-check-label" for="checkbox-nagios-val"><?php echo getLabel("label.ack_in_nagios");?>:</label>
						<input type="checkbox" class="form-check-input" id="checkbox-nagios-val" checked>
					</div>
				<?php }} else { ?>
					<div id="check-nagios-val" class="form-check"style="display:inline; margin-right: 4rem;">
						<label class="form-check-label" for="checkbox-nagios-val"><?php echo getLabel("label.ack_in_nagios");?>:</label>
						<input type="checkbox" class="form-check-input" id="checkbox-nagios-val" checked>
					</div>
				<?php } ?>
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

	<style>
						.modal-loader{
							background-color: rgba(0, 0, 0, .5);
							z-index:3500;
							position: absolute;
								top: 0;
								right: 0;
								bottom: 0;
								left: 0;
						}
						.div-loader {
							position: fixed;
								top: 50%;
								left: 50%;
							transform: translate(-50%, -50%);
							border: 14px solid #f3f3f3;
							border-radius: 50%;
							border-top: 14px solid #3498db;
							width: 80px;
							height: 80px;
							-webkit-animation: spin 2s linear infinite; /* Safari */
							animation: spin 2s linear infinite;
						}

						/* Safari */
						@-webkit-keyframes spin {
							0% { -webkit-transform: rotate(0deg); }
							100% { -webkit-transform: rotate(360deg); }
						}

						@keyframes spin {
						0% { transform: rotate(0deg); }
						100% { transform: rotate(360deg); }
						}
					</style>
					<div id="modal-loader" class="modal-loader" style="visibility: hidden;"><div class="div-loader"></div></div>
</div>

<?php include("../../footer.php"); ?>
