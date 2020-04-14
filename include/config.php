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

// #######################################
// # General Information
// #######################################
$version="5";
$release="3";
$surname="Zélie";

// #######################################
// # Database config information
// #######################################
$database_host="localhost";
$database_port="3306";

$database_username="eonweb";
$database_password="root66";

$database_cacti="cacti";
$database_eonweb="eonweb";
$database_ged="ged";
$database_lilac="lilac";
$database_nagios="nagiosbp";
$database_notifier="notifier";

// ###################################################
// # EyesOfNetwork
// ###################################################

// # Default language format
$langformat="en";

// # Logs options
$dateformat="M j, Y g:i:s A";
$datepurge="-1 month";

// # Menu Config
// You can view tabid in eonweb database
$defaulttab=1;
$defaultpage="./module/dashboard_view/index.php";

// # Max number of lines in a tablesorter
$maxlines=500;

// # Page refresh interval
$refresh_time=60;

// # Cookie domain
$cookie_domain="";

// # Cookie destroy time
$cookie_time=0;
// 4 hour : $cookie_time=4*60*60;

// LDAP
$ldap_search_begins=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0','\\28');

// Number of back-up file to use for nagios configuration file.
$max_bu_file = 5;

// # Define All Path
$path_eon="/srv/eyesofnetwork";
$path_eonweb="$path_eon/eonweb";
$path_frame="/module/module_frame/index.php?url=";
$dir_imgcache="cache";
$path_images="/images";
$path_logo="$path_images/logo.svg";
$path_logo_custom="$path_images/custom.logo.png";
$path_logo_favicon="$path_images/favicon.png";
$path_logo_favicon_custom="$path_images/custom.favicon.png";
$path_logo_navbar="$path_images/logo.svg";
$path_logo_navbar_custom="$path_images/custom.logo-navbar.png";
$path_languages="$path_eonweb/include/languages";
$path_messages="$path_languages/messages";
$path_messages_custom="$path_languages/custom.messages";
$path_menus="$path_languages/menus";
$path_menus_custom="$path_languages/custom.menus";
$path_menu_limited="$path_languages/menus-limited";
$path_menu_limited_custom="$path_languages/custom.menus-limited";
$path_reports="$path_eonweb/include/reports";

// # Nagios
$path_nagios="$path_eon/nagios";
$path_notifier="$path_eon/notifier";
$path_nagios_url="/thruk";
$path_nagios_cgi="/thruk/cgi-bin";
$path_nagios_url_others="/nagios";
$path_nagios_cgi_others="/nagios/cgi-bin";
$path_nagios_bin="$path_nagios/bin/nagios";
$path_nagios_etc="$path_nagios/etc/nagios.cfg";
$path_nagios_ser=$path_nagios."/etc/objects/services.cfg";
$path_nagiosbpcfg=$path_nagios."bp/etc/nagios-bp.conf";
$path_nagiosbpcfg_bu="/tmp/nagios-bp.conf";
$path_nagiosbpcfg_lock="/tmp/nagios_bp";
$path_nagiosdowntime="$path_nagios/plugins/Downtime/downtime_list.txt";
$path_notification="$path_notifier/etc/notifier.rules";

// # GED
$ged_prefix="";
$path_ged="$path_eon/ged";
$path_ged_bin="$path_ged/bin/gedq";
$path_gedcfg="$path_ged/etc/ged.cfg";
$path_gedhdb="$path_ged/etc/bkd/gedmysql.cfg";
$path_gedqcfg="$path_ged/etc/gedq.cfg";
$path_gedtcfg="$path_ged/etc/gedt.cfg";

// # Notifier
$path_notifier_rules="$path_notifier/etc/notifier.rules";
$path_notifier_methods="$path_notifier/etc/notifier.cfg";

// # NetCAT
$default_minport=1;
$default_maxport=1024;

?>
