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


class Itsm{
    public $itsmPeer;
    public $itsm_id;
    public $itsm_url;
    public $itsm_file;
    public $itsm_parent=NULL; //id un seul parents si y'en a un on execute
    public $itsm_return_champ=NULL;//
    public $itsm_order=NULL;
    public $itsm_type_request="get";//
    public $itsm_headers = array(); // array("key"=>"value")
    public $itsm_vars = array();//array d'object

    public function __construct(){
        
        $this->itsmPeer = new ItsmPeer();
    }

    function save(){
        global $database_eonweb;
        if(isset($this->itsm_id)){
            //update 
            $prepare = [$this->itsm_url,"".$this->itsm_file,"".$this->itsm_return_champ,"".$this->itsm_type_request];
            
            $sql    = 'UPDATE itsm SET itsm_url =?, itsm_file=? , itsm_return_champ = ? , itsm_type_request = ? ';
            $end = 'WHERE itsm_id = ?';
            if($this->insert_sql($this->itsm_order) != "NULL" ){
                $sql = $sql.', itsm_ordre=? ';
                $prepare[0] = $prepare[0];
                array_push($prepare,"".$this->insert_sql($this->itsm_order));
            }
            if($this->insert_sql($this->itsm_parent)!= "NULL"){
                $sql = $sql.', itsm_parent = ? ';
                $prepare[0] = $prepare[0];
                array_push($prepare,"".$this->insert_sql($this->itsm_parent));
            }

            $sql = $sql.$end;
            array_push($prepare,"".$this->itsm_id);
            $prepare[0] = $prepare[0];
            $result = sql($database_eonweb,$sql,$prepare);
            $this->maj_headers_db();
            $this->maj_vars_db();
            $description = "itsm config: " . $this->itsm_url . " was updated"; 
            logging("itsm", $description, $_COOKIE['user_name']);
        }else{
            //insert
            $prepare = [ "$this->itsm_url", "$this->itsm_file",  "$this->itsm_return_champ", "$this->itsm_type_request"];
            
            $sql = 'INSERT INTO itsm(itsm_url, itsm_file, itsm_return_champ, itsm_type_request';
            $end = ') VALUES(?, ?, ?, ?';

            if($this->insert_sql($this->itsm_order) != "NULL" ){
                $sql = $sql.', itsm_ordre';
                $end = $end.', ? ';
                $prepare[0] = $prepare[0];
                array_push($prepare,"".$this->insert_sql($this->itsm_order));
            }
            if($this->insert_sql($this->itsm_parent)!= "NULL"){
                $sql = $sql.', itsm_parent';
                $end = $end.', ? ';
                $prepare[0] = $prepare[0];
                array_push($prepare,"".$this->insert_sql($this->itsm_parent));
            }
            $sql = $sql.$end.")";
            $result = sql($database_eonweb,$sql, $prepare);
            if($result){
                $id = sql($database_eonweb,"SELECT itsm_id FROM itsm WHERE itsm_url = ?", array($this->itsm_url)); 
                $this->itsm_id = $id[0][0];          
                $this->maj_headers_db();
                $this->maj_vars_db();
            }
            $description = "itsm config: " . $this->itsm_url . " was created"; 
            logging("itsm", $description, $_COOKIE['user_name']);
        }
        
        return $result;
    }

    function delete(){
        global $database_eonweb;
        $sql = 'DELETE FROM itsm WHERE itsm_id = ?';
        $result = sql($database_eonweb,$sql, array($this->itsm_id));
        return $result;
    }

