<?php 
use backend\models\Label;
$viewParams = $this->params; ?>
<form id='labelForm' class="definewidth m20" >
<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken?>">
<input type="hidden" name="is_sub" value="1">
  <table class="table table-bordered table-hover m10">
    <tr>
      <td class="tableleft">标签名</td>
      <td><input type="text" name="name" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">标签介绍</td>
      <td><input type="text" name="intro" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">父级标签</td>
      <td>
        <select name="pid">
          <option value="0">一级标签</option>
          <?php foreach ($viewParams['p_label'] as $key => $value) { ?>
            <option value="<?= $value['id'] ?>" <?php if ($viewParams['labelInfo']['pid'] == $key) echo 'selected=""'; ?>><?= $value ?></option>
         <?php } ?>
        </select>
      </td> 
    </tr>
    <tr>
      <td class="tableleft">状态</td>
      <td>
        <?php
          foreach (Label::getStatus() as $key => $value) {
            echo "<input type='radio' name='status' value='".$key."'>".$value."&nbsp;&nbsp;";
          }
        ?>
    </tr>
    <tr>
      <td class="tableleft">是否热门</td>
      <td>
        <?php
          foreach (Label::getHotStatus() as $key => $value) {
            echo "<input type='radio' name='is_hot' value='".$key."'>".$value."&nbsp;&nbsp;";
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
    var subLabel = new formOperate("labelForm",{url:"/layout/editlabel"});

    subLabel.success = function (F) {
      top.jdbox.alert(F.status,F.info);
      if (F.status) {
        window.location.href = "/layout/label";
      }
    } 

    top.jdbox.alert(2);
    subLabel.submitForm();
  });
  
	$('button#backid').click(function(){
		window.location.href= "/layout/label";
	})
})
</script>    
