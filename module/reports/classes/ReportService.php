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

class ReportService {

    public static function save($report)
    {
        global $database_eonweb;
        if(is_null($report->getId())){

            // Insert name
            sql($database_eonweb, "INSERT INTO reports (report_name) VALUES ( ? )", array($report->getName()));

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
        } else {
            
            $savedReport = ReportService::getById($report->getId());

            // Update name
            if($savedReport->getName() != $report->getName()){
                sql($database_eonweb, "UPDATE reports SET report_name = ? WHERE id = ?", array($report->getName(), $report->getId()));
            }

            // Update hosts
            sort($savedReport->getHosts());
            sort($report->getHosts());
            if($savedReport->getHosts() != $report->getHosts()){
                sql($database_eonweb, "DELETE FROM reports_hosts WHERE reportsID = ?", array($report->getId()));
                foreach($report->getHosts() as $host){
                    sql($database_eonweb, "INSERT INTO reports_hosts (hostName, reportsID) VALUES ( ?, ? )", array($host, $report->getId()));
                }
            }

            // Update services
            sort($savedReport->getServices());
            sort($report->getServices());
            if($savedReport->getServices() != $report->getServices()){
                sql($database_eonweb, "DELETE FROM reports_services WHERE reportsID = ?", array($report->getId()));
                foreach($report->getServices() as $service){
                    sql($database_eonweb, "INSERT INTO reports_services (serviceName, reportsID) VALUES ( ?, ? )", array($service, $report->getId()));
                }
            }        
        }
    }

    public static function getByName($reportName)
    {
        global $database_eonweb;
        $report = new Report;

        $savedReport = sql($database_eonweb, "SELECT id FROM reports WHERE report_name = ?", array($reportName));
        $report->setId($savedReport[0]["id"]);
        $report->setName($reportName);

        $savedHosts = sql($database_eonweb, "SELECT hostName FROM reports_hosts WHERE reportsID = ?", array($report->getId()), PDO::FETCH_NUM);
        $savedServices = sql($database_eonweb, "SELECT serviceName FROM reports_services WHERE reportsID = ?", array($report->getId()), PDO::FETCH_NUM);
        $report->setHosts(array_column($savedHosts, 0));
        $report->setServices(array_column($savedServices, 0));

        return $report;
    }

    public static function getById($reportId)
    {
        global $database_eonweb;
        $report = new Report;

        $savedReport = sql($database_eonweb, "SELECT report_name FROM reports WHERE id = ?", array($reportId));
        $savedHosts = sql($database_eonweb, "SELECT hostName FROM reports_hosts WHERE reportsID = ?", array($reportId), PDO::FETCH_NUM);
        $savedServices = sql($database_eonweb, "SELECT serviceName FROM reports_services WHERE reportsID = ?", array($reportId), PDO::FETCH_NUM);
        
        $report->setId($reportId);
        $report->setName($savedReport[0]["report_name"]);
        $report->setHosts(array_column($savedHosts, 0));
        $report->setServices(array_column($savedServices, 0));

        return $report;
    }
}
