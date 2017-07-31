(function($){
	$.fn.navbar = function(options) {
		var defaults = $.extend({
			"fontFamily"		: "arial, helvetica, sans-serif",
			"fontWeight"		: "bold",
			"fontSize" 			: "1em",
			"letterSpacing"		: "normal",
			"bgColor" 			: "#000000",
			"color" 			: "#ffffff",
			"hoverBgColor" 		: "#cccccc",
			"border"			: "0px solid black",
			"hoverBorderBottom"	: "0px solid white",
			"hoverColor" 		: "#000000",
			"borderRadius"		: "0em",
			"linkWidth" 		: "125px",
			"padding"			: '.9em'
		}, options);
			
		return this.each(function() {
			var items = $(this).find("li a");
			var o = defaults;

			items.css("font-family", o.fontFamily)
				 .css("font-weight", o.fontWeight)
				 .css("font-size", o.fontSize)
				 .css("letter-spacing", o.letterSpacing)
				 .css("text-decoration", "none")
				 .css("display", "inline")
				 .css("border", o.border)
				 .css("background-color", o.bgColor)
				 .css("color", o.color)
				 .css("width", o.linkWidth)
				 .css("padding", o.padding);

			items.mouseover(function() {
            	$(this).css("background-color", o.hoverBgColor)
            		   	.css("border-bottom", o.hoverBorderBottom)
					   	.css("color", o.hoverColor);
            });
				
			items.mouseout(function() {
            	$(this).css("background-color", o.bgColor)
            			.css("border", o.border)
					   	.css("color", o.color);
			});

			//NOTE THAT this border radius is a border for the entire list, NOT each individual link
			$(this).find("li:first-child a").css("border-top-left-radius", o.borderRadius)
				.css("border-bottom-left-radius", o.borderRadius);
			$(this).find("li:last-child a").css("border-top-right-radius", o.borderRadius)
				.css("border-bottom-right-radius", o.borderRadius);
		});
		
	};
})(jQuery);