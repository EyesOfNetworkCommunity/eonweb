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

class ItsmPeer {

    public function __construct(){}
    
    /**
     * @return an itsm object by his url value
     */
    function getItsmByUrl($url_name){
        global $database_eonweb;
        try{
            $result = sql($database_eonweb,"SELECT itsm_id, itsm_url, itsm_file, itsm_ordre, itsm_parent, itsm_type_request, itsm_return_champ FROM itsm WHERE itsm_url = ?", array($url_name));

            if($result != null ){
                $row = $result[0];
                $itsm = new Itsm();
                $itsm->setItsm_id($row["itsm_id"]);
                $itsm->setItsm_url($row["itsm_url"]);
                $itsm->setItsm_file($row["itsm_file"]);
                $itsm->setItsm_order($row["itsm_ordre"]);
                $itsm->setItsm_parent($row["itsm_parent"]);
                $itsm->setItsm_return_champ($row["itsm_return_champ"]);
                $itsm->setItsm_type_request($row["itsm_type_request"]);
                $itsm->setItsm_headers($this->getItsmHeadersByItsmId($itsm->getItsm_id()));
                $itsm->setItsm_vars($this->getItsmVarByItsmId($itsm->getItsm_id()));
                return $itsm;
            }
            return false;
        }catch(Exception $e) {
            return 'Exception reçue : '.$e->getMessage().'\n';
        }
    }

    /**
     * @return an itsm object by his id
     */
    function getItsmById($itsm_id){
        global $database_eonweb;
        try{
            $result = sql($database_eonweb,"SELECT itsm_id, itsm_url, itsm_file, itsm_ordre, itsm_parent, itsm_type_request, itsm_return_champ FROM itsm WHERE itsm_id = ?", array($itsm_id));
            if($result != null){
                $row = $result[0];
                $itsm = new Itsm();
                $itsm->setItsm_id($row["itsm_id"]);
                $itsm->setItsm_url($row["itsm_url"]);
                $itsm->setItsm_file($row["itsm_file"]);
                $itsm->setItsm_order($row["itsm_ordre"]);
                $itsm->setItsm_parent($row["itsm_parent"]);
                $itsm->setItsm_return_champ($row["itsm_return_champ"]);
                $itsm->setItsm_type_request($row["itsm_type_request"]);
                $itsm->setItsm_headers($this->getItsmHeadersByItsmId($itsm->getItsm_id()));
                $itsm->setItsm_vars($this->getItsmVarByItsmId($itsm->getItsm_id()));
                return $itsm;
            }
            return false;
        }catch(Exception $e) {
            return 'Exception reçue : '.$e->getMessage().'\n';
        }
    }

    /**
     * @return list of champ_ged 
     */
    function getListChampGed(){
        global $database_eonweb;
        $sql = 'SELECT * FROM itsm_champ_ged';
        $result = sql($database_eonweb, $sql);
        $champs = array();
        foreach($result as $row){
            $champs[$row["champ_ged_id"]] = $row["champ_ged_name"];
        }
        return $champs;
    }

    /**
     * @return array key=itsm_var_name value=itsm_champs_ged
     */
    function getItsmVarByItsmId($itsm_id){
        global $database_eonweb;
        $sql = 'SELECT itsm_var_name, champ_ged_name, itsm_champ_ged.champ_ged_id as champ_ged_id FROM itsm_var, itsm_champ_ged WHERE itsm_id = ? AND itsm_var.champ_ged_id = itsm_champ_ged.champ_ged_id';
        $result = sql($database_eonweb, $sql, array($itsm_id));
        $itsm_vars = array();
        if($result != null){
            foreach($result as $row){
                $itsm_vars[$row["itsm_var_name"]] = $row["champ_ged_id"];
            }
        }
        return $itsm_vars;
    }
    
