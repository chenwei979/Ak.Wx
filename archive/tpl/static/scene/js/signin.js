
$(function() {
  signin_ajax(); 
});



function changeHight(){
  var beforeHeight = $(".toupiao .row").scrollTop();
  $(".toupiao .row").scrollTop($(".toupiao .row").scrollTop()+20);
  var afterHeight = $(".toupiao .row").scrollTop();
  if(beforeHeight == afterHeight){
  }else{
    setTimeout("changeHight()",5);
  }
}

function signin_add(data){//测试用
  var html=$("<li><div class='heads_bg'>" +
  		"</div><div class='user-block out'>" +
  		"<a href='javascript:void(0);'>" +
  		//"<img class='checkin-avatar checkinAvatar cki-default' src='"+ $('param.signin').attr('path') +"/scene/images/12.jpg'></a>" +
  		"<span class='checkin-name checkinName'>"+data.nickname+"</span>" +
  		"</div><div class='user-block in' > <a href='javascript:void(0);'>" +
  		"<img class='checkin-avatar checkinAvatar' src='"+data.portrait+"'></a>" +
  		"<span class='checkin-name checkinName'>"+data.nickname+"</span><i>签到</i> </div></li>");
  $(".qiandao_show").append(html);

  html.find(".user-block").show();
  html.find(".heads_bg").hide();
  html.find("i").fadeOut(1000);
  html.find(".user-block.in").css({
	    //  "animation-delay": i * 0.5 + "s",
	      "z-index": 999
  });
  setTimeout("changeHight()",5);//滚动条滚动到底部，异步请求时调用这个方法
}

function signin_ajax() {
	var id = $("param.signin").attr('pid');
	var last_id = $("param.signin").attr('last_id');
	var _this = this;
	$.ajax({
		url : $('param.signin').attr('url-ajax'),
		data : {id : id, last_id : last_id},
		type : 'POST',
		dataType : 'json',
		success : function (data) {
			if(data) {
				$("param.signin").attr('last_id', data.id);
				signin_add(data);
			}
			setTimeout("signin_ajax()", 2000);
		}
	});
}