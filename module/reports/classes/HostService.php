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

class HostService {

    public static function getHosts()
    {
        global $eon_api_token;
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, "https://localhost/eonapi/listNagiosObjects?username=admin&apiKey=" . $eon_api_token);
            curl_setopt($ch, CURLOPT_POST, true);
            $params = '{
                "object" : "hosts",
                "columns" : ["name", "services", "custom_variables"]
            }';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                curl_close($ch);
                return curl_error($ch);
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == intval(200)){
                curl_close($ch);
                return json_decode($response, true)["result"]["default"];
            }
            else{
                curl_close($ch);
                return "Error : " . $http_code;
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function getHost($name)
    {
        global $eon_api_token;
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, "https://localhost/eonapi/listNagiosObjects?username=admin&apiKey=" . $eon_api_token);
            curl_setopt($ch, CURLOPT_POST, true);
            $params = '{
                "object" : "hosts",
                "columns" : ["name", "services", "custom_variables"],
        		"filters": ["name = ' . $name . '"]
            }';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                curl_close($ch);
                return curl_error($ch);
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == intval(200)){
                curl_close($ch);
                return json_decode($response, true)["result"]["default"];
            }
            else{
                curl_close($ch);
                return "Error : " . $http_code;
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public static function getServices()
    {
        global $eon_api_token;
        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, "https://localhost/eonapi/listNagiosObjects?username=admin&apiKey=" . $eon_api_token);
            curl_setopt($ch, CURLOPT_POST, true);
            $params = '{
                "object" : "services",
                "columns" : ["host_name", "description", "perf_data"],
                "filters": ["perf_data != "]
            }';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $responses = curl_exec($ch);
            
            if (curl_errno($ch)) {
                curl_close($ch);
                return curl_error($ch);
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($http_code == intval(200)){
                curl_close($ch);
                $listServices = array();
                
                $responses = json_decode($responses, true)["result"]["default"];
                return $responses;
            }
            else{
                curl_close($ch);
                return "Error : " . $http_code;
            }
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
