$(function() {
  var a = $(window).height();
  var a1 = $(".main").height()

})
 

//投票
function voting(){
var vote=[
    {
	    "id": "0",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"  微商城微商城微商城微商城微商城"
	
	},
	    {
	    "id": "2",
        "percent": "50%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "3",
        "percent": "20%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "4",
        "percent": "60%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "5",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	},  {
	    "id": "6",
        "percent": "10%",
        "number": "150票",
        "pillar": "",
		"name":"微商城微商城微商城微商城微商城"
	
	} ] ;	
 var n =vote.length;
 var  keyArr={f1:0,f2:0,f3:0};
 var li_width=846/n;
  var a;
  var d = 0;
  var kk='';
   for(nn=1;nn<4;nn++){
	   d=0;
	   kk='f'+nn;
   	for (var i = 0; i < n; i++) {
   	 a =vote[i].percent;
    	a = parseInt(a);
    	if ((d < a) && (typeof(vote[i].ismax)=='undefined')) {
			d=a;
     	 keyArr[kk]=i;
    	}
   	}
	kk=keyArr[kk];
	vote[kk].ismax=0;
   }
   var maxd=0;
   	for (var i = 0; i < n; i++) {
	 a =vote[i].percent;
    	a = parseInt(a);
    	if (maxd<a) {
			maxd=a;
		
		}
	}
   vote[keyArr.f1].pillar='toupiao_one';
   vote[keyArr.f2].pillar='toupiao_two';
   vote[keyArr.f3].pillar='toupiao_three';
  
  // console.log(vote[keyArr.f1].pillar,vote[keyArr.f2].pillar,vote[keyArr.f3].pillar);

	if(n>0){
     //window.setTimeout(voting,3000);
 
		var text="";
		for(var i=0;i<vote.length;i++){
			console.log(maxd,vote[i].percent);
			var tiao_height=260/maxd*parseInt(vote[i].percent);
 
  			text +="<li style='width:"+li_width+"px;height:330px'><div class='show_list' id="+vote[i].id+"><i> "+vote[i].percent+"</i><span>"+vote[i].number
			+"</span><div class='tiao "+vote[i].pillar+"' style='height:"+tiao_height+"px'></div><span>"+vote[i].name+"</span></div ></li>";
	/*$(".tiao").click(function() {
    var aa = $(this).siblings("i").text();
    aa = parseInt(aa) * h;
    $(this).animate({
      height: tiao_height
    }, 1000)
  })*/
  //$('.tiao').trigger('click');
 }
	$(".toupiao_show").html(text);
  	}
	
	}
$(function() {
 voting();//调用
  
})
