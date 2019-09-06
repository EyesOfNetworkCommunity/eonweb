<?php
/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
# DEV NAME : Jeremy HOARAU
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
// ini_set('display_errors','on');
// error_reporting(E_ALL);

include("../../header.php");
include("../../side.php");
include("function_itsm.php");
include("classes/Itsm.php");
include("classes/ItsmPeer.php");

$state = get_itsm_state();
$itsmPeer = new ItsmPeer();
$itsm_list = $itsmPeer->get_all_itsm();
?>


<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_itsm.title"); ?> <span class="badge badge-dark">béta</span></h1>
		</div>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">
                <?php echo getLabel("label.admin_itsm.itsm_setting"); ?>
            </h4>
            <div class="btn-group pull-right">
                    <div class="btn-group" id="result_state_itsm">
                        <?php echo $state; ?>
                        <a href='modification_itsm.php' class="btn btn-info" role="button"><?php echo getLabel("action.add"); ?></a>
                    </div>
            </div>
        </div>
        <div class="panel-body">
                <div class="table-responsive">          
                    <table class="table">
                        <thead>
                        <!-- <tr> colspan="2" -->
                            <th><?php echo getLabel("label.admin_itsm.url"); ?></th>
                            <th>Type</th>
                            <th><?php echo getLabel("label.admin_itsm.header"); ?></th>
                            <th><?php echo getLabel("label.admin_itsm.file"); ?></th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                               
                                foreach($itsm_list as $itsm){
                                    echo "<tr>
                                            <td >".$itsm->getItsm_url()."</td>
                                            <td >".$itsm->getItsm_type_request()."</td>
                                            <td >";
                                    foreach($itsm->getItsm_headers() as $header){
                                        echo $header."</br> ";
                                    }
                                    
                                    echo "  </td>
                                            <td >".$itsm->getItsm_file()."</td>
                                            <td class=\"col-md-6\">
                                                <div class=\"btn-group\">
                                               
                                                    <a href='modification_itsm.php?url=".$itsm->getItsm_url()."' class=\"btn btn-success\" role=\"button\">".getLabel("action.edit")."</a>
                                                    <button class=\"btn btn-danger\" type=\"button\" onclick='delete_itsm(".$itsm->getItsm_id().")'>".getLabel("action.delete")."</button>
                                                    
                                                </div>
                                            </td>
                                        </tr>";
                                    
                                }
                            ?>
                        
                        </tbody>
                    </table>
                </div>
             

        </div>
    </div>

	<br/>
	
	<!-- Result here ! -->
	<div id="result"></div>
	
</div>

<?php include("../../footer.php"); ?>
