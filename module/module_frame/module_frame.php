<?php
/*
#########################################
#
# Copyright (C) 2017 EyesOfNetwork Team
# DEV NAME : Jean-Philippe LEVY
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

$url=retrieve_form_data("url",null);

?>

<script>
if (window !=top ) {top.location=window.location;}

var navbarwidth = 0;
$(document).ready(function(){
	navbarwidth = $(".navbar-default.sidebar").width();
});

$(window).on('load',function() {
	$('#page-wrapper').empty();
	$('<iframe>', {
		src: '<?php echo urldecode($url); ?>',
		id:  'iframe',
		class: 'iframe',
		frameborder: 0,
	}).appendTo('#page-wrapper');
});

$(window).bind("load resize", function() {
	topOffset = $(".navbar-static-top").height() + 1;
	leftOffset = $(".navbar-default.sidebar").width();
	width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
	if (width <= 768) {
		height = $("body")[0].scrollHeight;
		leftOffset=0
	} else { 
		height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
	}
	height = height - topOffset;
	if (height < 1) height = 1;
	$(".iframe").css("top",topOffset);
	$(".iframe").css("left",leftOffset);
	$("#page-wrapper").innerHeight(height-$(".footer").height());
	$(".iframe").height(height-$(".footer").height());
	$(".iframe").width(width-leftOffset);
	$(".iframe").show();
});

$("#menu-toggle").on("click", function() {
	leftOffset = 5;
	if($(".navbar-default.sidebar").width()!=navbarwidth) {
		leftOffset = leftOffset + navbarwidth;
	}
	width = (window.innerWidth > 0) ? window.innerWidth : screen.width;
	$(".iframe").css("left",leftOffset);
	$(".iframe").width(width-leftOffset);
});

</script>
