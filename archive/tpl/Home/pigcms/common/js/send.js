// JavaScript Document
$(function(){
	$("#send").click(function(){
		var c = $(this).attr('class');
		var username = $("#username").val();
		var verifytype = $("#verifytype").val();
		if(username !=""){
			if(c == 'sure'){
				var url = '/index.php?g=Home&m=Users&a=verification'
				
				$.getJSON(url,{username:username,verifytype:verifytype},function(res){
					if(res.code ==1){
						$("#send").removeClass("sure");
						$("#send").addClass("un");
						$("#send").html("请等待<i id='seconds'>60</i>秒");
						setTimeout('seconds()',1000);
					}
					alert(res.msg);
				});
			}
		}else{
			alert("手机号码不能为空");
		}
	});

    $("#imgVerify").keyup(function(){
        var imgVerify = trimSpace($("#imgVerify").val());
        if( 4==imgVerify.length )
        {
            $.ajax({
                url:'/index.php?g=Home&m=Users&a=beVerify',
                data:{imgVerify:imgVerify},
                type:'post',
                dataType:'json',
                success:function(data)
                {
                    if( 100==data.state )
                    {
                        $("#verification").show();
                        return false;
                    }else{
                        alert("验证码错误");
                        return false;
                    }
                }
            });
        }
    });

    $("#username").keyup(function(){
        var username = trimSpace($("#username").val());
        var verifytype = trimSpace($("#verifytype").val());
        if( 11==username.length )
        {
            $.ajax({
                url:'/index.php?g=Home&m=Users&a=checkTel',
                data:{username:username},
                type:'post',
                dataType:'json',
                success:function(data)
                {
                    if( -100==data.state )
                    {
                        alert("您的手机号码不正确")
                        return false;
                    }else if( -101==data.state && 1!=verifytype ){
                        alert("您的手机号码已注册");
                        return false;
                    }
                }
            });
        }
    });
});

function seconds(){
	var s = $("#seconds").html();
	s = parseInt(s);
	s = s -1;
	if(s>0){
		$("#seconds").html(s);
		setTimeout('seconds()',1000);
	}else{
		$("#seconds").html(s);
		$("#send").removeClass("un");
		$("#send").addClass("sure");
		$("#send").html("发送验证码");
	}
	
		
}

function trimSpace(str)
{
    return str.replace(/(^\s*)|(\s*$)/g, "");
}