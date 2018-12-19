<?php 
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div style="margin: 10px 20px;">
  <div class="layui-form-item" style="margin-bottom: 0px;">
    <?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => 'get','options' => ['style' => 'margin-top:10px;']]); ?>   
           <?= $filter ?>           
          <a type="button" class="btn btn-success" href="/blog/edit">添加文章</a>
          <a href="<?= Url::to(['public/upload-file']) ?>">上传文件</a>
          <a href="http://static.quanjiaxin.cn/upload/1.html" download=''>下载</a> 
    <?php ActiveForm::end(); ?>
  </div>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
        <tr>

          <?php foreach ($title as $value) { ?>

            <th><?= $value ?></th>

          <?php } ?>

        </tr>
    </thead>
    <tbody>
      <?php
        foreach ($lists as $list) {
          echo "<tr>";
          foreach ($title as $tk => $tv) {
            echo "<td>".$list[$tk]."</td>";
          }
          echo "</tr>";
        }
      ?>
    </tbody>
</table>
<div class="page-div">
<?=
LinkPager::widget([
      'pagination' => $pages,
    ]);
?>
</div>
<script type="text/javascript">
var content = function (id)
{
  var index = window.parent.layer.open({
    type:2
    ,content:"/blog/contentbrowse?articleid="+id
    ,area:['750px','600px']
    ,title:'内容'
  });
}
</script>