<?php

use modules\qaTask\src\useCases\qaTask\create\manually\QaTaskCreateManuallyForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskCreateManuallyForm $model */
/** @var View $this */

$formId = 'qa-task-create-lead-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-create/lead', 'objectId' => $model->objectId],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'categoryId')->dropDownList($model->getCategoryList(), ['prompt' => 'Select category']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$categoryId = Html::getInputId($model, 'categoryId');
$description = Html::getInputId($model, 'description');

$js = <<<JS

(function () {
    let category = $('#{$categoryId}');
    let description = $('#{$description}');
    let form = $("#{$formId}");
    
    category.on('change', function () {
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
