/**
 * Created by tanytree on 2015/9/22.
 */

//头部滚动固定
$(function(){
  var ah= $(".header").height();
    $(".headerWrap").height(ah);
    $(".case_list li").click(function(){
        var url=$(this).attr("url");
        var index=$(".case_list li").index(this);
        window.location=url+"&index="+index;
    })    
});
 
$(function(){ 
    var t = j=0;
    var imgLi=$(".flashBox ul li").length;
    $(".flashBox ul li:not(:first-child)").hide();
    t = setInterval(show, 4000);
    $(".flashBox").hover(
        function(){clearInterval(t);
            $(".bannerBtn a").fadeIn();
        },
        function(){t = setInterval(show, 4000);$(".bannerBtn a").hide();
        }
    );
    $(".bannerBtn a.prev").click(function(){
        shows();
    });
    $(".bannerBtn a.next").click(function(){
        show();
    });
    function show()
    {
        j = j >=(imgLi - 1) ? 0 : ++j;
        $(".flashBox ul li").filter(":visible").fadeOut(600).parent().children().eq(j).fadeIn(1000);
    }
    function shows()
    {
        j = j >=(imgLi + 1) ? 0 : --j;
        $(".flashBox ul li").filter(":visible").fadeOut(600).parent().children().eq(j).fadeIn(1000);
    }
});


$(function(){
    myFun.lastLimr(".upgradePart");

    $(".upgradePart li").hover(function(){
        $(this).find('.erw').stop().slideDown();
    },function(){
        $(this).find('.erw').slideUp();
    });

    $(".news .column").each(function(){
       $(this).find("li").eq(0).addClass('hover');
    });
    $(".news .column .bd ul li").hover(function(){
       $(this).addClass('hover').siblings().removeClass('hover');
    });

    $(".rightBar .barClosed").on("click",function(){
        $(".rightBar").fadeOut(300);
        $(".barToggle").fadeIn();
    });
    $(".barToggle").click(function(){
        $(".rightBar").fadeIn(300);
        $(this).fadeOut(300);
    });
});

//注册页面的下拉效果
$(function(){
    $(".selectPut>input[type=text]").click(function(){
        $(this).next().show();
        return false;
    });
    $(".selectPut .selectDrop li").each(function(){
        $(this).click(function(){
            var text=$(this).text();
            $(".selectPut>input[type=text]").val(text);
            $(this).parent().slideUp();
            return false;
        });
    });
});



$(function(){
    //导航点击
    $("#funNav li").on('click',function(){
        $(this).addClass('on').siblings().removeClass('on');
        var index_d  = $("#funNav li").index(this);
        var zhu_id = $("#funNav li").eq(index_d).attr("zhu_id");//功能分类ID
        $.ajax({
            type:"post",
            url:"?g=Home&m=Index&a=ajax_news",
            datatype:"json",
            data:{
                zhu_id:zhu_id
            },
            success:function(sta){
                var obj=JSON.parse(sta);
                if(obj.img_style != null){
                    $(".img_style").html(obj.img_style);                      
                }else{
                    $(".imgScroll").hide();
                    $(".img_none").show();                    
                }
                $("#tianchong").css({
                    "margin-left":"0",
                })                
                if(obj.li != null){
                    $("#tianchong").html(obj.li);
                    funScroll();
                    phoneInScroll();                   
                }else{
                    $("#tianchong").html('&nbsp;');
                    $(".title_zw").html('&nbsp;');                
                    $(".desc").html('&nbsp;');
                    $(".app_sight").html('&nbsp;');                    
                    $(".qrCode").html('&nbsp;');  
                }
            }
        })
    }).eq(0).trigger('click');
    anli();
});

