<?php $viewParams = $this->params; ?>
<script type="text/javascript">
window.UEDITOR_HOME_URL = "/editer/";
</script>
<script type="text/javascript" src="/editer/ueditor.config.js"></script>
<script type="text/javascript" src="/editer/ueditor.all.js"></script>
<script type="text/javascript" src="/editer/lang/zh-cn/zh-cn.js"></script>
<div style="margin: 10px auto;display: block;width: 850px;margin-bottom: 100px;">
  <div style="text-align: center;"><h3><?= $viewParams['articleInfo']['title'] ?></h3></div>
  <div style="margin-top: 20px;">
    <textarea id="content" style="height: 500px;width: 880px;display: block;"><?= $viewParams['articleInfo']['content'] ?></textarea>
  </div>
  <div style="margin: 20px 0;width: 850px;margin-bottom: 200px;">
    <div style="margin: 0 auto;width: 200px;">
      <button class="btn btn-primary sub">保存</button><a style="margin-left: 20px;" class="btn btn-danger" href="/blog/article">返回</a>
    </div>
  </div>
</div>
<form id="article">
  <input type="hidden" name="id" value="<?= $viewParams['articleInfo']['id'] ?>">
  <input type="hidden" name="sub_content" value="1">
</form>
<script type="text/javascript">
var ue = UE.getEditor('content',{enableAutoSave:false});

$(".sub").click(function () {
  var articleForm = new formOperate("article",{url:"/blog/content"});

  articleForm.addFormData("content",ue.getContent());
  addCsrf(articleForm);
//return alert(ue.getContent());
  articleForm.success = function (F) {
    alertInfo(F.info,F.status);
    //top.jdbox.alert(F.status,F.info);
    if (F.status)
      window.location.href = "/blog/article";
  }

  //top.jdbox.alert(2);
  articleForm.submitForm();
})


</script>

