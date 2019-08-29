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
    public $database_eonweb;
    public $itsmPeer;
    public $itsm_id;
    public $itsm_url;
    public $itsm_file;
    public $itsm_parent=NULL; //id un seul parents si y'en a un on execute
    public $itsm_parent_champ=NULL;
    public $itsm_order;
    public $itsm_headers = array(); // array("key"=>"value")
    public $itsm_vars = array();//array d'object

    public function __construct(){
        global $database_eonweb;
        $this->itsmPeer = new ItsmPeer();
        $this->database_eonweb = $database_eonweb;
    }

    function save(){
        if(isset($this->itsm_id)){
            //update
            $sql    = 'UPDATE itsm SET itsm_url ="'.$this->itsm_url.'", itsm_file="'.$this->itsm_file.'", itsm_ordre='.$this->itsm_ordre.', itsm_parent =  '.$this->itsm_parent.' , itsm_parent_champ = "'.$this->itsm_parent_champ.'" WHERE itsm_id = '.$this->itsm_id;
            $this->maj_headers_db();
            $this->maj_vars_db();
            $result = sqlrequest($this->database_eonweb,$sql);
        }else{
            //insert
            $sql = 'INSERT INTO itsm(itsm_url, itsm_file, itsm_order, itsm_parent, itsm_parent_champ) VALUES("'.$this->itsm_url.'", "'.$this->itsm_file.'", '.$this->itsm_order.', '.$this->itsm_parent.', "'.$this->itsm_parent_champ.'")';
            $result = sqlrequest($this->database_eonweb,$sql);
            if($result){
                $this->maj_headers_db();
                $this->maj_vars_db();
            }
        }
        
        return $result;
    }

    function delete(){
        $sql = 'DELETE FROM itsm WHERE itsm_id = '.$this->itsm_id;
        $result = sqlrequest($this->database_eonweb,$sql);
        return $result;
    }

    function addHeader($itsm_header){
        $nb = count($this->itsm_headers);
        $this->itsm_headers["new_$nb"];
    }

    function deleteHeader($itsm_header_id){
        unset($this->itsm_headers[$itsm_header_id]);
    }

    private function maj_headers_db(){
        $old_headers        = $this->itsmPeer->getItsmHeadersByItsmId($this->itsm_id);
        $headers_to_delete  = array_diff($old_headers, $this->itsm_headers);
        $headers_to_add     = array_diff($this->itsm_headers, $old_headers);
        $sql_delete         = 'DELETE FROM itsm_header WHERE itsm_header_id=';
        $sql_add            = 'INSERT INTO itsm_header(header,itsm_id) VALUES';
        
        foreach($headers_to_delete as $key=>$value){
            sqlrequest($this->database_eonweb,$sql_delete.$key);
        }

        foreach($headers_to_add as $key=>$value){
            if(preg_match("new_", $key) === 1 ){
                sqlrequest($this->database_eonweb,$sql_add.'("'.$value.'", '.$this->itsm_id.')');
            }
        }
    }

    private function maj_vars_db(){
        $old_vars       = $this->itsmPeer->getItsmVarByItsmId($this->itsm_id);
        $vars_to_delete = array_diff($old_vars, $this->itsm_vars);
        $sql_delete     = 'DELETE FROM itsm_var WHERE itsm_id = '.$this->itsm_id.' AND itsm_var_name="';
        $sql_add        = 'INSERT INTO itsm_var(itsm_id, itsm_var_name, champ_ged_id) VALUES';
        $champs_ged     =  $this->itsmPeer->getListChampGed();

        foreach($vars_to_delete as $key=>$value){
            sqlrequest($this->database_eonweb,$sql_delete.$key.'"');
        }

        foreach($this->getItsm_vars as $key=>$value){
            $key_ged = array_search($value, $champs_ged);
            if(array_key_exists($key,$old_vars)){
                sqlrequest($this->database_eonweb,'UPDATE FROM itsm_var SET champ_ged_id ='.$key_ged.' WHERE itsm_var_name="'.$key.'" AND itsm_id='.$this->itsm_id);
            }else{
                sqlrequest($this->database_eonweb,$sql_add.'('.$this->itsm_id.', "'.$key.'", '.$key_ged.')');
            }
        }

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
     * Get the value of itsm_parent_champ
     */ 
    public function getItsm_parent_champ()
    {
        return $this->itsm_parent_champ;
    }

    /**
     * Set the value of itsm_parent_champ
     *
     * @return  self
     */ 
    public function setItsm_parent_champ($itsm_parent_champ)
    {
        $this->itsm_parent_champ = $itsm_parent_champ;

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
}