//首页产品介绍的图片滚动和按钮点击滚动
function funScroll(){
    var i=0;
    var r=0;
    var phoneLI = $(".imgHolder .imgScroll ul>li");
    var phoneLiLen = phoneLI.length;
    var phoneLiW = phoneLI.width();
    var imgSroll = $(".imgHolder .imgScroll ul");
    imgSroll.width(phoneLiLen*phoneLiW);
    var btn = $(".minNav ul li");
    var btnLen=btn.length;
    var scrollUl=$(".minNav ul")
    var btnW=btn.outerWidth(true);
    var btnLen5=Math.ceil(btn.length/4);
    var btnLen5w=btnW*4;
    var prevBtn=$('.minNav .scrollBtn a.prev');
    var nextBtn=$('.minNav .scrollBtn a.next');
    $(".minNav .scrollBox ul").width(btnLen*btnW);

    btn.each(function(index) {
        $(this).live('click',function() {
            i = index;
            var key_id=btn.eq(index).attr("key");
            $.ajax({
                type:"post",
                url:"?g=Home&m=Index&a=ajax_dan",//功能分类下面的单个子功能填充
                datatype:"json",
                data:{
                    key_id:key_id
                },
                success:function(sta){
                    var obj=JSON.parse(sta);
                    var img_url=$(".img_id").eq(index).attr("img_url");
                    $(".title_zw").html(obj.title);                
                    $(".desc").html(obj.desc);
                    obj.sce_content==0? $(".app_sight").html('&nbsp;') : $(".app_sight").html('<strong>应用场景：</strong><span class="sce_content">'+obj.sce_content+'</span>');                    
                    obj.timg==0 ? $(".qrCode").html('&nbsp;') : $(".qrCode").html('<div class="redCode"><img src="'+obj.timg+'" alt="qrCode"></div><em>扫一扫关注体验</em>');
                    if(img_url==0){
                        $(".imgScroll").hide();
                        $(".img_none").show();
                    }else{
                        $(".imgScroll").show();
                        $(".img_none").hide();
                    }
                }
            })
            run();
            $(".imgHolder .imgScroll ul>li").eq(i).find(".scrollPage i").eq(0).trigger("click");

        });
    }).eq(0).trigger("click");

    prevBtn.bind('click',function(){
        prev();
        btnScroll();
    });
    nextBtn.bind('click',function(){
        next();
        btnScroll();
    });
    function next() {
        r++;
        if (r == btnLen5) {
            r = 0
        }
    }
    function prev() {
        r--;
        if (r < 0) {
            r = btnLen5 - 1
        }
    }
    function btnScroll(){
        scrollUl.stop().animate({
                'margin-left': -btnLen5w * r + 'px'
            },
            1000);
    }
    function run() {
        btn.eq(i).addClass('on').siblings().removeClass('on');
        imgSroll.stop().animate({ //图片切换
                'left': -phoneLiW * i + 'px'
            },
            1000);
    };
}
//首页手机产品滚动的内部滚动效果
function phoneInScroll(){
    $(".insideScroll").each(function(){
        var i=0;
        var timer=0;
        var prev=$(this).find(".scrollBtn a.prev");
        var next=$(this).find(".scrollBtn a.next");
        var pageI=$(this).find(".scrollPage i");
        var imgLi=$(this).find("ol li");
        function right() {
            i++;
            if (i == imgLi.length) {
                i = 0
            }
        }
        function left() {
            i--;
            if (i < 0) {
                i = imgLi.length - 1
            }
        }
        function run(){
            pageI.eq(i).addClass("on").siblings().removeClass("on");
            imgLi.eq(i).fadeIn(1000).siblings().fadeOut(1000).hide();
        }
        pageI.each(function(index){
            $(this).click(function(){
                i=index;
                run();
            });
        }).eq(0).trigger("click");
        function runn(){
            right();
            run();
        }
        timer= setInterval(runn, 5000);
        $(this).hover(function(){
            clearInterval(timer);
            $(".insideScroll .scrollBtn a").fadeIn(1000);
        },function(){
            timer = setInterval(runn, 5000);
            $(".insideScroll .scrollBtn a").fadeOut(1000);
        });
        prev.click(function(){
            left();
            run();
        });
        next.click(function(){
            right();
            run();
        });
    })
}


