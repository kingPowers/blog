<?php $viewParams = $this->params; ?>
<form id='bannerForm' class="definewidth m20" >
<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken?>">
<input type="hidden" name="is_sub" value="1">
  <table class="table table-bordered table-hover m10">
    <tr>
      <td class="tableleft">标题</td>
      <td><input type="text" name="title" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">图片</td>
      <td><a class="btn btn-primary chose-img">选择图片</a><input type="file" name="bannerImg" style="display: none;"></td>
    </tr>
    <tr>
      <td class="tableleft">预览</td>
      <td><div class="img-browse" style="min-height: 50px;"></div></td>
    </tr>
    <tr>
      <td class="tableleft">链接URL</td>
      <td><input type="text" name="url" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">状态</td>
      <td>
        <?php
          foreach ($viewParams['status'] as $key => $value) {
            echo "<input type='radio' name='status' value='".$key."'>".$value."&nbsp;&nbsp;";
          }
        ?>
    </tr>
    <tr>
      <td class="tableleft"></td>
      <td><button class="btn btn-primary" type="button"> 保存 </button>
        <button type="button" class="btn btn-success" name="backid" id="backid">返回列表</button></td>
    </tr>
  </table>
</form>
<script language="javascript">

$(function(){
  $(".chose-img").click(function () {
    $("input[name='bannerImg']").click();
  })

  $("input[name='bannerImg']").change(function () {
    $(".img-browse").html("");
    var file = this.files[0];
    var src = window.URL.createObjectURL(file); 
    var img = "<img src='"+src+"'/>";
    $(".img-browse").append(img);
  })

  $('button.btn-primary').click(function(){
    var subBanner = new formOperate("bannerForm");
    var formData = new FormData();

    $.each(subBanner.formData,function (i,e) {
      formData.append(i,e);
    })

    var file = $("input[name='bannerImg']")[0];
    formData.append('bannerImg',file.files[0]);
    top.jdbox.alert(2);

    $.ajax({  
          url: '/layout/editbanner' ,  
          type: 'POST',  
          data: formData,  
          dataType:'json',
          async: false,  
          cache: false,  
          contentType: false,  
          processData: false,  
          success: function (F) {
              top.jdbox.alert(F.status,F.info);
              if (F.status) {
                window.location.href = "/layout/banner";
              }
          },  
          error: function (F) {  
              var F = eval("("+F+")");
                top.jdbox.alert(0,F.info);  
          }  
    });  

  });
  
	$('button#backid').click(function(){
		window.location.href= "/layout/banner";
	})
})
</script>    
