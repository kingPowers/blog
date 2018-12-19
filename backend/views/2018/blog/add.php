<?php $viewParams = $this->params; ?>
<style type="text/css">
.labels-div {width: 100%;height: 100%;display: inline-block;}
.labels-div label {display: inline-block;float: left;width: 100px;height: 30px;line-height: 30px;}
</style>
<form id='articleForm' class="definewidth m20" >
<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken?>">
<input type="hidden" name="is_sub" value="1">
  <table class="table table-bordered table-hover m10">
    <tr>
      <td class="tableleft">标题</td>
      <td><input type="text" name="title" value=""/></td>
    </tr>
    <tr>
      <td class="tableleft">标签选择</td>
      <td>
        <select name="label_chose">
          <?php foreach ($viewParams['labels'] as $key => $value) { ?>
            <option value="<?= $value['id'] ?>" <?php if ($viewParams['labelInfo']['pid'] == $value['id']) echo 'selected=""'; ?>><?= $value['name'] ?></option>
         <?php } ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tableleft">标签</td>
      <td>
        <div class="labels-div">
        <?php
          $str = '';
          foreach ($viewParams['labelList'] as $value) {
            $str .= "<label><input name='labels' type='checkbox' value='".$value['id']."'/>".$value['name']."</label>";
          }
          echo $str;
        ?>
        </div>
      </td>
    </tr>
    <tr>
      <td class="tableleft"></td>
      <td>
        <textarea name="introduction" cols="50" rows="5">
          
        </textarea>
      </td>
    </tr>
    <tr>
      <td class="tableleft">是否最新</td>
      <td>
        <?php
          foreach ($viewParams['newStatus'] as $key => $value) {
            echo "<input type='radio' name='is_new' value='".$key."'>".$value."&nbsp;&nbsp;";
          }
        ?>
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
  $('button.btn-primary').click(function(){
    var subArticle = new formOperate("articleForm",{url:"/blog/edit"});

    subArticle.success = function (F) {
      alertInfo(F.info,F.status);
      //top.jdbox.alert(F.status,F.info);
      if (F.status) {
        window.location.href = "/blog/article";
      }
    } 

    top.jdbox.alert(2);
    subArticle.submitForm();
  });
  
	$('button#backid').click(function(){
		window.location.href= "/blog/article";
	})
})
</script>    
