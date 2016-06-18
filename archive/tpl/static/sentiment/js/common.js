/**
 * Created by tanytree on 2015/7/15.
 */
var timer=0;
$(function(){
    var docHeight = $(document).height();
    $(".fullBg").height(docHeight);
    tab(".tabList .hd .title",".tabList .bd .row","on");
    $(window).scroll(function() {
        if ($(window).scrollTop() > 200) {
            $(".backToTop").fadeIn(0);
        }
        else {
            $(".backToTop").fadeOut(500);
        }
    });
    $(".backToTop").click(function() {
        $('body,html').animate({
                scrollTop: 0
            },
            500);
        return false;
    });
    centerWindow(".w1");
    centerTop(".w0");
    $(".fullBg,.window .oClosed").click(function(){
        $(".window").removeClass("animate").hide();
        $(".fullBg").hide();
        clearTimeout(timer);
    });
    timeShow();//倒计时
    point(69);//首页红色进度条
    lastLi(".actPart2 .priceList");//处理最后的边框
    lastLi(".userLsit");//处理最后的边框
});

function point(a){
    var point=$(".range .rPoint");
    point.css('width','0%');
    point.animate({width:a+'%'},2000)
}

function lastLi(a){
    $(a).find("li").last().css('borderBottom','0');
}
function btnClick(){
    $(".fullBg").show();
    $(".w1").addClass("animate").show();
    closedWindow();
}

function showWindow(){
    $(".fullBg").show();
    $(".w0").addClass("animate").show();
}
function closedWindow(){
    timer=setTimeout(function(){
        $(".fullBg").hide();$(".window").removeClass("animate").hide();;
    },4000);
}

function tab(a,b,c){
    var len=$(a);
    len.bind("click",function(){
        var index = 0;
        $(this).addClass(c).siblings().removeClass(c);
        index = len.index(this);
        $(b).eq(index).addClass("animate").show().siblings().removeClass("animate").hide();
        return false;
    }).eq(0).trigger("click");
}
//2.将盒子方法放入这个方，方便法统一调用
function centerWindow(a) {
    center(a);
    //自适应窗口
    $(window).bind('scroll resize',
        function() {
            center(a);
        });
}

//1.居中方法，传入需要剧中的标签
function center(a) {
    var wWidth = $(window).width();
    var wHeight = $(window).height();
    var boxWidth = $(a).width();
    var boxHeight = $(a).height();
    var scrollTop = $(window).scrollTop();
    var scrollLeft = $(window).scrollLeft();
    var top = scrollTop + (wHeight - boxHeight) / 2;
    var left = scrollLeft + (wWidth - boxWidth) / 2;
    $(a).css({
        "top": top,
        "left": left
    });
}
function centerTop(a) {
    var wWidth = $(window).width();
    var boxWidth = $(a).width();
    var scrollLeft = $(window).scrollLeft();
    var left = scrollLeft + (wWidth - boxWidth) / 2;
    $(a).css({
        "left": left
    });
}

function timeShow(){
    var show_time = $(".timeShow");
    endtime = new Date("10/01/2015 23:59:59");//结束时间
    today = new Date();//当前时间
    delta_T = endtime.getTime() - today.getTime();//时间间隔
    if(delta_T < 0){
        clearInterval(auto);
        alert("倒计时已经结束");
    }
    window.setTimeout(timeShow,1000);
    total_days = delta_T/(24*60*60*1000);//总天数
    total_show = Math.floor(total_days);//实际显示的天数
    total_hours = (total_days - total_show)*24;//剩余小时
    hours_show = Math.floor(total_hours);//实际显示的小时数
    total_minutes = (total_hours - hours_show)*60;//剩余的分钟数
    minutes_show = Math.floor(total_minutes);//实际显示的分钟数
    total_seconds = (total_minutes - minutes_show)*60;//剩余的分钟数
    seconds_show = Math.floor(total_seconds);//实际显示的秒数
    if(total_days<10){
        total_days="0"+total_days;
    }
    if(hours_show<10){
        hours_show="0"+hours_show;
    }
    if(minutes_show<10){
        minutes_show="0"+minutes_show;
    }
    if(seconds_show<10){
        seconds_show="0"+seconds_show;
    }
    show_time.find("li").eq(0).find("em").text(total_show);//显示在页面上
    show_time.find("li").eq(1).find("em").text(hours_show);
    show_time.find("li").eq(2).find("em").text(minutes_show);
    show_time.find("li").eq(3).find("em").text(seconds_show);
}

