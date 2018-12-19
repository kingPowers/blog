<?php $viewParams = $this->params; ?>
<style type="text/css">
.models-div {width: 100%;height: 100%;display: inline-block;}
.models-div label {display: inline-block;float: left;width: 70px;height: 30px;line-height: 30px;}
</style>
<form id='navigateForm' class="definewidth m20" >
<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken?>">
<input type="hidden" name="is_sub" value="1">
<input type="hidden" name="navigateid" value="<?= $viewParams['navigateInfo']['id'] ?>">
  <table class="table table-bordered table-hover m10">
    <tr>
      <td class="tableleft">导航名</td>
      <td><input type="text" name="title" value="<?= $viewParams['navigateInfo']['title'] ?>"/></td>
    </tr>
    <tr>
      <td class="tableleft">导航链接</td>
      <td><input type="text" name="url" value="<?= $viewParams['navigateInfo']['url'] ?>"/></td>
    </tr>
    <tr>
      <td class="tableleft">models</td>
      <td><div class="models-div">
        <?php
          $str = '';
          foreach ($viewParams['models'] as $value) {
            $checked = (false === stripos($viewParams['navigateInfo']['models'], $value))?"":"checked=''";
            $str .= "<label><input name='modelsArr' type='checkbox' value='".$value."'  ".$checked."/>".$value."</label>";
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
            $checked = ($key == $viewParams['navigateInfo']['status'])?"checked=''":"";
            echo "<input type='radio' name='status' value='".$key."' ".$checked.">".$value."&nbsp;&nbsp;";
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
      if (F.status)window.location.href = "/layout/navigate";
    } 

    top.jdbox.alert(2);
    subNavigate.submitForm();
  });
  
	$('button#backid').click(function(){
		window.location.href= "/layout/navigate";
	})
})
</script>    
