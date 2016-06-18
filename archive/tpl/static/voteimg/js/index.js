// JavaScript Document
var width=$(window).width();
var height=$(window).height();
$(function(){
	//$("header").css("height",width*0.565);
	$(".indi_info").css("width",width-90);	
    $('label').click(function() {
        var radioId = $(this).attr('name');
        $('.order_add_list_ul label').removeAttr('class') && $(this).attr('class', 'checked');
        $('.order_add_list_ul input[type="radio"]').removeAttr('checked') && $('#' + radioId).attr('checked', 'checked');
    });	
	$(".layer,.close").click(function(){
		$(".layer").fadeToggle(500);
	    $(".layer_content").fadeOut(300);
		$("body").css({"height":"auto","overflow":"auto","overflow-x":"hidden"}); 
		})	
	}) 
	function recharge(){
	$(".layer").fadeToggle(500);
	$(".recharge").fadeToggle(300);
    $("body").css({"height":height,"overflow":"hidden"}); 
		}
	function editor(){
	$(".layer").fadeToggle(500);
	$(".editor").fadeToggle(300);
 
	$("body").css({"height":height,"overflow":"hidden"}); 
		}
	function record(){
	$(".layer").fadeToggle(500);
	$(".record").fadeToggle(300);
	$("body").css({"height":height,"overflow":"hidden"}); 
		}
		
$(function() {
  tab("menu li", ".menu_list > li", "active")

})
function tab(a, b, c) { //a 是点击的目标,,b 是所要切换的目标,c 是点击目标的当前样式
  var len = $(a);
  len.bind("click",
    function() {
      var index = 0;
      $(this).addClass(c).siblings().removeClass(c);
      index = len.index(this); //获取当前的索引
	  	  switch(index){
		  case 0:
				$("#tabname").val('a');
				break;
			case 1:
				$("#tabname").val('b');
				break;
			case 2:
				$("#tabname").val('c');
				break;
			case 3:
				$("#tabname").val('d');
				break;
			default:
				break;
	  }
      $(b).eq(index).show().siblings().hide();
      return false;
    }).eq(0).trigger("click"); //浏览器模拟第一个点击
}