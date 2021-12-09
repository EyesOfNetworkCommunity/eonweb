<?php
/*
#########################################
#
# Copyright (C) 2019 EyesOfNetwork Team
# DEV NAME : Julien GONZALEZ
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

include("Report.php");
include("HostService.php");

class ReportService {

    public static function save($report)
    {
        global $database_eonweb;
        if(is_null($report->getId())){
            // Insert name
            sql($database_eonweb, "INSERT INTO reports (reportName, period, cron) VALUES ( ?, ?, ? )", array($report->getName(), $report->getPeriod(), $report->getCron()));

            $savedReport = ReportService::getByName($report->getName());
            $report->setId($savedReport->getId());

            // Insert hosts
            foreach($report->getHosts() as $host){
                sql($database_eonweb, "INSERT INTO reports_hosts (hostName, reportsID) VALUES ( ?, ? )", array($host, $report->getId()));
            }

            // Insert services
            foreach($report->getServices() as $service){
                sql($database_eonweb, "INSERT INTO reports_services (serviceName, reportsID) VALUES ( ?, ? )", array($service, $report->getId()));
            }

            // Insert emails
            foreach($report->getEmails() as $email){
                sql($database_eonweb, "INSERT INTO reports_emails (email, reportsID) VALUES ( ?, ? )", array($email, $report->getId()));
            }

        } else {
            $savedReport = ReportService::getById($report->getId());
            if($savedReport != null) {

                if($savedReport->getName() != $report->getName() || $savedReport->getPeriod() != $report->getPeriod() || $savedReport->getCron() != $report->getCron()){
                    sql($database_eonweb, "UPDATE reports SET reportName = ?, period = ?, cron = ? WHERE id = ?", array($report->getName(), $report->getPeriod(), $report->getCron(), $report->getId()));
                }

                // Update hosts
                $sortedSavedHosts = $savedReport->getHosts();
                $sortedHosts = $report->getHosts();
                if(!empty($sortedSavedHosts) && !empty($sortedHosts)) {
                    sort($sortedSavedHosts);
                    sort($sortedHosts);
                }
                if($sortedSavedHosts != $sortedHosts){
                    sql($database_eonweb, "DELETE FROM reports_hosts WHERE reportsID = ?", array($report->getId()));
                    foreach($report->getHosts() as $host){
                        sql($database_eonweb, "INSERT INTO reports_hosts (hostName, reportsID) VALUES ( ?, ? )", array($host, $report->getId()));
                    }
                }

                // Update services
                $sortedSavedServices = $savedReport->getServices();
                $sortedServices = $report->getServices();
                if(!empty($sortedSavedServices) && !empty($sortedServices)) {
                    sort($sortedSavedServices);
                    sort($sortedServices);
                }

                if($sortedSavedServices != $sortedServices){
                    sql($database_eonweb, "DELETE FROM reports_services WHERE reportsID = ?", array($report->getId()));
                    foreach($report->getServices() as $service){
                        sql($database_eonweb, "INSERT INTO reports_services (serviceName, reportsID) VALUES ( ?, ? )", array($service, $report->getId()));
                    }
                }

                // Update emails
                $sortedSavedEmails = $savedReport->getEmails();
                $sortedEmails = $report->getEmails();
                if(!empty($sortedSavedEmails) && !empty($sortedEmails)) {
                    sort($sortedSavedEmails);
                    sort($sortedEmails);
                }

                if($sortedSavedEmails != $sortedEmails){
                    sql($database_eonweb, "DELETE FROM reports_emails WHERE reportsID = ?", array($report->getId()));
                    foreach($report->getEmails() as $email){
                        sql($database_eonweb, "INSERT INTO reports_emails (email, reportsID) VALUES ( ?, ? )", array($email, $report->getId()));
                    }
                }      
            }
        }
    }

    public static function delete($report)
    {
        global $database_eonweb;
        sql($database_eonweb, "DELETE FROM  reports WHERE id = ?", array($report->getId()));
        sql($database_eonweb, "DELETE FROM  reports_servcies WHERE reportsID = ?", array($report->getId()));
        sql($database_eonweb, "DELETE FROM  reports_services WHERE reportsID = ?", array($report->getId()));
        sql($database_eonweb, "DELETE FROM  reports_emails WHERE reportsID = ?", array($report->getId()));

        return true;
    }
    public static function getByName($reportName)
    {
        global $database_eonweb;
        $report = new Report;

        $savedReport = sql($database_eonweb, "SELECT id, period, cron FROM reports WHERE reportName = ?", array($reportName));
        $report->setId($savedReport[0]["id"]);
        $report->setName($reportName);

        $savedHosts = sql($database_eonweb, "SELECT hostName FROM reports_hosts WHERE reportsID = ?", array($report->getId()), PDO::FETCH_NUM);
        $savedServices = sql($database_eonweb, "SELECT serviceName FROM reports_services WHERE reportsID = ?", array($report->getId()), PDO::FETCH_NUM);
        $savedEmails = sql($database_eonweb, "SELECT email FROM reports_emails WHERE reportsID = ?", array($report->getId()), PDO::FETCH_NUM);
        $report->setHosts(array_column($savedHosts, 0));
        $report->setServices(array_column($savedServices, 0));
        $report->setPeriod($savedReport[0]["period"]);
        $report->setCron($savedReport[0]["cron"]);
        $report->setEmails(array_column($savedEmails, 0));

        return $report;
    }

    public static function getById($reportId)
    {
        global $database_eonweb;
        $report = new Report;

        $savedReport = sql($database_eonweb, "SELECT reportName, period, cron FROM reports WHERE id = ?", array($reportId));
        $savedHosts = sql($database_eonweb, "SELECT hostName FROM reports_hosts WHERE reportsID = ?", array($reportId), PDO::FETCH_NUM);
        $savedServices = sql($database_eonweb, "SELECT serviceName FROM reports_services WHERE reportsID = ?", array($reportId), PDO::FETCH_NUM);
        $savedEmails = sql($database_eonweb, "SELECT email FROM reports_emails WHERE reportsID = ?", array($reportId), PDO::FETCH_NUM);

        if(empty($savedReport)){
            return null;
        }

        $report->setId($reportId);
        $report->setName($savedReport[0]["reportName"]);
        $report->setHosts(array_column($savedHosts, 0));
        $report->setServices(array_column($savedServices, 0));
        $report->setPeriod($savedReport[0]["period"]);
        $report->setCron($savedReport[0]["cron"]);
        $report->setEmails(array_column($savedEmails, 0));

        return $report;
    }

    public static function getAllNames()
    {
        global $database_eonweb;
        $savedReports = sql($database_eonweb, "SELECT * FROM reports", null, PDO::FETCH_ASSOC);
       
        $reports = array();

        foreach($savedReports as $savedReport){
            $report = new Report;
            $report->setId($savedReport["id"]);
            $report->setName($savedReport["reportName"]);

            array_push($reports, $report);
        }

        return $reports;
    }


    public static function reportToHTML($report, $return = true)
    {
        global $eon_api_token;

        $panelIdServices = array();
        foreach($report->getServices() as $service) {
            switch($service) {
                case "interfaces":
                    array_push($panelIdServices, "1", "2");
                    break;
                case "memory":
                    array_push($panelIdServices, "3", "4");
                    break;
                case "partitions":
                    array_push($panelIdServices, "5", "6", "7", "8", "9", "10", "11");
                    break;
                case "processor":
                    array_push($panelIdServices, "12");
                    break;
            }
        }

        $dashId = array();
        $hosts = array();
        foreach(HostService::getHosts() as $host) {
            if(in_array($host["name"], $report->getHosts())) {
                array_push($dashId, $host["custom_variables"]["DASHID"]);
                array_push($hosts, $host["name"]);
            }
        }

        $period = array();
        $end = date_create('now');
        $start = date_create('now');;

        switch($report->getPeriod()) {
            case "lastDay":
                date_sub($start, DateInterval::createFromDateString('1 day'));
                $start = new DateTime(date_format($start, 'Y-m-d'));
                $end = new DateTime(date_format($start, 'Y-m-d 23:59:59'));
                break;
            case "thisDay":
                $start = new DateTime(date_format($start, 'Y-m-d'));
                break;
            case "lastWeek":
                $start = new DateTime(date("Y-m-d", strtotime("last week monday")));
                $end = new DateTime(date("Y-m-d 23:59:59", strtotime("last week sunday")));
                break;
            case "thisWeek":
                $start = new DateTime(date("Y-m-d", strtotime("last monday")));
                break;
            case "lastMonth":
                date_sub($start, DateInterval::createFromDateString('1 month'));
                $start = new DateTime(date_format($start, 'Y-m-01'));
                $end = new DateTime(date_format($start, 'Y-m-t 23:59:59'));
                break;
            case "thisMonth":
                $start = new DateTime(date_format($start, 'Y-m-01'));
                break;
            case "last6Month":
                date_sub($start, DateInterval::createFromDateString('5 month'));
                $start = new DateTime(date_format($start, 'Y-m-01'));
                break;
            case "lastYear":
                date_sub($start, DateInterval::createFromDateString('1 year'));
                $start = new DateTime(date_format($start, 'Y-01-01'));
                $end = new DateTime(date_format($start, 'Y-12-t 23:59:59'));
                break;
            case "thisYear":
                $start = new DateTime(date_format($start, 'Y-01-01'));
                break;
        }

        array_push($period, strval($start->getTimestamp()));
        array_push($period, strval($end->getTimestamp()));
        
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, "http://localhost:5000/report");
            curl_setopt($ch, CURLOPT_POST, true);
            if($return == false) {
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'));
            
            $params = '{
                "hostname": ' . json_encode($hosts) . ',
                "period": ' . json_encode($period) . ',
                "dashId": ' . json_encode($dashId) . ',
                "serviceId": ' . json_encode($panelIdServices) . ',
                "type": "' . $report->getPeriod() . '",
                "reportname": "' . $report->getName() . '",
                "key" : "'. $eon_api_token .'"
            }';

            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                curl_close($ch);
                return false;
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == intval(200)){
                curl_close($ch);
                return true;
            }
            else{
                curl_close($ch);
                return "Error : " . $http_code;
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function generateCron()
    {
        global $database_eonweb;
        $savedCrons = sql($database_eonweb, "SELECT id, cron FROM reports", null, PDO::FETCH_ASSOC);
        $cronTab = "";
        foreach($savedCrons as $cron) {
            switch($cron["cron"]) {
                case "never":
                    $cronTab .= "";
                    break;
                case "everyDay":
                    $cronTab .= "0 0 * * * /usr/bin/php /srv/eyesofnetwork/eonweb/module/reports/cron.php " . $cron["id"] . "\n";
                    break;
                case "everyWeek":
                    $cronTab .= "0 0 * * 0 /usr/bin/php /srv/eyesofnetwork/eonweb/module/reports/cron.php " . $cron["id"] . "\n";
                    break;
                case "everyMonth":
                    $cronTab .= "0 0 1 * * /usr/bin/php /srv/eyesofnetwork/eonweb/module/reports/cron.php " . $cron["id"] . "\n";
                    break;
                case "every6Month":
                    $cronTab .= "0 0 1 */6 * /usr/bin/php /srv/eyesofnetwork/eonweb/module/reports/cron.php " . $cron["id"] . "\n";
                    break;
                case "everyYear":
                    $cronTab .= "0 0 1 1 * /usr/bin/php /srv/eyesofnetwork/eonweb/module/reports/cron.php " . $cron["id"] . "\n";
                    break;
            }
        }
        $filename = 'py/cron/eyesofnetwork.cron';
        if (!$handle = fopen($filename, 'w')) {
            exit;
        }

        if (fwrite($handle, $cronTab) === FALSE) {
            exit;
        }
        fclose($handle);
        
        exec('crontab py/cron/eyesofnetwork.cron', $output,$result);
    }
}
