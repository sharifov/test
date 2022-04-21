<?php

use src\model\cases\useCases\cases\updateInfo\UpdateInfoForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model UpdateInfoForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $categoryList [] */

?>
<?php Pjax::begin(['id' => 'pjax-cases-update-form']); ?>
<div class="cases-change-status">
    <?php $form = ActiveForm::begin([
        'action' => ['/cases/ajax-update', 'gid' => $model->getCaseGid()],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
    echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'depId')->dropDownList($model->getdepartmentList(), [
        'prompt' => '-',
        'disabled' => !$model->fieldAccess->canEditDepartment(),
        'onchange' => $model->fieldAccess->canEditCategory() ?
            '$( "#' . Html::getInputId($model, 'categoryId') . '").prop("disabled", true);
            $.get( "' . Url::to(['/cases/get-categories']) . '", { id: $(this).val() } )
                .done(function( data ) {
                    $( "#' . Html::getInputId($model, 'categoryId') . '" ).html( data );
                    $( "#' . Html::getInputId($model, 'categoryId') . '").prop("disabled", false);
                }
            );
        ' : '$( "#' . Html::getInputId($model, 'categoryId') . '").prop("disabled", true);'
    ]); ?>

    <?= $form->field($model, 'categoryId')->dropDownList($model->getCategoryList(), [
        'prompt' => '-',
        'disabled' => !$model->fieldAccess->canEditCategory(),
    ]); ?>

    <?= $form->field($model, 'orderUid')->textInput(['maxlength' => 7]) ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 4, 'disabled' => !$model->fieldAccess->canEditDescription()]); ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Update', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php if ($model->fieldAccess->canEditDepartment() && (count($model->getdepartmentList()) === 1)) {
    $keyDepartment = array_key_first($model->getdepartmentList());
    $js = '$( "#' . Html::getInputId($model, 'depId') . '" ).val("' . $keyDepartment . '").change();';
    $this->registerJs($js);
} ?>
<?php Pjax::end(); ?>
