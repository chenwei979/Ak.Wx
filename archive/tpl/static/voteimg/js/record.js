var isRecording = false;
var voice_record_start;
//开始录音
function startRecord(){
	$(".sign_vido").hide();
	$("#stop").show();
	$('#repeat').hide();
	$('#play').hide();
	var voice_record_time = 0;
	voice_record_start = (new Date()).getTime();
	if (isRecording) return;
	isRecording = true;
	setTimeout(function () { // 60秒后自动停止录音
		stopRecord();
	}, 60 * 1000);
	wx.startRecord({
		success: function () {
			isRecording = false;
			$(".sign_vido").hide();
			$("#stop").show();
			$('#repeat').hide();
			$('#play').hide();
		},
		cancel: function () {
			isRecording = false;
		}
	});
}

//停止录音
function stopRecord(){
	isRecording = false;
	voice_record_time = Math.ceil(((new Date()).getTime() - voice_record_start) / 1000);
	if(voice_record_time < 1){
		alert('您的录音时间太短');
		$(".sign_vido").show();
		$("#stop").hide();
		$('#repeat').hide();
		$('#play').hide();
	}
	wx.stopRecord({
		success: function (res) {
			var localId = res.localId;
			$("#localId").val(localId);
			onRecordDone(localId);
		},
		complete:function(){
			$(".sign_vido").hide();
			$("#stop").hide();
			$('#repeat').show();
			$('#play').hide();
			//$("#stop").html("播放中...&nbsp;&nbsp;<a href=\"javascript:stopPlay();\">停止播放</a>");
		}
	});
}
//录完播放和上传
    var last_voice_localId;
    function onRecordDone(localId) {
		voice_record_time = Math.ceil(((new Date()).getTime() - voice_record_start) / 1000);
		$("#record_time").val(voice_record_time);
		last_voice_localId = localId;
        setTimeout(function () {
            uploadVoice(localId);
        }, 1000);
       // playVoice(localId);
    }
//上传声音
function uploadVoice(localId) {
	wx.uploadVoice({
		localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
		isShowProgressTips: 1, // 默认为1，显示进度提示
		success: function (res) {
			var serverId = res.serverId; // 返回音频的服务器端ID
			$("#serverId").val(serverId);
		}
	});
}

//播放声音
var is_playing = false;
function playVoice(id){
	$(".sign_vido").hide();
	$("#stop").hide();
	$('#repeat').hide();
	$('#play').show();
	is_playing = true;
	setTimeout(function () {
		is_playing = false;
		$(".sign_vido").hide();
		$("#stop").hide();
		$('#repeat').show();
		$('#play').hide();
	}, voice_record_time * 1000);
	wx.playVoice({
		localId: id// 需要播放的音频的本地ID，由stopRecord接口获得
	});
}
function playVoiceByUser() {
    playVoice(last_voice_localId);
}
function stopPlay(){
	wx.stopVoice({
		localId: last_voice_localId // 需要停止的音频的本地ID，由stopRecord接口获得
	});
	$(".sign_vido").hide();
	$("#stop").hide();
	$('#repeat').show();
	$('#play').hide();
}

function recordagain(){
	$("#localId").val('');
	$("#serverId").val('');
	startRecord();
}