    function execute_itsm($previous=false, $ged_type=NULL, $queue=NULL, $id_ged=NULL){
        $value_parent = false;
        if(isset($this->itsm_parent)){
            $parent = $this->itsmPeer->getItsmById($this->itsm_parent);
            $value_parent = $parent->execute_itsm();
        }

        $headers = array();
        foreach($this->itsm_headers as $header){
            if(preg_match("%PARENT_VALUE%",$header)==1 && $value_parent != false){
                $header = str_replace("%PARENT_VALUE%",$value_parent, $header);
            } 
            array_push($headers,$header);
        }

        if(isset($this->itsm_file) && !empty($this->itsm_file)){
            $extension              = explode(".",basename($this->itsm_file))[1];
            $file_content           = file_get_contents($this->itsm_file);
        }else $file_content = "";
        
        //$content_type_header    = ($extension == "xml")? 'Content-Type: text/xml;charset=UTF-8':'Content-Type: application/json;charset=UTF-8';
        $tab_champGed = $this->itsmPeer->getListChampGed();
        if(isset($id_ged)){
            foreach($this->itsm_vars as $key=>$value){
                
                $value_champ_ged = get_champ_ged($tab_champGed[$value], $ged_type, $queue, $id_ged);
                $file_content = str_replace($key,$value_champ_ged,$file_content);
            }
        }

        if(preg_match("%PREVIOUS%",$file_content)==1 && $previous != false){
            $file_content = str_replace("%PREVIOUS%",$previous, $file_content);
        }

        $url=$this->itsm_url;
        if(preg_match("%PREVIOUS%",$this->itsm_url)==1 && $previous != false){
            $url = str_replace("%PREVIOUS%",$previous, $url);
        }

        $result = curl_call($headers,$url,$file_content,$this->itsm_type_request); 
        $json_obj = json_decode($result,true);
        if(isset($json_obj) && gettype($json_obj) == "array" ){
	        $result = $json_obj[$this->itsm_return_champ];
        }else return true;

        if(empty($result)){
            return true;
        }else{
            return $result;
        }
        
    }

    private function maj_headers_db(){
        global $database_eonweb;
        $old_headers        = $this->itsmPeer->getItsmHeadersByItsmId($this->itsm_id);
        $headers_to_delete  = array_diff($old_headers, $this->itsm_headers);
        $headers_to_add     = array_diff($this->itsm_headers, $old_headers);
        $sql_delete         = 'DELETE FROM itsm_header WHERE itsm_header_id=?';
        $sql_add            = 'INSERT INTO itsm_header(header,itsm_id) VALUES (?, ?)';
        
        foreach($headers_to_delete as $key=>$value){
            sql($database_eonweb,$sql_delete, array($key));
        }

        foreach($headers_to_add as $key=>$value){ 
            sql($database_eonweb,$sql_add, array($value, $this->itsm_id));
        }
    }

    private function maj_vars_db(){ 
        global $database_eonweb;
        $old_vars       = $this->itsmPeer->getItsmVarByItsmId($this->itsm_id);
        $vars_to_delete = array_diff($old_vars, $this->itsm_vars);
        $sql_delete     = 'DELETE FROM itsm_var WHERE itsm_id = ? AND itsm_var_name= ?';
        $sql_add        = 'INSERT INTO itsm_var(itsm_id, itsm_var_name, champ_ged_id) VALUES (?,?,?)';

        foreach($vars_to_delete as $key=>$value){
            sql($database_eonweb ,$sql_delete, array($this->itsm_id, $key));
        }

        foreach($this->itsm_vars as $key=>$value){
            // $value = id champ_ged
            if(array_key_exists($key,$old_vars)){
                sql($database_eonweb,'UPDATE itsm_var SET champ_ged_id =? WHERE itsm_var_name=? AND itsm_id=?',array($value,$key,$this->itsm_id));
            }else{
                sql($database_eonweb,$sql_add,array($this->itsm_id,$key,$value));
            }
        }

    }

    public function up(){
        global $database_eonweb;
        $order = intval($this->itsm_order)-1;
        $sql1='UPDATE itsm SET itsm_ordre = itsm_ordre+1 WHERE itsm_ordre= ?';
        $sql2='UPDATE itsm SET itsm_ordre = itsm_ordre-1 WHERE itsm_id= ?';
        sql($database_eonweb,$sql1, array($order));
        sql($database_eonweb,$sql2, array($this->itsm_id));
    }

