<?php

use modules\qaTask\src\useCases\qaTask\decide\lead\reAssign\QaTaskDecideLeadReAssignForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskDecideLeadReAssignForm $model */
/** @var View $this */

$formId = 'qa-task-decide-lead-re-assign-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/decide-lead-re-assign', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'assignUserId')->dropDownList($model->getUserList(), ['prompt' => 'Select employee']) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$assignUser = Html::getInputId($model, 'assignUserId');

$js = <<<JS

(function () {
    let assignUser = $('#{$assignUser}');
    let form = $("#{$formId}");
    
    assignUser.on('change', function () {
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
