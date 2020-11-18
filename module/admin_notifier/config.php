<?php
/*
#########################################
#
# Copyright (C) 2018 EyesOfNetwork Team
# DEV NAME : Eric Belhomme
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
			<h1 class="page-header"><?php echo getLabel("label.admin_notifier.config.title"); ?></h1>
		</div>
	</div>

	<?php
    // UPDATE
    //global $database_notifier;
	if (isset($_POST["update"])) {
		// Get post data
        $debug=retrieve_form_data("debug",null);
        if ($debug != '1') {$debug = '0';}
	    $debug_rules=retrieve_form_data("debug_rules",null);
	    $log_file=retrieve_form_data("log_file",null); 
        $notif_file=retrieve_form_data("notif_file",null);
       
        $sql_update = "UPDATE configs SET value=? WHERE name='debug'";
        sql($database_notifier,$sql_update, array($debug));
        $sql_update = "UPDATE configs SET value=? WHERE name='debug_rules'";
		sql($database_notifier,$sql_update,array($debug_rules));
        $sql_update = "UPDATE configs SET value=? WHERE name='log_file'";
		sql($database_notifier,$sql_update, array($log_file));
        $sql_update = "UPDATE configs SET value=? WHERE name='notifsent_file'";
		sql($database_notifier,$sql_update, array($notif_file));
        message(6," : Configs have been updated",'ok');
    }
    else {
        $sqlret = sql($database_notifier,"SELECT value FROM configs WHERE name='debug'");
		if($sqlret[0]["value"]) {
            $debug = $sqlret[0]["value"];
        }
        $sqlret = sql($database_notifier,"SELECT value FROM configs WHERE name='debug_rules'");
		if($sqlret[0]["value"]) {
            $debug_rules = $sqlret[0]["value"];
        }
        $sqlret = sql($database_notifier,"SELECT value FROM configs WHERE name='log_file'");
		if($sqlret[0]["value"]) {
            $log_file = $sqlret[0]["value"];
        }
        $sqlret = sql($database_notifier,"SELECT value FROM configs WHERE name='notifsent_file'");
		if($sqlret[0]["value"]) {
            $notif_file = $sqlret[0]["value"];
        }
    }
    ?>

    <form id="form-config" action="./config.php" method="POST" name="form">
    
        <div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.config.debug") ?></label>
			<div class="col-md-9">
				<input class="checkbox" type="checkbox" name="debug" value="1" 
				<?php 
				if (isset($debug)) {
					if ($debug > 0) {
						echo " checked";
					}
				}
				?>
				>
			</div>
		</div>

        <div class="row form-group">
            <label class="col-md-3"><?php echo getLabel("label.admin_notifier.config.debug_rules") ?></label>
            <div class="col-md-9">
                <select class="form-control" name="debug_rules">
                <?php
                for($i=0;$i<=3;$i++) {
                    if($debug_rules==$i) {
                        echo "<option selected>$i</option>";
                    } else {
                        echo "<option>$i</option>";
                    }
                }
                ?>
                </select>
            </div>
        </div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.config.log_file") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" name="log_file" value="<?php echo $log_file; ?>" autofocus>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-3"><?php echo getLabel("label.admin_notifier.config.notif_file") ?></label>
			<div class="col-md-9">
				<input class="form-control" type="text" name="notif_file" value="<?php echo $notif_file; ?>" autofocus>
			</div>
		</div>

        <div class="form-group">
    		<input class='btn btn-primary' type='submit' name='update' value="<?php echo getLabel('action.update'); ?>">
            <input class="btn btn-default" type="button" name="back" value="<?php echo getLabel("action.cancel"); ?>" onclick="location.href='index.php'">
    	</div>
    </form>
</div>
<?php include("../../footer.php"); ?>