    /**
     * @return itsm headers linked to an itsm object by his id
     */
    function getItsmHeadersByItsmId($itsm_id){
        global $database_eonweb;
        $sql = 'SELECT itsm_header_id, header FROM itsm_header WHERE itsm_id = ?';
        $result = sql($database_eonweb, $sql, array($itsm_id));
        $itsm_headers = array();
        if($result != null){
            foreach($result as $row){
                $itsm_headers[$row["itsm_header_id"]] = $row["header"];
            }
        }
        return $itsm_headers;
    }

    /**
     * @return array of itsm witch is a child 
     */
    function getItsmChilds(){
        global $database_eonweb;
        try{
            $result = sql($database_eonweb,"SELECT itsm_id, itsm_url, itsm_file, itsm_ordre, itsm_parent, itsm_type_request, itsm_return_champ FROM itsm WHERE itsm_id NOT IN (SELECT DISTINCT itsm_parent FROM itsm WHERE itsm_parent <> NULL)");
            $tab_itsm = array();
            $nb = $result[0];
            //error_log("itsmPeer.php : $nb \n", 3 , "/srv/eyesofnetwork/eonweb/module/admin_itsm/uploaded_file/log");

            if($result != null){
                foreach($result as $row){
                    $itsm = new Itsm();
                    $itsm->setItsm_id($row["itsm_id"]);
                    $itsm->setItsm_url($row["itsm_url"]);
                    $itsm->setItsm_file($row["itsm_file"]);
                    $itsm->setItsm_order($row["itsm_ordre"]);
                    $itsm->setItsm_parent($row["itsm_parent"]);
                    $itsm->setItsm_return_champ($row["itsm_return_champ"]);
                    $itsm->setItsm_type_request($row["itsm_type_request"]);
                    $itsm->setItsm_headers($this->getItsmHeadersByItsmId($itsm->getItsm_id()));
                    $itsm->setItsm_vars($this->getItsmVarByItsmId($itsm->getItsm_id()));
                    //error_log("itsmPeer.php : ".$row["itsm_id"]."\n", 3 , "/srv/eyesofnetwork/eonweb/module/admin_itsm/uploaded_file/log");
                    array_push($tab_itsm, $itsm);
                }
            }
            return $tab_itsm;
        }catch(Exception $e) {
            return 'Exception reçue : '.$e->getMessage().'\n';
        }
    }
    
    /**
     * @return number of itsm
     */
    function count_itsm(){
        global $database_eonweb;
        try{
            $result = sql($database_eonweb,"SELECT COUNT(*) AS nb FROM itsm ORDER BY itsm_ordre");
            $row = $result[0];
            return intval($row["nb"]);
        }catch(Exception $e) {
            return 'Exception reçue : '.$e->getMessage().'\n';
        }
    }

    /**
     * @return array of itsm object
     */
    function get_all_itsm(){
        global $database_eonweb;
        try{
            $result = sql($database_eonweb,"SELECT itsm_id, itsm_url, itsm_file, itsm_ordre, itsm_parent, itsm_type_request, itsm_return_champ FROM itsm ORDER BY itsm_ordre");
            $tab_itsm = array();
            if($result != false){
                foreach($result as $row){
                    $itsm = new Itsm();
                    $itsm->setItsm_id($row["itsm_id"]);
                    $itsm->setItsm_url($row["itsm_url"]);
                    $itsm->setItsm_file($row["itsm_file"]);
                    $itsm->setItsm_order($row["itsm_ordre"]);
                    $itsm->setItsm_parent($row["itsm_parent"]);
                    $itsm->setItsm_return_champ($row["itsm_return_champ"]);
                    $itsm->setItsm_type_request($row["itsm_type_request"]);
                    $itsm->setItsm_headers($this->getItsmHeadersByItsmId($itsm->getItsm_id()));
                    $itsm->setItsm_vars($this->getItsmVarByItsmId($itsm->getItsm_id()));
                    array_push($tab_itsm, $itsm);
                }
            }
            return $tab_itsm;
        }catch(Exception $e) {
            return 'Exception reçue : '.$e->getMessage().'\n';
        }
    }

}


?>