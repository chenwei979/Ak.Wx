/**
 * Created by tanytree on 2015/09/16.
 */
//判断是否ie7
$(function() {
    if (navigator.appName == "Microsoft Internet Explorer" && ((navigator.appVersion.match(/7./i)=="7.") || (navigator.appVersion.match(/8./i)=="8.")) ){
        $(".fullBg,.iePrompt ").show()
        $(".iePrompt_main .close,.i_know").click(function() {
            $(".iePrompt,.fullBg").hide();
        });
    }
});
function getConnect() {
	$.ajax({
		url : $('param.params').attr('url-connect'),
		data : {id : $('param.params').attr('id')},
		type : 'POST',
		success : function (data) {
			$('.messageTotal').html(data.count);
			$('.totalname').html(data.totalname);
			setTimeout("getConnect()", 5000);
		}
	});
}



$(function() {
    // 初始化气泡提示
    $('.tooltip').tooltipster({
        theme: 'tooltipster-shadow',
        trigger: 'hover',
        delay: 0,
        speed: 200,
    });

    //头部文字切换
    $(".wordList li").soChange({
        slideTime: 500,
        changeTime: 5000
    });

    $('.wordScroll').kxbdSuperMarquee({
        isMarquee: true,
        isEqual: false,
        scrollDelay: 40,
        direction: 'left',
    });

	//跑得快头部文字切换
    /*var yyText=$('.param').attr('title-chage');
	yyText=yyText?yyText:'|';
    var yyTextArr = new Array();
    yyTextArr = yyText.split("|");
	var yyTextHtml='';
    for(var i=0;i<yyTextArr.length;i++){
        yyTextHtml=yyTextHtml+'<li style="display:none">'+yyTextArr[i]+'</li>';
    }
	$(".paodaoBigText ul").html(yyTextHtml);*/
	
	$(".paodaoBigTextUl li").soChange({
		slideTime: 200,
		changeTime: 2000
	});
    //跑得快头部文字切换结束

    //红包按钮点击
    $(".starBtn a,.starBtn2").bind('click', function() {
    	$('aside.aside').hide();
        marginTop(".countdown .bigText");
        $(window).resize(function() {
            marginTop(".countdown .bigText");
        });
        $("body").css('overflow', 'hidden');

        //倒计时
        var wait = 5,
            t = null;
        if ($('.param').attr('nowait')) {
        	wait = 0;
        }
        var oUrl = $('.param').attr('url-location');
        $('.countdown .bigText em').text(wait);
        $.ajax({
    		url	 : $('.param').attr('url-start'),
    		type : 'POST',
    		dataType : 'JSON',
    		success : function (data) {
				if(data.error == 0){
					if (!$('.param').attr('nowait')) {
						$(".fullBg").show();
						$(".countdown").show();
					}
					timeOut();
				}else if(data.error == 1){
					//location.href = oUrl;
				}
    		}
        });

        function timeOut() {
            if (wait == 0) {
                $('.countdown').fadeOut(300);
                $('.fullBg').fadeOut(300);
                clearTimeout(t);
                //window.open(oUrl);
                location.href = oUrl;
            } else {
                t = setTimeout(function() {
                    wait--;
                    $('.countdown .bigText em').text(wait);
                    timeOut();
                }, 1000);

            }
        }
    });


    tab(".con-inner"); //互动平台切换
    rowlastLi(".con-inner .bd .userList", 3); //获奖用户排列每行第三个处理


    //$(".userWord .scrollBox li").live("click",function(){
    //    $('.messageDetailBox').slideDown(300);
    //    $(".userWord ").css('visibility','hidden');
    //    scrollItem(".messageDetailBox","horizontal",1);
    //});
});
//计算每行最后一个，清除每行最后一个的margin
     function rowlastLi(a, b) {
        $(a).each(function() {
            var li = $(this).find("ul>li");
            var len = $(this).find("ul>li").length;
            var y = len / b;
            for (var i = 1; i <= y; i++) {
                li.eq(i * b - 1).css({
                    'margin-right': '0'
                });
            }
        })
    }
    //tab切换一个参数
     function tab(obj) {
        var tabObj = $(obj);
        tabObj.each(function() {
            var len = tabObj.find('.hd ul li');
            var row = tabObj.find('.bd .row');
            len.bind("click", function() {
                var index = 0;
                $(this).addClass('on').siblings().removeClass('on');
                index = len.index(this);
                row.eq(index).show().siblings().hide();
                return false;
            }).eq(0).trigger("click");
        });
    }
    //tab切换三个参数
     function tabs(a, b, c) {
        var len = $(a);
        len.bind("click", function() {
            var index = 0;
            $(this).addClass(c).siblings().removeClass(c);
            index = len.index(this);
            $(b).eq(index).addClass("animate").show().siblings().removeClass("animate").hide();
            return false;
        }).eq(0).trigger("click");
    }
    //清楚最后一个li的border
    function lastLi(a) {
        $(a).find("li").last().css('borderBottom', '0');
    }

    //设置相对屏幕（不是整个文档的）的top值
     function marginTop(a) {
        var wHeight = $(window).height();
        var boxHeight = $(a).height();
        //var scrollTop = $(window).scrollTop();
        var top = (wHeight - boxHeight) / 2;
        $(a).css('marginTop', top);
    }

