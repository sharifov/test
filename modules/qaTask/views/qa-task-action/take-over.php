<?php

use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskTakeOverForm $model */
/** @var View $this */

$formId = 'qa-task-take-over-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/take-over', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'reasonId')->dropDownList($model->getReasonList(), ['prompt' => 'Select reason']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Take Over', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$reasonId = Html::getInputId($model, 'reasonId');
$description = Html::getInputId($model, 'description');

$js = <<<JS

(function () {
    var reason = $('#{$reasonId}');
    var description = $('#{$description}');
    
    reason.on('change', function () {
        resetForm();
    });
    
    description.on('input',function(e){
        resetForm();
    });
    
    function resetForm() {
        $("#{$formId}").find(".alert.alert-danger").hide();
        $("#{$formId} .is-invalid").each(function (index, el) {
            $(el).removeClass('is-invalid');
        });
        $("#{$formId}").find('.invalid-feedback').html('');
    }
})()

JS;

$this->registerJs($js);
