// $(function(){
//     //点击任意地方关闭地址选择弹窗
//     $(document).click(function(e) {
//         var $menu = $('.selectPut .selectDrop');
//         if(!(e.target == $menu[0] || $.contains($menu[0], e.target))) {
//             $menu.slideUp();
//         };
//     });
// })
//处理注册登录页面底部兼容效果
$(function(){
    ftPosition();
    $(window).resize(function(){
        ftPosition();
    });
    function ftPosition(){
        if($(window).height()>790){
            $(".footer").css({
                "position":"absolute","bottom":"0"
            })
        }else{
            $(".footer").css({
                "position":"static"
            })
        }
    }
});
function refreshImg(){
    document.getElementById("txtCheckCode").src="/index.php?g=Home&m=Index&a=verify&s="+Math.random();
}
function sendMsg(){
    var num = document.getElementById('sms_mp').value;
    var reg=/^0{0,1}1[0-9]{10}$/i;
    if( num == '' || !reg.test(num)){
        alert('请输入正确的手机号！');
        return false;
    }


   if (confirm("我们会将会发送验证码到 "+num)){
        jQuery(function($) {
            $.ajax({
                url:"index.php?g=Home&m=Users&a=sendMsg",
                type:"post",
                data:"mp="+num,
                success:function(data){
                    
                    $("#a_verify").css({"background":"#ccc","borderColor":"#ccc"});
                    fun_timedown(60);

                  }
            });
        });
    }
    return false;
}


function fun_timedown(time){
    if(time=='undefined'){
        time = 60;
    }

    $("#a_verify").html(time+"秒后可重新获取");
    
    time = time-1;
    if(time>=0){
        setTimeout("fun_timedown("+time+")",1000);
    }else{
        $("#a_verify").css({"background":"#fff","borderColor":"#007DDB"});
        $("#a_verify").html('<a href="javascript:sendMsg();" id="a_btn" >获取验证码</a>');
    }
}