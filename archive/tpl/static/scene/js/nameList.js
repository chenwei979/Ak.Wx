/**
 * Created by Administrator on 2015/11/13.
 */

var refreshtime =3;
var len=6;
var cur=0;//当前位置
var mtime;
var data=new Array();
//data[0]=new Array('0','http:\/\/www.wookmark.com\/images\/thumbs\/540921_wookmark.jpg','在山的那边','90');
var lastid='1096';



function messageAdd()
{
    if(cur==len)
    {
        messageData();
        return false;
    }
    var str='<li id="li'+cur+'" class="clearfix t-row"><div class="fl"><div class="avatar"><img src="'+data[cur][1]+'" alt="avatar"></div><div class="name"> <h3>'+data[cur][2]+'</h3></div></div><em class="fr">'+data[cur][3]+'元</em></li>';
    if(cur > 50){
        $("#priceLsit li").remove("#priceLsit #li"+(cur-50));
    }
    $("#priceLsit").prepend(str);
    $("#priceLsit #li"+cur).slideDown(600);
    cur++;
    messageData();
}
function messageData2()
{
    var url=$('.param').attr('url-prize');
    alert(url);
    $.getJSON(url,{lastid:$('.param').attr('prize-id')},function(d) {
        if(d['ret']==1) {
            $.each(d['data'], function(i,v){
                data.push(new Array(v['id'],v['avatar'],v['nickname'],v['price']));
                lastid=v['id'];
                len++;
            });
        }else{
            window.setTimeout('messageData();', refreshtime*1000);
        }
    });	

}

function messageData()
{
	var last_id = $('.param').attr('prize-id');
	$.ajax({
		url : $('.param').attr('url-prize'),
		data : {last_id : last_id},
		type : 'POST',
		dataType : 'json',
		success : function (data) {
			if(data.id) {
				$('.param').attr('prize-id', data.id);
			    $("#priceLsit").prepend(data.html);
			    $("#priceLsit li:first").slideDown(600);
			}
			setTimeout(messageData, 3000);
		}
	});

}

function activeUser() {
	var last_id = $('.param').attr('user-id');
	$.ajax({
		url : $('.param').attr('url-user'),
		data : {last_id : last_id},
		type : 'POST',
		dataType : 'json',
		success : function (data) {
			if(data.id) {
				$('.param').attr('user-id', data.id);
				$('.userList ul').prepend(data.html);
			}
			setTimeout(activeUser, 3000);
		}
	});
}


window.onload=function()
{
    //mtime=setInterval(messageAdd,refreshtime*1000);
	activeUser();
    $(".con-inner .hd li:eq(1)").click(function(){
        var hide=$(".scrollBox").is(":hidden");
        if(!hide){
        	messageData();
        	//clearInterval(mtime);
            //mtime=setInterval(messageAdd,refreshtime*1000);
        }else{
            return;
        };

    });   

};