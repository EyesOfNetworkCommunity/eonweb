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

?>
<script src="ajax.js"></script>

<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo getLabel("label.report.title"); ?></h1>
        </div>
</h4>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <div class="btn-group pull-right">
                    <div class="btn-group" id="">
                        <a href='edit.php' class="btn btn-info" role="button"><?php echo getLabel("action.add"); ?></a>
                    </div>
            </div>
        </div>
        <div class="panel-body">
                <div class="table-responsive">          
                    <table class="table">
                        <thead>
                        <tr> 
                            <th><?php echo getLabel("label.report.name"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $reports = ReportService::getAllNames();
                            foreach($reports as $report){
                                echo "<tr id=\"line-id-" . $report->getId() . "\">
                                        <td class=\"col-sm-3\" ><a href='edit.php?id=" . $report->getId() . "'>" . $report->getName() . "</a></td>
                                        <td id=\"report-id-". $report->getId() . "\"><button type=\"button\"  onclick='generateReport(" . $report->getId() . ",\"" . $report->getName() . "\")' class=\"btn btn-info\">Generate</button></td>
                                ";
                                if(file_exists("py/ressources/reports/" . $report->getName() . "_report.pdf")) {
                                    echo "<td ". $report->getId() . "\"><a href=\"py/ressources/reports/" . $report->getName() . "_report.pdf\">PDF</a></td>";
                                } else 
                                    echo "<td id=\"pdf-url-". $report->getId() . "\"></td>";
                                echo '<td class="text-right"><button style="width: -webkit-fill-available;max-width: 90px; margin-bottom: 5px; " class="btn btn-danger" type="button" onclick="deleteReport(' . $report->getId() . ')">Supprimer</button></td></tr>';
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
