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
$last = count($itsm_list);
$itsm_acquit = (get_config_var("itsm_acquit") == false ) ? "" : get_config_var("itsm_acquit");
$itsm_create = (get_config_var("itsm_create") == false ) ? "" : get_config_var("itsm_create");
$itsm_thruk = (get_config_var("itsm_thruk") == false ) ? "" : get_config_var("itsm_thruk");

?>


<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_itsm.title"); ?> <span class="badge badge-dark">béta</span></h1>
        </div>
        <h4><?php echo getLabel(	"label.admin_itsm.description"); ?>
</h4>
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
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paramTicket">
                    <span class="glyphicon glyphicon-cog"></span>
                </button>
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
                            <th>Action</th>
                            <th><?php echo getLabel("label.admin_itsm.order"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                               
                                foreach($itsm_list as $itsm){
                                    echo "<tr>
                                            <td class=\"col-sm-3\">".$itsm->getItsm_url()."</td>
                                            <td class=\"col-sm-0\">".$itsm->getItsm_type_request()."</td>
                                            <td class=\"col-sm-6\">";
                                    
                                    foreach($itsm->getItsm_headers() as $header){
                                        echo $header."</br> ";
                                    }
                                    
                                    echo "  </td>
                                            <td class=\"col-sm-2\">
                                                    <a style =\"width: -webkit-fill-available;max-width: 90px; margin-bottom: 5px; \" href='modification_itsm.php?url=".$itsm->getItsm_url()."' class=\"btn btn-success\" role=\"button\">".getLabel("action.edit")."</a>
                                                    <button style =\"width: -webkit-fill-available;max-width: 90px; margin-bottom: 5px; \" class=\"btn btn-danger\" type=\"button\" onclick='delete_itsm(".$itsm->getItsm_id().")'>".getLabel("action.delete")."</button>
                                            </td>
                                            <td class=\"col-sm-1\">";
                                    if($itsm->getItsm_order() == 1){
                                            echo "<button type=\"button\"  onclick='down_itsm(".$itsm->getItsm_id().")' class=\"btn btn-info\"><i class=\"fa fa-arrow-down\"></i></button>";
                                    }else if($itsm->getItsm_order() == $last){
                                        echo "<button type=\"button\"  onclick='up_itsm(".$itsm->getItsm_id().")' class=\"btn btn-info\"><i class=\"fa fa-arrow-up\"></i></button>";
                                    }else {
                                        echo "<button type=\"button\"  onclick='down_itsm(".$itsm->getItsm_id().")' class=\"btn btn-info\"><i class=\"fa fa-arrow-down\"></i></button>
                                        <button type=\"button\"  onclick='up_itsm(".$itsm->getItsm_id().")' class=\"btn btn-info\"><i class=\"fa fa-arrow-up\"></i></button>";
                                    }    
                                    echo " </td>
                                        </tr>";
                                    
                                }
                            ?>
                        
                        </tbody>
                    </table>
                </div>
             

        </div>
        <div class="modal fade" id="paramTicket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="exampleModalLabel"><?php echo getLabel("label.admin_itsm.itsm_setting");?></h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" id="myForm_config_itsm" enctype="multipart/form-data" style="display:grid; ">
                                <label style="margin:5px"><input style="margin-right:10px" id="itsm_create" name="itsm_create" type="checkbox"  value="true" <?php  if($itsm_create=="true"){echo 'checked';} echo '>'.getLabel("label.admin_itsm.create");?> <span id="what_auto_create_itsm" class="glyphicon glyphicon-question-sign"></span></label>
                                
                                <label style="margin:5px"><input style="margin-right:10px" id="itsm_acquit" name="itsm_acquit" type="checkbox" value ="true" <?php  if($itsm_acquit=="true"){echo 'checked';} echo '>'.getLabel("label.admin_itsm.acquit");?> <span id="what_auto_ack" class="glyphicon glyphicon-question-sign"></span></label>
                                
                                <label style="margin:5px"><input style="margin-right:10px" id="itsm_thruk" name="itsm_thruk" type="checkbox" value ="true" <?php  if($itsm_thruk=="true"){echo 'checked';} echo '>'.getLabel("label.ack_in_nagios_default");?> <span id="what_ack_thruk" class="glyphicon glyphicon-question-sign"></span></label>
                          
                            </form>
                            <div id="auto_create_itsm" style="position:absolute" class="col-md-6 alert alert-warning" hidden>
                                <strong><?php echo getLabel("label.admin_itsm.warn"); ?> ! </strong> <?php echo getLabel("label.admin_itsm.warn_text"); ?>
                            </div>
                            <div id="auto_ack" style="position:absolute" class="col-md-6 alert alert-warning" hidden>
                                <strong><?php echo getLabel("label.admin_itsm.warn"); ?> ! </strong> <?php echo getLabel("label.admin_itsm.warn_text"); ?>
                            </div>
                            <div id="ack_thruk" style="position:absolute" class="col-md-6 alert alert-warning" hidden>
                                <?php echo getLabel("label.admin_itsm.ack_thruk"); ?>
                            </div>
                            <div class="modal-footer">
                                <button id="btn_config_itsm" type="submit" class="btn btn-primary" data-dismiss="modal">Ok</button>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

	<br/>
	
	<!-- Result here ! -->
	<div id="result"></div>
	
</div>

<?php include("../../footer.php"); ?>
