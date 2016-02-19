/*
#########################################
#
# Copyright (C) 2014 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
# VERSION 4.2
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

$(document).ready(function() {
        $("#datepicker").daterangepicker({
                earliestDate: 'today',
                dateFormat: 'dd/mm/yy',
                datepickerOptions: {
                        changeMonth: true,
                        changeYear: true
                }
        });
        $("table")
        .tablesorter({widthFixed: false,widgets: ['zebra']})
        .tablesorterPager({container: $("#pager")});
        $("#loading").hide();
        $("#showtable").show();
});

function submitFormAjax()
{
        txt = $("form").serialize();

        $('#gedtable').ajaxStart(function(){
                $('#gedtable *').empty();
                $('#loading').show();
        });

        $.ajax({
                type: "POST",
                url: "logs_actions.php",
                data: txt,
                success: function(res){
                        $('#gedtable').html(res);
                }
       }
        );

        $('#gedtable').ajaxStop(function(){
                $('#loading').hide();
                $("#showtable").show();
        });

        return false;
}
