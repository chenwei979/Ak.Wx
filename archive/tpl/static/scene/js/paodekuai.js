/**
 * Created by tanytree on 2015/10/16.
 */


//窗口处理
$(function(){
    timeOut_run();
    tab(".paopaoAll");//切换

    $('.paopaoWin .btn a.check').on('click',function(){
        //wClosed();
        paopaoAll();
    	$.ajax({
    		url	 : $('.param').attr('url-all'),
    		type : 'POST',
    		success : function (data) {
    	        $('.paopaoAll .ajax').html(data);
    	        tab(".paopaoAll");//切换
    	        paopaoAll();
				$(".paopaoAll .hd ul li").last().click();
    		}
    	});
    });
    $('.paopaoWin .btn a.again').on('click',function(){
        $.ajax({
    		url	 : $('.param').attr('url-start'),
    		type : 'POST',
    		dataType : 'JSON',
    		success : function (data) {
    			if(data.error == 0) {
    				//timeOut_run();
    	        	$(".window").fadeOut();
    	        	window.setTimeout(timeOut_run,1500);
    			}
    		}
        });
        //location.href = $('.param').attr('url-location');
    })
});
function wClosed(){
    $(".fullBg").fadeOut();
    $(".window").fadeOut();
}
function wClosedAll(){
    $(".paopaoAll").fadeOut();
}

//跑跑显示另一个头部底部
function showHdAndFt(){
    $(".header").css('visibility','hidden');
    $(".footer").hide();
    $(".paodaoBigText").css('visibility','visible');
    $(".paodaoFtText").show();
}
//跑跑结束后还原头部底部
function hideHdAndFt(){
    $(".header").css('visibility','visible');
    $(".footer").show();
    $(".paodaoBigText").css('visibility','hidden');
    $(".paodaoFtText").hide();
}

//倒计时
function timeOut_run(){
    $(".fullBg").show();
    $(".countdown").show();
    marginTop(".countdown .bigText");
    $(window).resize(function() {
        marginTop(".countdown .bigText");
    });

    //倒计时
    var wait = $('.param').attr('start-time'),
        t = null;
    $('.countdown .bigText em').text(wait);
    timeOut();

    function timeOut() {
        if (wait == 0) {
            $('.countdown').fadeOut(300);
            $('.fullBg').fadeOut(300);
            showHdAndFt();
            gameRun();
            clearTimeout(t);
        } else {
            t = setTimeout(function() {
                wait--;
                $('.countdown .bigText em').text(wait);
                timeOut();
            }, 1000);

        }
    }

}


/*
var run_players = ([

    {
        "id": "0",
        "mLeft": "100",
        "name": "王尼玛",
        "avatar": "http:\/\/www.wookmark.com\/images\/thumbs\/540921_wookmark.jpg"
    },{
        "id": "1",
        "mLeft": "30",
        "name": "李连杰",
        "avatar": "http://www.wookmark.com/images/thumbs/541775_wookmark.jpg"
    },{
        "id": "2",
        "mLeft": "400",
        "name": "张国荣",
        "avatar": "http://www.wookmark.com/images/thumbs/541205_wookmark.jpg"
    },{
        "id": "3",
        "mLeft": "150",
        "name": "王美丽",
        "avatar": "http://www.wookmark.com/images/thumbs/541521_wookmark.jpg"
    },{
        "id": "4",
        "mLeft": "70",
        "name": "梅长苏",
        "avatar": "http://www.wookmark.com/images/thumbs/541770_wookmark.jpg"
    },{
        "id": "5",
        "mLeft": "90",
        "name": "梅长苏",
        "avatar": "http://www.wookmark.com/images/thumbs/541770_wookmark.jpg"
    },{
        "id": "6",
        "mLeft": "180",
        "name": "梅长苏",
        "avatar": "http://www.wookmark.com/images/thumbs/541770_wookmark.jpg"
    },{
        "id": "7",
        "mLeft": "60",
        "name": "梅长苏",
        "avatar": "http://www.wookmark.com/images/thumbs/541770_wookmark.jpg"
    }

]);
var run_players = ([
]);
*/
var shakeWin = {};
function gameRun(){
	$.ajax({
		url	 : $('.param').attr('url-run'),
		//data : {pid : pid},
		type : 'POST',
		dataType : 'JSON',
		success : function (data) {
		    if(data.res && data.status == 0){
		    	shakeWin = data.res;
		        x = window.setTimeout(gameRun,1000);
		        var temp 	= '';
		        for (var i = data.res.length - 1; i >= 0; i--) {
		            temp += '<div class="player" id='+data.res[i].id+' style="top:'+i*67+'px;left:'+data.res[i].mLeft+'"><h2>'+data.res[i].name+'</h2> <img src='+data.res[i].portrait+ '></div>';
		        }
		        $(".paodao .players").html(temp);
		    } else {
	    		hideHdAndFt();
	        	paopaoWin();
		    }
		}
	});
}
//跑跑排名结果
function paopaoWin(){
    window.setTimeout(function() {
        setWindow.centerWindow(".paopaoWin");
        $(".fullBg").show();
        if (shakeWin[0] != undefined) {
        	$(".paopaoWin .first h2").html(shakeWin[0].name);
        	$(".paopaoWin .first img").attr('src', shakeWin[0].portrait);
        }
        if (shakeWin[1] != undefined) {
        	$(".paopaoWin .second h2").html(shakeWin[1].name);
        	$(".paopaoWin .second img").attr('src', shakeWin[1].portrait);
        }
        if (shakeWin[2] != undefined) {
            $(".paopaoWin .third h2").html(shakeWin[2].name);
            $(".paopaoWin .third img").attr('src', shakeWin[2].portrait);
        }
        $(".paopaoWin").show();
    },1000);
}

//跑跑排名名单，带全部名次窗口
function paopaoAll(){
    setWindow.centerWindow(".paopaoAll");
    $(".fullBg").show();
    $(".paopaoAll").show();
}

























