<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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

?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_logs.title"); ?></h1>
		</div>
	</div>

	<form id="logs-form" method="post" onsubmit="return submitFormAjax()">
		<div class="row">
			<div class="form-group col-md-4">
				<label><?php echo getLabel('label.period'); ?></label>
				<input type="text" class="daterangepicker-eonweb form-control" name="date">
			</div>
			
			<div class="form-group col-md-4">
				<label><?php echo getLabel("label.user"); ?></label>
				<input type="text" id="user" name="user" class="form-control">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label>Module</label>
				<input type="text" id="module" name="module" class="form-control">
			</div>
			
			<div class="form-group col-md-4">
				<label>Description</label>
				<input type="text" id="description" name="description" class="form-control">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-4">
				<label>Source</label>
				<div class="input-group">
					<input type="text" id="source" name="source" class="form-control">
					<span class="input-group-btn">
						<button class="btn btn-primary"><?php echo getLabel("action.search"); ?></button>
					</span>
				</div>
			</div>
		</div>
	</form>
	<br>
	<!-- Loading message -->
	<div id="loading">
		<h2>Loading, please wait ...</h2><br>
	</div>
	
	<!-- Result here ! -->
	<div id="result"></div>
	
</div>

<?php include("../../footer.php"); ?>
