<?php

use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskCancelForm $model */
/** @var View $this */

$formId = 'qa-task-cancel-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/cancel', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'reasonId')->dropDownList($model->getReasonList(), ['prompt' => 'Select reason']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$reasonId = Html::getInputId($model, 'reasonId');
$description = Html::getInputId($model, 'description');

$js = <<<JS

(function () {
    let reason = $('#{$reasonId}');
    let description = $('#{$description}');
    let form = $("#{$formId}");
    
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
