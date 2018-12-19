<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<script type="text/javascript" src='_STATIC_public/plugins/dropzone/dropzone.js'></script>
<style type="text/css">
	table tr td {min-height: 50px;line-height: 50px;min-width: 100px;}
	.uploadfile div{display: none;}
	.upload-progress{width: 100%;height: 50px;}
	.upload-progress div {display: inline-block;}
	.progress-left {width: 24%;}
	.progress-mid {width: 50%;}
	.progress-right {width: 24%;}
    .progress-mid .all-progress{width: 90%;height: 20px;background: #fff;border-radius: 5px;vertical-align: middle;overflow: hidden;}
    .progress-bar{width: 0%;background: #5484ff;height: 100%;}
</style>
<div class="container" style="width: 700px;margin: 50px auto;border: 1px solid #ccc;border-radius: 10px;">
	<div class="box" style="padding: 2px 10px;">
		<table>
			<tr>
				<td colspan="2">文件上传</td>
			</tr>
			<tr>
				<td>文件：</td>
				<td><button class="btn btn-primary uploadfile">上传文件</button></td>
			</tr>
			<tr>
				<td>已上传文件：</td>
				<td>
					<div class="uploading-div" style="width: 600px;line-height: normal;">
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<script type="text/javascript">
function uploadProgressHtml (data) {
	var container = createBaseDom('div','upload-progress');
	var progressLeft = createBaseDom('div','progress-left');
	var progressMid = createBaseDom('div','progress-mid');
	var allProgress = createBaseDom('div','all-progress');
	var progressBar = createBaseDom('div','progress-bar');
	var progressRight = createBaseDom('div','progress-right');
	var leftText = '正在上传文件:';
	var rightText = '0%';
	if (data.leftText) leftText = data.leftText;
	if (data.rightText) rightText = data.rightText;
	progressLeft.innerText = leftText;
	progressRight.innerText = rightText;
	allProgress.appendChild(progressBar);
	progressMid.appendChild(allProgress);
	container.appendChild(progressLeft);
	container.appendChild(progressMid);
	container.appendChild(progressRight);
	return container;
}
$('.uploadfile').dropzone({
	url : "<?= Url::to(['public/upload-file']) ?>"
	,dictRemoveLinks: "x"
    ,dictCancelUpload: "x"
    ,dictFileTooBig:"上传文件({{filesize}}M)过大，大小限制{{maxFilesize}}M"
	,paramName : 'uploadfile'
	,maxFiles : 10
	,maxFilesize : 200
	,init:function () {
		var progressDom = '';
		var ot = new Date().getTime();
		var ol = 0;
		this.on('sending',function (file,xhr,formData) {
			formData.append('is_uploadfile',1);
		});
		this.on('addedfile',function (file) {
			var html = uploadProgressHtml({leftText:file.name});
			progressDom = html;
			$('.uploading-div').append(html);
			var ot = new Date().getTime();
			var ol = 0;
		});
		this.on("uploadprogress",function(file,progress,size) {
			if (progressDom) {
				var total = ((file.upload.total)/1024)/1024;
				var current = ((size)/1024)/1024;
				$(progressDom).find(".progress-bar").css("width",progress + "%");
				var nt = new Date().getTime();
				var pertime = (nt - ot)/1000;
				ot = nt;
				var nl = size;
				var perloaded = nl - ol;
				ol = nl;
				var speed = perloaded/pertime;
				var unit = "B/s";
				if (speed > 1024) {
				  speed = speed/1024;
				  unit = "KB/s";
				}
				if (speed > 1024) {
				  speed = speed/1024;
				  unit = "M/s";
				}
				var speedText = speed.toFixed(2) + unit;
				$(progressDom).find(".progress-right").html("<span style='color:rgb(217, 83, 79);'>" + current.toFixed(2) + "M</span>/" + total.toFixed(2) + "M <span style='color:rgb(217, 83, 79);'>" + progress.toFixed(2)+"% " + speedText + "</span>");
			}
				
         });
		this.on('success',function (file,res) {
			console.log(res);
			$(progressDom).find(".progress-right").html("<span style='color:rgb(217, 83, 79);'>"+res.info+"</span>");
		});
		this.on('error',function (file,message,ajaxMessage) {
			if (progressDom) {
				var text = '出错';
				if (message) {
					text = message;
				} else if (ajaxMessage) {
					text = ajaxMessage;
				}
				$(progressDom).find(".progress-right").html("<span style='color:rgb(217, 83, 79);'>"+text+"</span>");
			}
		})
	}

});
</script>