//案例页面滚动
function anli(){
    var timer= 0;
    var i=0;
    var caseLi=$(".caseList ul>li");
    var caseLen=caseLi.length;

    var dot=$(".dotNav ol li");
    $(".caseList ul>li:not(:first-child)").hide();

    if(caseLen==1){
       return;
    };

    dot.each(function(index){
        $(this).live('click',function(){
            i=index;
            move();
        })
    });
    timer = setInterval(startMove, 4000);
    $(".caseList").hover(function(){clearInterval(timer);
        }, function(){timer = setInterval(startMove, 4000);}
    );
    $(".caseBtn a.prev").click(function(){
        prev();
        move();
    });
    $(".caseBtn a.next").click(function(){
        next();
        move();
    });
    function prev(){
        i--;
        if (i < 0) {
            i = caseLen - 1
        }
    }
    function next(){
        i++;
        if (i == caseLen) {
            i = 0
        }
    }
    function move(){
        $(".caseList ul li").filter(":visible").fadeOut(600).parent().children().eq(i).fadeIn(1000);
       dot.removeClass("on").eq(i).addClass("on");
    }
    function startMove(){
        next();
        move();
    }
};

$(function(){
    var i=0;
    var t=null;
    var sLi=$(".whyPig .whyScrollUl li");
    var tLi=$(".whyText ul li");
    var sLiLen=sLi.length;
    var tLiLen=tLi.length;
    var sLiW=sLi.width();
    var tLiW=tLi.width();

    var sUl=$(".whyPig .whyScrollUl");
    var tUl=$(".whyText ul");

    sUl.width(sLiLen*sLiW);
    tUl.width(tLiLen*tLiW);
    t = setInterval(startMove, 4000);
    $(".whyPig .w1000").hover(function(){clearInterval(t);
        }, function(){t = setInterval(startMove, 4000);}
    );
    $(".whyPig .whyBtn a.prev").click(function(){
        prev();
        move();
    });
    $(".whyPig .whyBtn a.next").click(function(){
        next();
        move();
    });
    function prev(){
        i--;
        if (i < 0) {
            i = sLiLen - 1
        }
    }
    function next(){
        i++;
        if (i == sLiLen) {
            i = 0
        }
    }
    function move(){
        sUl.stop().animate({ //图片切换
                'margin-left': -sLiW * i + 'px'
            },
            1000);
        tUl.stop().animate({ //图片切换
                'margin-left': -tLiW * i + 'px'
            },
            300);
    }
    function startMove(){
        next();
        move();
    }
});


var myFun = {
    //计算每行最后一个，清除每行最后一个的margin
    rowlastLi: function(a, b) {
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
    },
    //tab切换一个参数
    tab: function(obj) {
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
    },
    //tab切换三个参数
    tabs: function(a, b, c) {
        var len = $(a);
        len.bind("click", function() {
            var index = 0;
            $(this).addClass(c).siblings().removeClass(c);
            index = len.index(this);
            $(b).eq(index).addClass("animate").show().siblings().removeClass("animate").hide();
            return false;
        }).eq(0).trigger("click");
    },
    //清楚最后一个li的border
    lastLi: function(a) {
        $(a).find("li").last().css('borderBottom', '0');
    },
    //清楚最后一个li的margin-right
    lastLimr: function(a) {
        $(a).find("li").last().css('marginRight', '0');
    },

//设置相对屏幕（不是整个文档的）的top值
    marginTop: function(a) {
        var wHeight = $(window).height();
        var boxHeight = $(a).height();
        //var scrollTop = $(window).scrollTop();
        var top = (wHeight - boxHeight) / 2;
        $(a).css('marginTop', top);
    }    

};
