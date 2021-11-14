<?php
/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
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
//ini_set('display_errors','on');
//error_reporting(E_ALL);

include("../../header.php");
include("../../side.php");
?>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Rapports</h1>
		</div>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">
                Les rapports
            </h4>
            
        </div>
        <div class="panel-body">
            <form  class="form-horizontal" id="myForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="report_name">Nom :</label>
                    <div class="col-sm-6"> 
                        <input type="text" class="form-control" id="report_name" name="report_name" required>
                    </div>
                </div>
            </form>
            <div class="form-group"> 
                    <div class="col-sm-offset-2 col-sm-4">
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
