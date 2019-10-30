# Module admin_itsm 

Module for eonweb

This module have been created to manage a connexion with an external itsm tool (like glpi, mantis ... ).
This module take different informations to call the api of your ticket application.  

A graphic interface to manage the configuration have been created and files in monitoring_ged had been modified to manage this new functionnality.

Requirements
------------

Eyes of network

Installation 
------------
To use this module you need to have the latest version of eonweb that used all the modification given to the module monitoring_ged, included files and admin_itsm. Moreover to used all the functionality of this tool you have to modified `/srv/eyesofnetwork/ged/scripts/ged-nagios-host` and `/srv/eyesofnetwork/ged/scripts/ged-nagios-service` and add this line : 

``` sh
    #Call script to handle itsm auto-management
    php /srv/eyesofnetwork/eonweb/module/admin_itsm/scripts/get-nagios.php
```

The database have seen some changes to be able to handle the admin itsm module. This file `itsm.sql` will do the proper change. 

Author Information
------------------

* **Jérémy HOARAU** - <jeremy.hoarau@axians.com> - [EyesOfNetwork Community](https://github.com/eyesofnetworkcommunity)
