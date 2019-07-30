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

include("../../header.php");
include("../../side.php");
include("./function_itsm.php");

?>


<?php
    $itsm_file      = basename(get_itsm_var("itsm_file"));
    $itsm_header    = get_itsm_var("itsm_header");    
    $itsm_url       = get_itsm_var("itsm_url");    
    $itsm_acquit    = get_itsm_var("itsm_acquit");
    $itsm_create    = get_itsm_var("itsm_create");

?>


<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.admin_itsm.title"); ?></h1>
		</div>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">
                <?php echo getLabel("label.admin_itsm.itsm_setting"); ?>
            </h4>
            <div class="btn-group pull-right">
                    <button class="btn btn-warning" id="btn_unlock" >
						<?php echo getLabel("action.update"); ?>
					</button>
            </div>
        </div>
        <div class="panel-body">
            <form  class="form-horizontal" id="myForm" enctype="multipart/form-data">
                <div class="form-group">
                    
                    <label class="control-label col-sm-2" ><?php echo getLabel("label.admin_itsm.file"); ?> :</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <label class="input-group-btn">
                                <span class="btn btn-primary">
                                    <?php echo getLabel("action.import"); ?>&hellip; <input id="input_file" name="fileName" type="file" style="display: none;" >
                                </span>
                            </label>
                            <input type="text" id="file_label" class="form-control" placeholder="File type : xml , json" <?php echo 'value="'.$itsm_file.'"';?> readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="itsm_header"><?php echo getLabel("label.admin_itsm.header"); ?>:</label>
                    <div class="col-sm-8"> 
                        <input type="text" class="form-control" id="itsm_header" name="itsm_header" placeholder="SoapAction : mc..." <?php echo 'value="'.$itsm_header.'"';?> >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="itsm_url"><?php echo getLabel("label.admin_itsm.url"); ?> :</label>
                    <div class="col-sm-8"> 
                        <input type="text" class="form-control" id="itsm_url" name="itsm_url" placeholder="http://url.com" <?php echo 'value="'.$itsm_url.'"';?> >
                    </div>
                </div>
                <div class="form-group" id="options_itsm"> 
                    <div class="col-sm-offset-2 col-sm-3">
                        <div class="checkbox">
                            <?php 
                            if($itsm_create=="true"){
                                echo '<label><input id="itsm_create" name="itsm_create" type="checkbox"  value="true" checked>'.getLabel("label.admin_itsm.create").'</label>';
                            }else echo '<label><input id="itsm_create" name="itsm_create" type="checkbox"  value="true" >'.getLabel("label.admin_itsm.create").'</label>';
                            ?>
                        </div>
                    </div>
                    <div class=" col-sm-3">
                        <div class="checkbox">
                            <?php
                                if($itsm_acquit == "true"){
                                    echo '<label><input id="itsm_acquit" name="itsm_acquit" type="checkbox" value ="true" checked>'.getLabel("label.admin_itsm.acquit").'</label>';
                                }else echo '<label><input id="itsm_acquit" name="itsm_acquit" type="checkbox" value ="true" >'.getLabel("label.admin_itsm.acquit").'</label>';
                            ?>
                        </div>
                    </div>
                  
                    <div  id="info_options" class="col-sm-4 alert alert-warning" hidden>
                        <strong><?php echo getLabel("label.admin_itsm.warn"); ?> ! </strong> <?php echo getLabel("label.admin_itsm.warn_text"); ?>
                    </div>
                   
                </div>
                
            </form>
            <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-8">
                        <button class="btn btn-primary" id="btn_form" type="submit">
                            <?php echo getLabel("action.apply"); ?>
                        </button>
                    </div>
                </div>
            

        </div>
    </div>
 

	<br/>
	
	<!-- Result here ! -->
	<div id="result"></div>
	
</div>

<?php include("../../footer.php"); ?>
