<?php
/*
#########################################
#
# Copyright (C) 2021 EyesOfNetwork Team
# DEV NAME : Julien Gonzalez
# VERSION : 6.0
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
include("classes/ReportService.php");

$periods = array("lastDay", "thisDay", "lastWeek", "thisWeek", "lastMonth", "last6Month", "thisMonth", "lastYear", "thisYear");
$crons = array("never", "everyDay", "everyWeek", "everyMonth", "every6Month", "everyYear", "custom");
$services = HostService::getServices();
$servicesName = array();
foreach($services as $service) {
    array_push($servicesName, $service["description"]);
}
$servicesName = array_unique($servicesName);
$hosts = HostService::getHosts();
$emails = array();
$error = false;

if(!empty($_POST["report_name"])) {
    $report = new Report;
    foreach($_POST["hosts"] as $host) {
        if(!in_array($host, array_column($hosts, 'name'))) {
            $error = true;
        }
    }

    foreach($_POST["services"] as $service) {
        if(!in_array($service, $servicesName)) {
            $error = true;
        }
    }
    
    if(!in_array($_POST["period"], $periods)) {
        $error = true;
    }

    if(empty($_POST["report_cron_custom"])) {
        if(!in_array($_POST["cron"], $crons)) {
            $error = true;
        }
    }
    if($error == false) {
        if(!empty($_POST["id"])) {
            $report->setId($_POST["id"]);
        }
        $report->setName($_POST["report_name"]);
        $report->setHosts($_POST["hosts"]);
        $report->setServices($_POST["services"]);
        $report->setPeriod($_POST["period"]);
        if(empty($_POST["report_cron_custom"])) {
            $report->setCron($_POST["cron"]);
        } else {
            if(!preg_match('/([^0-9-\* ])/', $_POST["report_cron_custom"], $matches)) {
                $report->setCron($_POST["report_cron_custom"]);
            }
        }
        $report->setEmails(explode(",", $_POST["report_mail"]));
        ReportService::save($report);
        var_dump( ReportService::generateCron());
    }
}

$report = new Report;
if(!empty($_GET["id"])) {
    $report = ReportService::getById($_GET["id"]);
    $emails = $report->getEmails();
}

?>
<script src="../../bower_components/jquery/dist/jquery.min.js"></script>
<script src="select2.min.js" defer></script>
<script src="scripts.js"></script>
<link rel="stylesheet" href="css/select2.min.css">

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.report.title"); ?></h1>
		</div>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="btn-group pull-right">
                <div class="btn-group" id="result_state_itsm">
                    <a href="index.php" class="btn btn-info" id="btn_return" role="button">
                        <i class="fa fa-reply"></i>
					</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form  class="form-horizontal" id="myForm" enctype="multipart/form-data" method="post">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="report_name"><?php echo getLabel("label.report.name"); ?></label>
                    <div class="col-sm-6"> 
                        <input type="text" class="form-control" id="report_name" placeholder="report 1..." name="report_name" value="<?php echo ($report !=null) ? $report->getName() : ""; ?>" required>
                    </div>
                </div>

                <div id="dynamic_fields_var">
                    <div class="form-group">
                        <label class="control-label col-sm-2"><?php echo getLabel("label.admin_notifier.hosts"); ?></label>
                        <div class="col-sm-6 input"> 
                            <select class="js-example-basic-multiple" name="hosts[]" multiple="multiple" style="width:100%">
                            <?php 
                                foreach($hosts as $host) {
                                    if($report != null && in_array($host["name"], $report->getHosts())) {
                                        print("<option value=" . $host["name"] . " selected>" . $host["name"] . "</option>");
                                    } else {
                                        print("<option value=" . $host["name"] . ">" . $host["name"] . "</option>");
                                    }
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="dynamic_fields_var2">
                    <div class="form-group">
                        <label class="control-label col-sm-2"><?php echo getLabel("label.service"); ?></label>
                        <div class="col-sm-6 select"> 
                            <select class="js-example-basic-multiple" name="services[]" multiple="multiple"  style="width:100%">
                            <?php 
                                foreach($servicesName as $service) {
                                    if($report != null && in_array($service, $report->getServices())) {
                                        print("<option value=" . $service . " selected>" . $service . "</option>");
                                    } else {
                                        print("<option value=" . $service . ">" . $service . "</option>");
                                    }
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="dynamic_fields_var3">
                    <div class="form-group">
                        <label class="control-label col-sm-2"><?php echo getLabel("label.report_performance.period"); ?></label>
                        <div class="col-sm-6 select"> 
                            <select class="form-control select_champ" name="period">
                            <?php 
                                foreach($periods as $period) {
                                    if($report != null && $period == $report->getPeriod()) {
                                        print("<option value=" . $period . " selected>" . $period . "</option>");
                                    } else {
                                        print("<option value=" . $period . ">" . $period . "</option>");
                                    }
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="dynamic_fields_var4">
                    <div class="form-group">
                        <label class="control-label col-sm-2"><?php echo getLabel("label.report.cron"); ?></label>
                        <div class="col-sm-6 select"> 
                            <select id="dropdownCron" onChange="addCustom(custom)" class="form-control select_champ" name="cron">
                            <?php
                                $custom = true;
                                $last = array_key_last($crons);
                                foreach($crons as $k => $cron) {
                                    if($report != null && $cron == $report->getCron()) {
                                        print("<option value=" . $cron . " selected>" . $cron . "</option>");
                                        $custom = false;
                                        
                                    } else {
                                        if($last == $k && !in_array($report->getCron(), $crons)) {
                                            print("<option value='" . $report->getCron() . "' selected>custom</option>");
                                        } elseif($last == $k && in_array($report->getCron(), $crons)) {
                                            print("<option value=''>custom</option>");
                                        } else {
                                            print("<option value=" . $cron . ">" . $cron . "</option>");
                                        }
                                    }
                                    
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php 
                    if($custom == true)
                        print('<div id="custom-cron-input" class="form-group"><label class="control-label col-sm-2" for="report_cron_custom"></label><div class="col-sm-6"><input type="text" class="form-control" id="report_cron_custom" name="report_cron_custom" placeholder="* * * * *" value="' . $report->getCron() . '" required></div></div>');
                    ?>
                </div>
                <div id="dynamic_fields_var5">
                    <div class="form-group">
                        <label class="control-label col-sm-2"><?php echo getLabel("label.report.email"); ?></label>
                        <div class="col-sm-6">
                            <input type="email" placeholder="example1@example.com, example2@example.com,..." multiple class="form-control" id="report_mail" name="report_mail" value="
                            <?php 
                                $lastKey = array_key_last($emails);
                                foreach($emails as $k => $email) {
                                    if($k == $lastKey)
                                        print($email);
                                    else
                                        print($email . ",");
                                }
                            ?>                            
                            ">
                        </div>
                    </div>
                </div>
                <input class="form-control" name="id" value="<?php echo ($report != null) ? $report->getId() : ""; ?>" type="hidden">

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-4">
                        <button class="btn btn-primary" id="btn_form" type="submit">
                            <?php echo getLabel("action.apply"); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
	<br/>
	<!-- Result here ! -->
	<div id="result"></div>
</div>
<?php include("../../footer.php"); ?>
