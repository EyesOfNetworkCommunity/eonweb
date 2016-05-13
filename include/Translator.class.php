<?php
/*
#########################################
#
# Copyright (C) 2016 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION : 5.0
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
	
	private static $dictionnary_content;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// # Languages files
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
			
			// Language detection
			$lang = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$lang = strtolower(substr(chop($lang[0]),0,2));
			$GLOBALS['langformat']=$lang;	
		}
	}
	
	/**
	 * Init File
	 */
	public static function initFile($file,$file_custom)
	{		
		$lang=$GLOBALS['langformat'];
		
		$path_tmp=$file."-$lang.json";
		$path_tmp_custom=$file_custom.".json";
		$path_tmp_custom_lang=$file_custom."-$lang.json";
		$file=$file.".json";
		
		if(file_exists($path_tmp_custom_lang)) { $file=$path_tmp_custom_lang; }
		elseif(file_exists($path_tmp)) { $file=$path_tmp; }
		elseif(file_exists($path_tmp_custom)) { $file=$path_tmp_custom; }
		
		Translator::$dictionnary_content = file_get_contents($file);	
	}
	 
	/**
	 * PHP Dictionnary
	 */
	public static function createPHPDictionnary()
	{
		$dictionnary = json_decode(Translator::$dictionnary_content, true);		
		return $dictionnary;
	}
	
	/**
	 * JS Dictionnary
	 */
	public static function createJSDictionnary()
	{
		echo "<script>";
		echo "var dictionnary = ".Translator::$dictionnary_content;
		echo "</script>\n";
	}
	
}

?>
