<?php

use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskCloseForm $model */
/** @var View $this */

$formId = 'qa-task-close-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => ['/qa-task/qa-task-action/close', 'gid' => $model->getTaskGid()],
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'rating')->dropDownList($model->getRatingList(), ['prompt' => 'Select rating']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$rating = Html::getInputId($model, 'rating');
$description = Html::getInputId($model, 'description');

$js = <<<JS

(function () {
    let rating = $('#{$rating}');
    let description = $('#{$description}');
    let form = $("#{$formId}");
    
    rating.on('change', function () {
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
