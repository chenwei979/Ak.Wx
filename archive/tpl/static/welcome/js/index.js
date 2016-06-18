
// JavaScript Document

$(function(){
	var h=$(window).height();
	$(".popup_bud").css({"height":h*0.8-40, });
	//$(".popup_pro").css("height",h*0.8-260);
	$(".active_list li").click(function(){
       $(this).addClass("active").siblings().removeClass("active");
	   
		})
	
     $(".search_input input").click(function(){
		 $(this).val("");
	  })
     
	 
	})
	
 