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
          <a type="button" class="btn btn-success" href="/layout/editnavigate">新增导航</a>
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
var edit = function (id)
{
  window.location.href = "/layout/editnavigate?navigateid=" + id;
}
</script>