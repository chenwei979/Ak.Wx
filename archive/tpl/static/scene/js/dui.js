// JavaScript Document
var duiduuipeng_15 = $('.lottery').attr('url-duiduuipeng_15');
$(function() {
	var a =  document.body.scrollHeight;
	$("body").css("height",a);
	var aArray = new Array();
	var pArray = new Array();
	var aArray1 = new Array();
	var pArray1 = new Array();
    var active = "<div class='active'><i></i><p>......</p></div>"
    var box = "";
    var box1 = "";
    var aA_n = "";
    var pei = "";
    var pei1 = "";
	var num=0;
	var num1=0;
	var num2=0;
	var num3=0;
	var num4=0;
    var flag=false;
	var flag1=false;
    var huoqurenshu = 0;
	$.ajax({
		url : $('.lottery').attr('url-start'),
		type : 'POST',
		dataType : 'json',
		success : function (data) {
			if(0 == data.err) {
				maleLength = data.data.maleCount;
				femaleLength = data.data.femaleCount;
				for (i = 0; i < maleLength; i++) {
					aArray[i] = "<div class='head_img'><img src='"+data.data.list.male[i].portrait+"' /></div><p>"+data.data.list.male[i].truename+"</p>";
					pArray[i] = data.data.list.male[i].id;
				}
				for (i = 0; i < femaleLength; i++) {
					aArray1[i] = "<div class='head_img'><img src='"+data.data.list.female[i].portrait+"' /></div><p>"+data.data.list.female[i].truename+"</p>";
					pArray1[i] = data.data.list.female[i].id;
				}
			}else{
				alert(data.info||data.msg);
			}
		}
	});
    //$('#box').siblings().find("em").text(aArray.length);
    //$('#box1').siblings().find("em").text(aArray1.length);
    $('#start').click(function() {
		
		if(huoqurenshu == 0){
			if ($('#start').hasClass("active")) {} else {
				$.ajax({
					url : $('.lottery').attr('url-start'),
					type : 'POST',
					dataType : 'json',
					success : function (data) {
						if(0 == data.err) {
							huoqurenshu = 1;
							maleLength = data.data.maleCount;
							femaleLength = data.data.femaleCount;
							for (i = 0; i < maleLength; i++) {
								aArray[i] = "<div class='head_img'><img src='"+data.data.list.male[i].portrait+"' /></div><p>"+data.data.list.male[i].truename+"</p>";
								pArray[i] = data.data.list.male[i].id;
							}
							for (i = 0; i < femaleLength; i++) {
								aArray1[i] = "<div class='head_img'><img src='"+data.data.list.female[i].portrait+"' /></div><p>"+data.data.list.female[i].truename+"</p>";
								pArray1[i] = data.data.list.female[i].id;
							}
							$('#box').siblings().find("em").text(aArray.length);
							$('#box1').siblings().find("em").text(aArray1.length);
							if ($('#start').hasClass("active")) {} else {
								if ((aArray.length <= 0)) {
									$("#box .head_img").html("<img src='"+duiduuipeng_15+"' />");
									alert("参加对对碰的嘉宾不足,赶紧签到来参加吧!");
								} else if ((aArray1.length <= 0)) {

									$("#box1 .head_img").html("<img src='"+duiduuipeng_15+"' />")
									alert("参加对对碰的嘉宾不足,赶紧签到来参加吧!");
								} else {
									//      console.log(aArray.length);
									$(".dui_con i").hide();
									immer = setInterval(function() {
										num = Math.floor(Math.random() * aArray.length);
										num1 = Math.floor(Math.random() * aArray1.length);
										$('#box').html(aArray[num]);
										//console.log(aArray1[num1]);
										$('#box1').html(aArray1[num1]);
									}, 30)
									   flag=true;
									$('#start').hide();
									$('#stop').show();
								}
							}
						}else{
							alert(data.info||data.msg);
						}
					}
				});
			}
		}else{
			if ($('#start').hasClass("active")) {} else {
				if ((aArray.length <= 0)) {
					$("#box .head_img").html("<img src='"+duiduuipeng_15+"' />");
					alert("人数不够");
				} else if ((aArray1.length <= 0)) {

					$("#box1 .head_img").html("<img src='"+duiduuipeng_15+"' />")
					alert("人数不够");
				} else {
					//      console.log(aArray.length);
					$(".dui_con i").hide();
					immer = setInterval(function() {
						num = Math.floor(Math.random() * aArray.length);
						num1 = Math.floor(Math.random() * aArray1.length);
						$('#box').html(aArray[num]);
						//console.log(aArray1[num1]);
						$('#box1').html(aArray1[num1]);
					}, 30)
					   flag=true;
					$('#start').hide();
					$('#stop').show();
				}
			}
		}
    });
    var j = $('.lottery').attr('url-count');
    $('#stop').click(function() {
		var _this = $(this);
        if (_this.hasClass("active")) {
        } else {
            clearInterval(immer);
            num2 = Math.floor(Math.random() * (aArray.length));
            num3 = Math.floor(Math.random() * (aArray1.length));
			
			nid = pArray[num2];
			vid = pArray1[num3];
			
			$(".dui_con i").show();
			$("#box1").removeClass("dd");
			$('#box').html(aArray[num2]);
			flag=false;
			$('#box1').html(aArray1[num3]).addClass("dd");
			
			$.ajax({
				url : $('.lottery').attr('url-stop'),
				type : 'POST',
				dataType : 'json',
				data : {nid : nid, vid : vid},
				success : function (data) {
					if(data.error == 0){
						
						$('.peidui_list ').prepend("<li><i>" + j + "</i><div class='peidui_content clearfix'> <div class='peidui_head'><span stype='nid' nid='"+nid+"' sid='"+data.sid+"'></span>" + aArray[num2] + "</div><div class='peidui_xin'></div> <div class='peidui_head'><span stype='vid' vid='"+vid+"' sid='"+data.sid+"'></span>" + aArray1[num3] + "  </div> </div>  <div class='close_but' sid='"+data.sid+"'></div> </li>");
						aArray.splice(num2, 1);
						aArray1.splice(num3, 1);
						pArray.splice(num2, 1);
						pArray1.splice(num3, 1);
						$('#box').siblings().find("em").text(aArray.length);
						$('#box1').siblings().find("em").text(aArray1.length);
						$(".duiduipeng_title b").text(j);
						j++;
						_this.hide();
						$('#start').show();
					}
				}
			});
						
        }
    });
function spancl() {
        aA_n.push(pei);
        pA_n.push(uid);
        box.siblings().find("em").text(aA_n.length);
        // console.log(aArray);
        box.html("<img src='"+duiduuipeng_15+"' />");
        box1.html(pei1);
       // console.log(pei1);
        $("#start").addClass("active").text("为Ta重新配对");
        $("#stop").addClass("active");
        var immer1 = 0;
		
        $("#start").click(function() {
			if($(this).hasClass("active")){
             immer1 = setInterval(function() {
                num4 = Math.floor(Math.random() * (aA_n.length));
               // console.log(aArray);
                box.html(aA_n[num4]);
                //box1.html(pei1);
             //  console.log(num4);

                box.siblings().find("em").text(aA_n.length);
            }, 30);
                flag1=true;
            $(this).removeClass("active").hide();
            $('#stop').show();
			}
        });
        $('#stop').click(function() {
            clearInterval(immer1);
			 
            //$(".peidui_list li").eq(index - 1).children().find(".active").parent().hide();
            var da = $(".peidui_list li .peidui_content .peidui_head");

            if (da.hasClass("on")) {
                var num0 = Math.floor(Math.random() * (aA_n.length));
				uid = pA_n[num0];
				var dd = "<span></span>" + aA_n[num0];
				$(".peidui_list li").children().find(".active").parent().html(dd);
				box.html(aA_n[num0]);
				//box1.html(pei1);
				aA_n.splice(num0, 1);
				pA_n.splice(num0, 1);
				box.siblings().find("em").text(aA_n.length);
                $.ajax({
					url : $('.lottery').attr('url-set'),
					type : 'POST',
					dataType : 'json',
					data : {sid : sid, uid : uid, stype : stype},
					success : function (data) {
						if(data.error == 0){
							
						}
					}
				});
							

            }
            da.removeClass("on");
            flag1=false;
            $(this).removeClass("active").hide();
            $("#start").text("开始对对碰").show();
            clearInterval(immer1);

        });

    }

    var index = 0;
    //2113
    $(".close_but").live('click', function() {
		var _this = $(this);
		if(flag||flag1){
		alert("请完成配对")	;			
			} else{	
        if (confirm('是否取消配对')) {
				var sid = _this.attr('sid');
				$.ajax({
					url : $('.lottery').attr('url-del'),
					type : 'POST',
					dataType : 'json',
					data : {sid : sid},
					success : function (data) {
						if(data.error == 0){
							var pei = _this.prev().children(":first").html();
							var pei1 = _this.prev().children(":last").html();
							_this.parent().remove();
							aArray.push(pei);
							aArray1.push(pei1);
							$('#box').siblings().find("em").text(aArray.length);
							$('#box1').siblings().find("em").text(aArray1.length);
							//console.log(aArray); 
							var j_count = $('.peidui_list li').length;
							$(".duiduipeng_title b").text(j_count);
							if(j_count > 0){
								j = j_count+1;
								var ii = 0;
								for(var jj = j_count; jj > 0; jj--){
									$('.peidui_list li:eq('+ii+') i').text(jj);
									ii++;
								}
							}
						}
					}
				});
							
			}
        }
    })
    $(".peidui_bot").click(function() {
		if(flag|flag1){
		alert("请完成配对");
	} else{
		$.ajax({
			url : $('.lottery').attr('url-again'),
			type : 'POST',
			dataType : 'json',
			success : function (data) {
				if(data.error == 0){
					huoqurenshu = 0;	/*alert(flag);*/	window.location.reload();
				}
			}
		});
		
	}
    })

    $(".peidui_head  span").live('click', function() {
		var _this = $(this);
		if(flag){
		alert("请完成配对");			
			} else{	
   
				if ($(".peidui_head").hasClass("on")) {
					alert("对对碰区域还有未完成的配对,暂不能进行删除操作!");
				} else {
					if (confirm('是否取消配对')) {
						
						index = _this.parent().addClass("on");
						pei = _this.parent().html();
						pei1 = _this.parent().siblings(".peidui_head").html();
						sid = _this.attr('sid');
						stype = _this.attr('stype');
						
					 
						if (_this.parent().next().hasClass("peidui_xin")) {
							box = $('#box');
							box1 = $('#box1');
							aA_n = aArray;
							pA_n = pArray;
							uid = _this.attr('nid');
							if(aA_n.length>=0){
								_this.parent().html(active);
								spancl();
							}
							else{alert("参加对对碰的嘉宾不足，赶紧签到来参加吧!");}

						} else {
							box = $('#box1');
							box1 = $('#box');
							aA_n = aArray1;
							pA_n = pArray1;
							uid = _this.attr('vid');
							if(aA_n.length>=0){
								_this.parent().html(active);
								spancl();
							}
							else{alert("参加对对碰的嘉宾不足，赶紧签到来参加吧!");}
						}
	 
					}
				}
            }
   
    })

})