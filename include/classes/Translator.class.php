<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.3
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

/**
 * Translator class for all eonweb's pages
 *
 * usage examples :
 * PHP : 		echo getLabel("label...");
 * Javascript : document.write(dictionnary["label.message.logout.success"]);
 * JS in PHP : 	echo '<script>document.write('.getLabel("label.message.logout.success").')</script>';
 */
class Translator
{
	
	private $dictionnary_content;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{

		global $database_eonweb;
		$lang = 0;
		
		// # Languages files
		
		// Check if user default lang is defined
		if(isset($_COOKIE['user_id'])){
			$idUser =$_COOKIE['user_id'];			
			$lang=$result["user_language"];
			$lang = sql($database_eonweb, "SELECT user_language from users where user_id= ?", array($idUser));
			$lang = $lang[0][0];
		}
		
		// Check if isset browser lang
		if($lang) {
			$GLOBALS['langformat']=$lang;	
		}
		elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			
			// Language detection
			$lang = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$lang = strtolower(substr(chop($lang[0]),0,2));
			$GLOBALS['langformat']=$lang;	
		}
	}

	/**
	 * Get File
	 */
	public function getFile($file,$file_custom)
	{
		$lang=$GLOBALS['langformat'];

		$path_tmp=$file."-$lang.json";
		$path_tmp_custom=$file_custom.".json";
		$path_tmp_custom_lang=$file_custom."-$lang.json";
		$file=$file.".json";

		if(file_exists($path_tmp_custom_lang)) { $file=$path_tmp_custom_lang; }
		elseif(file_exists($path_tmp)) { $file=$path_tmp; }
		elseif(file_exists($path_tmp_custom)) { $file=$path_tmp_custom; }

		return $file;
	}
	
	/**
	 * Init File
	 */
	public function initFile($file,$file_custom)
	{				
		global $path_messages_custom;
		$lang=$GLOBALS['langformat'];
		
		// Get file to use
		$file_final = $this->getFile($file,$file_custom);	
	
		// If language file do merge
		if(preg_match("#$path_messages_custom#", $file_final)) {
			if(preg_match("#".$path_messages_custom."-".$lang."#", $file_final)){
				$file=$file."-".$lang;
			}
			$messages_custom=json_decode(file_get_contents($file_final),true);
			$messages=json_decode(file_get_contents($file.".json"),true);
			$messages_all=array_merge($messages,$messages_custom);
			$this->dictionnary_content = json_encode($messages_all);
		}
		else {
			$this->dictionnary_content = file_get_contents($file_final);
		}
		
		return $this->dictionnary_content;
	}
	 
	/**
	 * PHP Dictionnary
	 */
	public function createPHPDictionnary()
	{
		$dictionnary = json_decode($this->dictionnary_content, true);		
		return $dictionnary;
	}
	
	/**
	 * JS Dictionnary
	 */
	public function createJSDictionnary()
	{
		echo "<script>";
		echo "var dictionnary = ".$this->dictionnary_content;
		echo "</script>\n";
	}
	
}

?>
