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

if (window !=top ) {top.location=window.location;}
	
$(function() {
	$(window).bind("load resize", function() {
		topOffset = 51;
		leftOffset = 200;
		width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
		if (width < 768) {
			topOffset = 100; // 2-row-menu
			leftOffset=0
		} 
		
		height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
		height = height - topOffset;
		if (height < 1) height = 1;
		$(".iframe").css("top",topOffset);
		$(".iframe").css("left",leftOffset);
		$(".iframe").height(height-$(".footer").height());
		$(".iframe").width(width-leftOffset);
		$(".iframe").css("display","block");
	});
});
