<?php
// require('fpdf/fpdf.php');

// $pdf = new FPDF();
// $pdf->AddPage();
// $pdf->SetFont('Arial','B',16);
// $pdf->Cell(40,10,'Hello World!');
// $pdf->Image('http://localhost/grafana/render/d-solo/W_KoryF7k/localhost-dashboard?orgId=1&from=1635845908992&to=1636018708992&panelId=1&width=1000&height=500&tz=Europe%2FParis',60,30,90,0,'PNG');
// $pdf->Output();




// $image_url = 'https://192.168.80.31/grafana/render/d-solo/W_KoryF7k/localhost-dashboard?orgId=1&from=1635858333993&to=1636031133993&panelId=1&width=1000&height=500&tz=Europe%2FParis';
// $save_as = '1234.png';
// $ch = curl_init($image_url);
// curl_setopt($ch, CURLOPT_HEADER, false);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// curl_setopt( $ch, CURLOPT_COOKIEJAR,  $_COOKIE['group_id'] );
// curl_setopt( $ch, CURLOPT_COOKIEFILE, $_COOKIE['session_id'] );
// curl_setopt( $ch, CURLOPT_COOKIEFILE, $_COOKIE['user_id'] );
// curl_setopt( $ch, CURLOPT_COOKIEFILE, $_COOKIE['user_limitation'] );
// curl_setopt( $ch, CURLOPT_COOKIEFILE, $_COOKIE['user_name'] );
// // curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
// $raw_data = curl_exec($ch);
// curl_close($ch);
// $fp = fopen($save_as, 'w');
// var_dump($raw_data);
// fwrite($fp, $raw_data);
// fclose($fp);
// $content = file_get_contents('http://phoenixjp.net/news/include/img/pjp_logo.png');
// file_put_contents('flower.jpg', $content);
?>

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
// ini_set('display_errors','on');
// error_reporting(E_ALL);

include("../../header.php");
include("../../side.php");


?>


<div id="page-wrapper">

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Rapports</h1>
        </div>
</h4>
	</div>
    
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">
                Les rapports
            </h4>
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
                        <!-- <tr> colspan="2" -->
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Publique</th>
                            <th>Mail</th>
                            <th>Schedule</th>
                            <th>Last Time Run</th>
                            <th>Duration</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                               
                                // foreach($list as $item){
                                // }
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
