///**
// * Created by tanytree on 2015/10/16.
// */

var refreshtime =2;
var len=4;
var cur=0;//当前位置
var mtime;
var data=new Array();
data[0]=new Array('0','./tpl/static/scene/images/avatar.png','系统消息','欢迎来到微信上墙，发送图片也可以上墙哦！','','weibo');
var lastid='0';

function viewOneHide(){
    oopen=switchto(oopen,'mone');
}
function viewOne(cid,t)
{
    var str=$('#li'+cid);
    var onenickname = str.find("h2").text();
    var oneword = str.find("h3").text();
    var onesrc = str.find(".avatar img").attr('src');
    var oneimgsrc = str.find(".msgImage ").attr('src');
	var wordLen=oneword.length;
    if(typeof(oneimgsrc) == 'string'){
        $("#mone").find(".msgcon").html('<img src="'+oneimgsrc+'"/>');
    }else{
        $("#mone").find(".msgcon").text(oneword);
        var wordLen=$("#mone").find(".msgcon").text().length;
        if(wordLen>100 && wordLen<200){
            $("#mone").find(".msgcon").css('font-size','38px');
            }else if(wordLen>200 && wordLen<300){
            $("#mone").find(".msgcon").css('font-size','36px');
        }else if(wordLen>300 && wordLen<400){
            $("#mone").find(".msgcon").css('font-size','30px');
        }else if(wordLen>400 && wordLen<500){
            $("#mone").find(".msgcon").css('font-size','26px');
        }else if(wordLen>500 && wordLen<600){
            $("#mone").find(".msgcon").css('font-size','24px');
        }else if(wordLen>600 && wordLen<700){
            $("#mone").find(".msgcon").css('font-size','22px');
        }else if(wordLen>700 && wordLen<800){
            $("#mone").find(".msgcon").css('font-size','20px');
        }else if(wordLen>800 && wordLen<900){
            $("#mone").find(".msgcon").css('font-size','18px');
        }else if(wordLen>900 && wordLen<1000){
            $("#mone").find(".msgcon").css('font-size','16px');
        }else if(wordLen>1000 && wordLen<1100){
            $("#mone").find(".msgcon").css('font-size','16px');
        }else if(wordLen>1100 && wordLen<1200){
            $("#mone").find(".msgcon").css('font-size','14px');
        }else if(wordLen>1200 && wordLen<1500){
            $("#mone").find(".msgcon").css('font-size','14px');
        }else if(wordLen>1500 && wordLen<3000){
            $("#mone").find(".msgcon").css('font-size','12px');
            }
        else{
            $("#mone").find(".msgcon").css('font-size','50px');
        }
    }
    $("#mone").find("span").first().html(onenickname);
    $("#mone").find("img").first().attr('src',onesrc);
    oopen=switchto(oopen,'mone');
}

function messageAdd()
{
    if(cur==len)
    {
        messageData();
        return false;
    }
    if (data[cur] == undefined) return false;
    if (data[cur][4] == ''){
	        if(data[cur][3].length>40){
            var str='<li class="clearfix t-row" id=li'+cur+' onclick="viewOne('+cur+',this);"><div class="fl"><div class="avatar"><img src="'+data[cur][1]+'" width="100" height="100" /></div><div class="name"><h2>'+data[cur][2]+'</h2><h3 style="font-size: 21px">'+data[cur][3]+'</h3></div></div></li>';
        }else if(data[cur][3].length>160){
            var str='<li class="clearfix t-row" id=li'+cur+' onclick="viewOne('+cur+',this);"><div class="fl"><div class="avatar"><img src="'+data[cur][1]+'" width="100" height="100" /></div><div class="name"><h2>'+data[cur][2]+'</h2><h3 style="font-size: 14px">'+data[cur][3]+'</h3></div></div></li>';
        }else{
            var str='<li class="clearfix t-row" id=li'+cur+' onclick="viewOne('+cur+',this);"><div class="fl"><div class="avatar"><img src="'+data[cur][1]+'" width="100" height="100" /></div><div class="name"><h2>'+data[cur][2]+'</h2><h3>'+data[cur][3]+'</h3></div></div></li>';
        }
    }
    else {
		
        var str='<li id=li'+cur+' onclick="viewOne('+cur+',this);"><div class="fl"><div class="avatar"><img src="'+data[cur][1]+'" width="100" height="100" /></div><div class="name"><h2>'+data[cur][2]+'</h2><h3>'+data[cur][3]+'</h3></div></div><img class="msgImage" src="'+data[cur][4]+'"/></li>';
    }

    if(cur > 5){
        $("li").remove("#li"+(cur-5));
    }
    $(".user-list").prepend(str);
    $("#li"+cur).slideDown(600);
    //if (data[cur][4] != ''){
    //    viewOne(cur,cur);
    //    window.setTimeout('viewOneHide();', 3000);
    //}
    cur++;
    messageData();
}
function messageData()
{
    var url=$('param.message').attr('url');
    $.getJSON(url,{lastid:lastid},function(d) {
        if(d['res'])
        {//alert(d['res'].content);
            //$.each(d['res'], function(i,v){
                data.push(new Array(d['res'].num,d['res'].avatar,d['res'].nickname,d['res'].content,d['res'].image,d['res'].fromtype));
                lastid=d['res'].id;
                len++;
            //});
        }else{
            window.setTimeout('messageData();', refreshtime*1000);
        }
    });
}
window.onload=function()
{
    mtime=setInterval(messageAdd,refreshtime*1000);

};