var setWindow = {
    //1.居中方法，传入需要剧中的标签
    center: function(a) {
        var wWidth = $(window).width();
        var wHeight = $(window).height();
        var boxWidth = $(a).width();
        var boxHeight = $(a).height();
        var scrollTop = $(window).scrollTop();
        var scrollLeft = $(window).scrollLeft();
        var top = scrollTop + (wHeight - boxHeight) / 2;
        var left = scrollLeft + (wWidth - boxWidth) / 2;
        $(a).css({"top": top, "left": left});
    },
    //2.将盒子方法放入这个方，方便法统一调用
    centerWindow: function(a) {
        setWindow.center(a);
        //自适应窗口
        $(window).bind('scroll resize', function() {
            setWindow.center(a);
        });
    },
    //3.点击弹窗方法
    clickaShowWindow: function(a, b) {
        $(b).click(function() {
            setWindow.centerWindow(a);
            $(".fullBg").show();
            $(a).slideDown(300);
            return false;
        });
    },
    xClosed:function(){
        $(".fullBg").hide();
        $(".window").hide();
        $(".flagPosition").removeClass("hidden");
        $(".userWord ").css('visibility','visible');
    },
    closedWindow:function(){
        var timer=null;
    timer=setTimeout(function(){
        $(".fullBg").hide();$(".window").hide();
    },4000);
},
    windowClosed:function(){
        $(".fullBg").hide();
        $(".window").hide();
    }
};

$(function(){
    //倒计时
    var wait = $('param.param').attr('countdown-time'),
        t = null;
	if(wait < 1){
		messageData();
	}
    $('.timeCount i').text(wait);
    $(".con-inner .hd ul").css('margin-left','-100px');
    timeOut();
    function timeOut() {
        if (wait == 0) {
            clearTimeout(t);
            $(".timeCount").remove();
            $(".con-inner .hd ul").css('margin-left','0');
			$(".con-inner .hd li").eq(1).click();
			//
        } else {
            t = setTimeout(function() {
                wait--;
                $('.timeCount i').text(wait);
                timeOut();
            }, 1000);

        }
    }
    
    //扫二维码弹窗
    $(".showIntroBtn").click(function(){
        setWindow.centerWindow('.qrCodeWindow');
        $(".fullBg").show();
        $(".qrCodeWindow").show();
    });
});

function join_count( url ){

        $.ajax({
           type: "POST",
           url: url,
		   dataType:'json',
           data: "",
           success: function(data){
            if(data != ''){
				$('.messageTotal').text(data.count);
				$('.totalname').text(data.totalname);
			};
           }
        });


}




















$(window).resize(function(){
   $('body').height($(window).height());
});

//全屏方法表达式
(function() {
    var viewFullScreen = document.getElementById("view-fullscreen");
    if (viewFullScreen) {
        viewFullScreen.addEventListener("click", function() {
            fullScreen();
        }, false);
    }
})();
(function() {
    var viewFullScreen = document.getElementById("view-fullscreen1");
    if (viewFullScreen) {
        viewFullScreen.addEventListener("click", function() {
            fullScreen();
        }, false);
    }
})();


function fullScreen(){
    var docElm = document.documentElement;
    if (docElm.requestFullscreen) {
        docElm.requestFullscreen();
    } else if (docElm.msRequestFullscreen) {
        docElm.msRequestFullscreen();
    } else if (docElm.mozRequestFullScreen) {
        docElm.mozRequestFullScreen();
    } else if (docElm.webkitRequestFullScreen) {
        docElm.webkitRequestFullScreen();
    }
}

$(window).resize(function(){
   $('body').height($(window).height());
});