<?php 
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<link rel="stylesheet" type="text/css" href="_STATIC_/public/plugins/imgbrowser/style.css">
<script type="text/javascript" src="_STATIC_/public/plugins/imgbrowser/plug-in_module.js"></script>
<div style="margin: 10px 20px;">
  <div class="layui-form-item" style="margin-bottom: 0px;">
    <?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => 'get','options' => ['style' => 'margin-top:10px;']]); ?>   
           <?= $filter ?> 
          <a type="button" class='btn btn-primary btn-sm' href="<?= Url::to(['layout/editbanner'])?>">新增banner</a>   
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
        foreach ($list as $value) {
          echo "<tr>";
          foreach ($title as $tk => $tv) {
            echo "<td>".$value[$tk]."</td>";
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
  window.location.href = "/layout/editbanner?bannerid=" + id;
}
parent.fancyBox($('.image-browers-span'),{
            openEffect: 'none',
            closeEffect: 'none',
            prevEffect: 'none',
            nextEffect: 'none',
            centerOnScroll: true,
            closeBtn: false,
            helpers:
                {
                    buttons:
                        {
                            position: 'bottom'
                        }
                },
            afterLoad: function () {
                this.title = '第 ' + (this.index + 1) + ' 张, 共 ' + this.group.length + ' 张' + (this.title ? ' - ' + this.title : '');
            }
        });
</script>