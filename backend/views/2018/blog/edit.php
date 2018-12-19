<?php 
use backend\components\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
 ?>
<style type="text/css">
.labels-div {width: 100%;height: 100%;display: inline-block;}
.labels-div label {display: inline-block;float: left;min-width: 100px;height: 30px;line-height: 30px;}
.error-msg {display: inline-block;color: red;height: 40px;line-height: 40px;margin-left: 20px;}
.no-msg {display: none;}
</style>
<div class="error-msg <?= $model->backend_err_msg?'':'no-msg'?>">
  <?= $model->backend_err_msg; ?>
</div>
<?php $form = ActiveForm::begin(['method' => 'post']);?>
  <?= Html::input('hidden','id',$model->id) ?>
  <?= Html::input('hidden','is_edit',1) ?>
  <table class="table table-bordered table-hover m10">
    <tr>
      <td class="tableleft">标题</td>
      <td><?= $form->field($model,'title')->textInput() ?></td>
    </tr>
    <tr>
      <td class="tableleft">主标签</td>
      <td><?= $form->field($model,'major_label')->dropDownList($labels) ?></td>
    </tr>
    <tr>
      <td class="tableleft">副标签选择</td>
      <td><?= Html::dropDownList('label_chose',null,$labels) ?></td>
    </tr>
    <tr>
      <td class="tableleft">已选副标签</td>
      <td>
        <div class="labels-div">
        <?php foreach ($vice_label as $value): ?>
          <label><?= Html::input('checkbox','Article[labels]',$value['id'],['checked' => 'true']) ?><?= $value['name'] ?></label>
        <?php endforeach ?>
        </div>
      </td>
    </tr>
    <tr>
      <td class="tableleft">介绍</td>
      <td><?= $form->field($model,'introduction')->textarea(['name' => 'introduction','cols'=>50,'rows'=>5,'style'=>'width:auto;height:auto;']) ?></td>
    </tr>
    <tr>
      <td class="tableleft">是否最新</td>
      <td><?= $form->field($model,'is_new')->radioList($new_status) ?></td>
    </tr>
    <tr>
      <td class="tableleft">是否置顶</td>
      <td><?= $form->field($model,'is_top')->radioList($top_status) ?></td>
    </tr>
    <tr>
      <td class="tableleft">状态</td>
      <td><?= $form->field($model,'status')->radioList($status) ?></td>
    </tr>
    <tr>
      <td colspan="2"><?= Html::input('submit','sub','提交',['class' => 'btn btn-success']) ?></td>
    </tr>
  </table>
<?php ActiveForm::end();?>
<script language="javascript">
 /*---------------------------------------
  * 清除字符串两端空格，包含换行符、制表符
  *---------------------------------------*/
 var trim = function (str) { 
  return str.replace(/(^[\s\n\t&nbsp;]+|[\s\n\t&nbsp;]+$)/g, "");
 }
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

   // top.jdbox.alert(2);
    subArticle.submitForm();
  });
  
  $('button#backid').click(function(){
    window.location.href= "/blog/article";
  })
})
$("select[name='label_chose']").change(function () {
  var id = $(this).val(),name = $(this).find('option:selected').html();
  var str = '<label><input type="checkbox" checked="" name="labels" value="' + id + '">' + trim(name) + '</label>';
  $('.labels-div').append(str);
})
</script>    