<?php $viewParams = $this->params; ?>
<style type="text/css">
.models-div {width: 100%;height: 100%;display: inline-block;}
.models-div label {display: inline-block;float: left;width: 70px;height: 30px;line-height: 30px;}
</style>
<form id='navigateForm' class="definewidth m20" >
<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken?>">
<input type="hidden" name="is_sub" value="1">
  <table class="table table-bordered table-hover m10">
    <tr>
      <td class="tableleft">导航名</td>
      <td><input type="text" name="title" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">导航链接</td>
      <td><input type="text" name="url" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">models</td>
      <td><div class="models-div">
        <?php
          $str = '';
          foreach ($viewParams['models'] as $value) {
            $str .= "<label><input name='modelsArr' type='checkbox' value='".$value."' />".$value."</label>";
          }
          echo $str;
        ?>
      </div></td>
    </tr>
    <tr>
      <td class="tableleft"></td>
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
  $('button.btn-primary').click(function(){
    var subNavigate= new formOperate("navigateForm",{url:"/layout/editnavigate"});

    subNavigate.success = function (F) {
      top.jdbox.alert(F.status,F.info);
      if (F.status) {
        window.location.href = "/layout/navigate";
      }
    } 

    top.jdbox.alert(2);
    subNavigate.submitForm();
  });
  
	$('button#backid').click(function(){
		window.location.href= "/layout/navigate";
	})
})
</script>    
