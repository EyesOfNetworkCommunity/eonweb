# Module admin_itsm 
## <version 1.0>

Module for eonweb

This module have been created to manage a connexion with an external itsm tool (like glpi, mantis ... ).
This module take different informations to call the api of your ticket application.  

A graphic interface to manage the configuration have been created and the interface of monitoring_ged had been modified to manage those new functionnalities.

Requirements
------------

Eyes of Network 5.3 

Installation 
------------
To use this module you need to have the latest version of eonweb that used all the modification given to the module monitoring_ged, included files and admin_itsm. Moreover to used all the functionality of this tool you have to modified if necessary to automatize the creation of ticket, `/srv/eyesofnetwork/ged/scripts/ged-nagios-host` and `/srv/eyesofnetwork/ged/scripts/ged-nagios-service` and add this line : 

``` shell
    #Call script to handle itsm auto-management
    php /srv/eyesofnetwork/eonweb/module/admin_itsm/scripts/get-nagios.php
```

The database have seen some changes to be able to handle the admin itsm module. This file `itsm.sql` will do the proper change. 

Configuration
-------------
The configuration depends of the itsm you used. Most of those tools needs a json or xml file to be send through an api request, those files could be upload and modify with value whitch will be change by value before the request is executed. This exemple bellow will help you to create yours. 

``` json
{
    "input": {
        "id": "%PREVIOUS%", 
        "group": "",
        "type": "2",
        "status": "6"
        }
}
```

Moreover most of api's request require to get a token first to then proceed other action. 
To do this, this tool can use queries run in succession and can, thanks to reused variables, get the result from previous queries and so-called "parent" queries. (The parent request will allow for example to retrieve the TOKEN and be reused in all the following requests while the request 
the requests that precede an other request allow the transfert of its result to one another). An exemple is for requests where one must recover at first a token then an id and finally create a ticket. It will be necessary then to use a parent request which will make it possible to recover %PARENT_VALUE% (that can be the token to securise request to the api), then a request to recover the id (of a group for example) that will be injected directly into the following creation request through a predefined variable %PREVIOUS%.

<!-- graph LR
   request_get_token \-\->|%PARENT_VALUE% contains the response|request_get_id & request_create_ticket & request_change_author
   request_get_id \-\->|%PREVIOUS% contains the response|request_create_ticket
   request_create_ticket \-\->|%PREVIOUS% contains for exemple the id of the newly ticket|request_change_author -->

   [![](https://mermaid.ink/img/eyJjb2RlIjoiZ3JhcGggTFJcbiAgIHJlcXVlc3RfZ2V0X3Rva2VuIC0tPnwlUEFSRU5UX1ZBTFVFJSBjb250YWlucyB0aGUgcmVzcG9uc2V8cmVxdWVzdF9nZXRfaWQgJiByZXF1ZXN0X2NyZWF0ZV90aWNrZXQgJiByZXF1ZXN0X2NoYW5nZV9hdXRob3JcbiAgIHJlcXVlc3RfZ2V0X2lkIC0tPnwlUFJFVklPVVMlIGNvbnRhaW5zIHRoZSByZXNwb25zZXxyZXF1ZXN0X2NyZWF0ZV90aWNrZXRcbiAgIHJlcXVlc3RfY3JlYXRlX3RpY2tldCAtLT58JVBSRVZJT1VTJSBjb250YWlucyBmb3IgZXhlbXBsZSB0aGUgaWQgb2YgdGhlIG5ld2x5IHRpY2tldHxyZXF1ZXN0X2NoYW5nZV9hdXRob3Jcblx0XHQiLCJtZXJtYWlkIjp7InRoZW1lIjoiZGVmYXVsdCJ9LCJ1cGRhdGVFZGl0b3IiOmZhbHNlfQ)](https://mermaid-js.github.io/mermaid-live-editor/#/edit/eyJjb2RlIjoiZ3JhcGggTFJcbiAgIHJlcXVlc3RfZ2V0X3Rva2VuIC0tPnwlUEFSRU5UX1ZBTFVFJSBjb250YWlucyB0aGUgcmVzcG9uc2V8cmVxdWVzdF9nZXRfaWQgJiByZXF1ZXN0X2NyZWF0ZV90aWNrZXQgJiByZXF1ZXN0X2NoYW5nZV9hdXRob3JcbiAgIHJlcXVlc3RfZ2V0X2lkIC0tPnwlUFJFVklPVVMlIGNvbnRhaW5zIHRoZSByZXNwb25zZXxyZXF1ZXN0X2NyZWF0ZV90aWNrZXRcbiAgIHJlcXVlc3RfY3JlYXRlX3RpY2tldCAtLT58JVBSRVZJT1VTJSBjb250YWlucyBmb3IgZXhlbXBsZSB0aGUgaWQgb2YgdGhlIG5ld2x5IHRpY2tldHxyZXF1ZXN0X2NoYW5nZV9hdXRob3Jcblx0XHQiLCJtZXJtYWlkIjp7InRoZW1lIjoiZGVmYXVsdCJ9LCJ1cGRhdGVFZGl0b3IiOmZhbHNlfQ)
		



Author Information
------------------

* **Jérémy HOARAU** - <jeremy.hoarau@axians.com> - [EyesOfNetwork Community](https://github.com/eyesofnetworkcommunity)