    public function down(){
        global $database_eonweb;
        $order = intval($this->itsm_order)+1;
        $sql1='UPDATE itsm SET itsm_ordre = itsm_ordre-1 WHERE itsm_ordre= ?';
        $sql2='UPDATE itsm SET itsm_ordre = itsm_ordre+1 WHERE itsm_id= ?';

        sql($database_eonweb,$sql1, array($order));
        sql($database_eonweb,$sql2, array($this->itsm_id));
    }

    function insert_sql($var){
        if(isset($var)){
            return $var;
        }else return "NULL";
    }
//===============GETTER AND SETTER ==========================
    
    /**
     * Get the value of itsm_id
     */ 
    public function getItsm_id()
    {
        return $this->itsm_id;
    }

    /**
     * Set the value of itsm_id
     *
     * @return  self
     */ 
    public function setItsm_id($itsm_id)
    {
        $this->itsm_id = $itsm_id;

        return $this;
    }

    /**
     * Get the value of itsm_url
     */ 
    public function getItsm_url()
    {
        return $this->itsm_url;
    }

    /**
     * Set the value of itsm_url
     *
     * @return  self
     */ 
    public function setItsm_url($itsm_url)
    {
        $this->itsm_url = $itsm_url;

        return $this;
    }

    /**
     * Get the value of itsm_file
     */ 
    public function getItsm_file()
    {
        return $this->itsm_file;
    }

    /**
     * Set the value of itsm_file
     *
     * @return  self
     */ 
    public function setItsm_file($itsm_file)
    {
        $this->itsm_file = $itsm_file;

        return $this;
    }

    /**
     * Get the value of itsm_parent
     */ 
    public function getItsm_parent()
    {
        return $this->itsm_parent;
    }

    /**
     * Set the value of itsm_parent
     *
     * @return  self
     */ 
    public function setItsm_parent($itsm_parent)
    {
        $this->itsm_parent = $itsm_parent;

        return $this;
    }

    /**
     * Get the value of itsm_order
     */ 
    public function getItsm_order()
    {
        return $this->itsm_order;
    }

    /**
     * Set the value of itsm_order
     *
     * @return  self
     */ 
    public function setItsm_order($itsm_order)
    {
        $this->itsm_order = $itsm_order;

        return $this;
    }

    /**
     * Get the value of itsm_headers
     */ 
    public function getItsm_headers()
    {
        return $this->itsm_headers;
    }

    /**
     * Set the value of itsm_headers
     *
     * @return  self
     */ 
    public function setItsm_headers($itsm_headers)
    {
        $this->itsm_headers = $itsm_headers;

        return $this;
    }

    /**
     * Get the value of itsm_vars
     */ 
    public function getItsm_vars()
    {
        return $this->itsm_vars;
    }

    /**
     * Set the value of itsm_vars
     *
     * @return  self
     */ 
    public function setItsm_vars($itsm_vars)
    {
        $this->itsm_vars = $itsm_vars;

        return $this;
    }

    /**
     * Get the value of itsm_type_request
     */ 
    public function getItsm_type_request()
    {
        return $this->itsm_type_request;
    }

    /**
     * Set the value of itsm_type_request
     *
     * @return  self
     */ 
    public function setItsm_type_request($itsm_type_request)
    {
        $this->itsm_type_request = $itsm_type_request;

        return $this;
    }

    /**
     * Get the value of itsm_return_champ
     */ 
    public function getItsm_return_champ()
    {
        return $this->itsm_return_champ;
    }

    /**
     * Set the value of itsm_return_champ
     *
     * @return  self
     */ 
    public function setItsm_return_champ($itsm_return_champ)
    {
        $this->itsm_return_champ = $itsm_return_champ;

        return $this;
    }
}