$(document).ready(function(){$(".analytics-tabs").each(function(e,t){$(".analytics-nav a",t).each(function(e,n){$(n).click(function(){$(".analytics-nav a",t).removeClass("active");$(this).addClass("active");$(".analyticsTab",t).addClass("hidden");$($(".analyticsTab",t).get(e)).removeClass("hidden");$("body").trigger("redraw");return!1})});$(".analytics-nav li:first-child a",t).trigger("click")})});