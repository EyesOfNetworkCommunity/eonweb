<?php




include_once('/srv/eyesofnetwork/cacti/include/global.php');
include_once('/srv/eyesofnetwork/cacti/lib/functions.php');
include_once('/srv/eyesofnetwork/cacti/lib/plugins.php');


// syslog
//  clear all syslog table

error_log("Start syslog plugin installation", 0);

// $syslogdb_default = 'cacti';
$syslog_install_options['engine'] = 'innodb';
$syslog_install_options['upgrade_type'] = 'truncate';
$syslog_install_options['db_type'] = 'trad';
$syslog_install_options['days'] = 15;

api_plugin_install('syslog');
error_log("Syslog plugin installation completed", 0);

api_plugin_enable('syslog');
error_log("Syslog activation\n", 0);


// weathermap
// 
error_log("Start weathermap plugin installation", 0);
api_plugin_install('weathermap');
error_log("Weathermap plugin installation completed", 0);

api_plugin_enable('weathermap');
error_log("Weathermap activation\n", 0);





?>