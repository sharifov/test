<?php

use modules\qaTask\src\useCases\qaTask\returnTask\QaTaskReturnForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskReturnForm $model */
/** @var View $this */

$formId = 'qa-task-return-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/return', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'statusId')->dropDownList($model->getStatusList(), ['prompt' => 'Select status']) ?>

    <?= $form->field($model, 'reasonId')->dropDownList($model->getReasonList(), ['prompt' => 'Select reason']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$statusId = Html::getInputId($model, 'statusId');
$reasonId = Html::getInputId($model, 'reasonId');
$description = Html::getInputId($model, 'description');

$js = <<<JS

(function () {
    let status = $('#{$statusId}');
    let reason = $('#{$reasonId}');
    let description = $('#{$description}');
    let form = $("#{$formId}");
    
    status.on('change', function () {
        resetForm();
    });
    
    reason.on('change', function () {
        resetForm();
    });
    
    description.on('input',function(e){
        resetForm();
    });
    
    function resetForm() {
        form.find(".alert.alert-danger").hide();
        form.find(".is-invalid").each(function (index, el) {
            $(el).removeClass('is-invalid');
        });
        form.find('.invalid-feedback').html('');
    }
})()

JS;

$this->registerJs($js);
