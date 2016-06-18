$(function() {
  var a = $(window).height();
  var a1 = $(".main").height()

})
$(function() {
  var n = $(".toupiao_show li").length;
  var a;
  var d = 0;
  var h = 500;
  h = h / 100;
  for (var i = 0; i < n; i++) {
    a = $(".toupiao_show li").eq(i).find("i").text();
    a = parseInt(a);
    if (d < a) {
      d = a;
    }
  }
  $(".toupiao_show li").css("height", h * d + 100);
  //$(".tiao").css("height",parseInt($(this).siblings("i").text()))
  $(".tiao").click(function() {
    var aa = $(this).siblings("i").text();
    aa = parseInt(aa) * h;
    $(this).animate({
      height: aa
    }, 1000)
  })
  $('.tiao').trigger('click');

})


$(function() {
  center(".main");
})

function centerWindow(a) {
  center(a);
  //自适应窗口
  $(window).bind('scroll resize', function() {
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

  if (boxHeight < wHeight) {
    $(a).css({
      "top": top,
      "left": left
    });
  } else {
    $(a).css({
      "top": 0,
      "left": left
    });
  }


  function lastLi(a, b) {
    $(a).each(function() {
      var li = $(this).find("li");
      var len = $(this).find("li").length;
      var y = len / b;
      for (var i = 1; i <= y; i++) {
        li.eq(i * b - 1).css({
          'margin-right': '0',
          'border-right': '0'
        });
      }
    })
  }

}
$(function() {
  var i;
  var a = $(".qiandao_show li").length;

  for (i = 0; i < a; i++) {
    $(".qiandao_show li").eq(i).find(".user-block.in").css({
    //  "animation-delay": i * 0.5 + "s",
      "z-index": 999 - i
    })

var ii;
var j=500;
$('.qiandao_show li').each(function(){
    ii=$(this).index();
    tt=function(id){
        $('.qiandao_show li').eq(id).find(".user-block").show();
      $('.qiandao_show li').eq(id).find(".heads_bg").hide();
    $('.qiandao_show li').eq(id).find("i").fadeOut(1000);
    }
    setTimeout('tt('+ii+')',j);
    j=j+1000;
  
});
 
  }



  //抽奖名字
  $(".choulist").click(function() {
    $(".choulist ul").toggle();
  });
  $(".choulist ul li").on('click',function() {
  var _this = this;
  var pid = $(_this).attr('value');
  $.ajax({
    url  : $('.lottery').attr('url-prize'),
    data : {pid : pid},
    type : 'POST',
    dataType : 'JSON',
    success : function (data) {
      $('.choujiang_r ul').html('');
      $('.choujiang_title .span1 i').html(data.num + "名");
      $('.choujiang_content .choujiang_img img').attr('src', data.pic);
      $('.choujiang_content .pname').html(data.pname);
            if(data.prize_user){
              var html='';
              for(i =0; i < data.prize_user.length; i++) {
          html += '<li><a href="javascript:;"><div class="head_img"><img src="'+data.prize_user[i].portrait+'" /></div><p>'+data.prize_user[i].nickname+'</p></a></li>';
        }
              $('.choujiang_r ul').html(html);
            } 
        var oText=$(_this).text();
        $(".choulist input").val(oText);
        $(".choulist input").attr('pid', pid);
        $(".choujiang .choulist ul").slideUp();
    }
  });
  });
 



 var maleArray = {};
 var femaleArray = {};
 var maleArrayPrize = {};
 var femaleArrayPrize = {};
 var maleLength = '';
 var femaleLength = ''
 var malePrize = '';
 var femalePrize = '';
 $('#start').click(function() {
  var _this = this;
  $.ajax({
    url : $('.lottery').attr('url-start'),
    type : 'POST',
    dataType : 'json',
    success : function (data) {
      if(0 == data.err) {
        maleLength = data.data.maleCount;
        femaleLength = data.data.femaleCount;
          $(".dui_con i").hide();
          for (i = 0; i < maleLength; i++) {
            maleArray[i] = '<a href="javascript:;"><div class="head_img"><img src="'+data.data.list.male[i].portrait+'" /></div><p>'+data.data.list.male[i].truename+'</p></a>';
            maleArrayPrize[i] = data.data.list.male[i].id;
          }
          for (i = 0; i < femaleLength; i++) {
            femaleArray[i] = '<a href="javascript:;"><div class="head_img"><img src="'+data.data.list.female[i].portrait+'" /></div><p>'+data.data.list.female[i].truename+'</p></a>';
            femaleArrayPrize[i] = data.data.list.female[i].id;
        }
          immer = setInterval(function() {
            malePrize = Math.floor(Math.random() * maleLength);
            femalePrize = Math.floor(Math.random() * femaleLength);
            $('#box').html(maleArray[malePrize]);
            $('#box1').html(femaleArray[femalePrize]);
          }, 50);
          $(_this).hide();
          
          $('#stop').show();              
      } else {
        alert(data.info||data.msg);
      }
    }
    
  });

 
});
 var j=1;

$('#stop').click(function() {
  clearInterval(immer);
  var _this = this;
  $.ajax({
    url : $('.lottery').attr('url-stop'),
    data : {sceneid : 1, nid : maleArrayPrize[malePrize], vid : femaleArrayPrize[femalePrize]},
    type : 'POST',
    dataType : 'json',
    success : function (data) {
      $(".dui_con i").show();
      $('.peidui_list ').prepend("<li><i>" + j + "</i><div class='peidui_content clearfix'> <div class='peidui_head'>" 
      + maleArray[malePrize] + "</div><div class='peidui_xin'></div> <div class='peidui_head'>" + femaleArray[femalePrize] + "  </div> </div>  <div class='close_but'></div> </li>");

       $(".duiduipeng_title b").text(parseInt($(".duiduipeng_title b").text()) + 1)
      j++;
      $(_this).hide();
      $('#start').show();
    }
  });
});
 
 
})

$(function() {
  $(".close_but").live('click',function() {
    $(this).parent().remove();
    $(".duiduipeng_title b").text($(".duiduipeng_title b").text() - 1);
  })
  $(".peidui_bot").click(function() {
    $(".peidui_list li").remove();
  })
  $(".peidui_head  span").live('click',function(){
    $(this).parent().find("img").attr("src","");
    $(this).parent().find("p").text("......");
        })
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

function add(){//测试用
  var a=$("<li>222222</li>");
  $(".qiandao_show").append(a);
  setTimeout("changeHight()",5);//滚动条滚动到底部，异步请求时调用这个方法
}