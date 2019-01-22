<?php

use backend\components\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $form = ActiveForm::begin(['method' => 'post', 'action' => Url::to(['/layout/editlabel']), 'options' => ['style' => 'margin-top:10px;']]); ?>
<?= Html::hiddenInput('id', $model->id) ?>
<table class="table table-bordered table-hover m10">
    <tr>
        <td class="tableleft">标签名</td>
        <td>
            <?= $form->field($model, 'name')->textInput(); ?>
        </td>
    </tr>
    <tr>
        <td class="tableleft">标签介绍</td>
        <td>
            <?= $form->field($model, 'intro')->textarea(); ?>
        </td>
    </tr>
    <tr>
        <td class="tableleft">父级标签</td>
        <td>
            <?= $form->field($model, 'pid')->dropDownList($p_label) ?>
        </td>
    </tr>
    <tr>
        <td class="tableleft">状态</td>
        <td>
            <?= $form->field($model, 'status')->radioList($status); ?>
        </td>
    </tr>
    <tr>
        <td class="tableleft">是否热门</td>
        <td>
            <?= $form->field($model, 'is_hot')->radioList($hotStatus); ?>
    </tr>
    <tr>
        <td class="tableleft"></td>
        <td>
            <?= Html::submitButton('保存', ['class' => 'btn btn-primary']); ?>
            <button type="button" class="btn btn-success" name="backid" id="backid">返回列表</button>
        </td>
    </tr>
</table>

<?php ActiveForm::end(); ?>
