//读写cookie函数
function GetCookie(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end   = document.cookie.indexOf(";",c_start);
			if (c_end == -1) {
				c_end = document.cookie.length;
			}
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}
	return null
}
//设置cookie
function SetCookie(c_name,value,expiredays) {
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + expiredays);
	document.cookie = c_name + "=" +escape(value) + ';path=/' +((expiredays == null) ? "" : ";expires=" + exdate.toGMTString());
}

//播放
function playvoice(){
	var oAudio = document.getElementById('myaudio');
	oAudio.play();
	$("#img_mouth_playing").show();
	$("#img_mouth").hide();
}
//暂停
function pausevoice(){
	var oAudio = document.getElementById('myaudio');
	oAudio.pause();
	$("#img_mouth_playing").hide();
	$("#img_mouth").show();
}