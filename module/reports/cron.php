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

use PHPMailer\PHPMailer\PHPMailer;

require 'classes/PHPMailer/src/Exception.php';
require 'classes/PHPMailer/src/PHPMailer.php';
require 'classes/PHPMailer/src/SMTP.php';

include("/srv/eyesofnetwork/eonweb/include/config.php");
include("/srv/eyesofnetwork/eonweb/module/reports/classes/ReportService.php");
include("/srv/eyesofnetwork/eonweb/include/function.php");

$report = ReportService::getById($argv[1]);
ReportService::reportToHTML($report);
$emails = $report->getEmails();

/**
 * Send email
 */
function send_mail($to,$from,$subject,$message,$format=false,$files=false) {

    $mail = new PHPMailer;
    $mail->CharSet = "UTF-8";
    $mail->isHTML($format);
    $mail->AllowEmpty = true;
    
    // Parse from address
    $from=$mail->parseAddresses($from);
    $mail->setFrom($from[0]["address"],$from[0]["name"]);   
    
    // Parse to addresses
    foreach ($to as $t) {    
        $mail->addAddress(trim($t));
    }
    
    // Subject and Body
    $mail->Subject = $subject;
    $mail->Body = $message;
    
    // Add attachment if file exists
    if($files != false) {

        if(file_exists($files)) {
            $mail->addAttachment($files);
        }
    }
    
    // Send email
    if(!$mail->send()) {
        echo "Message could not be sent\n";
        echo "Mailer Error: " . $mail->ErrorInfo."\n";
    }
}

if(!empty($report->getEmails()))
    send_mail($emails, "eyesofnetwork@eyesofnetwork.net", "Eyes Of Network Report: " . $report->getName(), "", false, "/srv/eyesofnetwork/eonweb/module/reports/py/ressources/reports/" . $report->getName() . "_report.pdf");
