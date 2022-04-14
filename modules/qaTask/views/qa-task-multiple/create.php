<?php

use modules\qaTask\src\useCases\qaTask\multiple\create\QaTaskMultipleCreateForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var QaTaskMultipleCreateForm $model */
/** @var View $this */
/** @var string $summaryIdentifier */
/** @var string $modalId */
/** @var string $pjaxId */
/** @var string|null $script */
/** @var string|null $actionUrl */
/** @var string|null $validationUrl */

$formId = 'qa-task-create-multiple-form';

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'action' => $actionUrl,
    'validationUrl' => $validationUrl,
]);

?>

    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'ids', [
        'template' => '{input}',
        'options' => ['tag' => false]
    ])->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'categoryId')->dropDownList($model->getCategoryList(), ['prompt' => 'Select category']) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Apply', ['class' => 'btn btn-info']) ?>
    </div>

<?php

ActiveForm::end();

$categoryId = Html::getInputId($model, 'categoryId');

$js = <<<JS

(function () {
    let category = $('#{$categoryId}');
    let form = $("#{$formId}");
    
    category.on('change', function () {
        resetForm();
    });
    
    function resetForm() {
        form.find(".alert.alert-danger").hide();
        form.find(".is-invalid").each(function (index, el) {
            $(el).removeClass('is-invalid');
        });
        form.find('.invalid-feedback').html('');
    }
    
    $('#{$formId}').on('beforeSubmit', function (e) {
        e.preventDefault();
        $.ajax({
           type: $(this).attr('method'),
           url: $(this).attr('action'),
           data: $(this).serializeArray(),
           dataType: 'json',
           success: function(data) {
                $('#{$modalId}').modal('toggle');
                var message = '';
                if (data.success) {
                    message = 'Success';
                    if (data.message) {
                        message = data.message;
                    }
                    createNotifyByObject({title: 'Create QA Tasks', text: message, type: 'info'});
                } else {
                    message = 'Error. Try again later.';
                    if (data.message) {
                        message = data.message;
                    }
                    createNotifyByObject({title: 'Create QA Tasks', text: message, type: 'error'});
                }
                var summary = '{$summaryIdentifier}';
                if (summary && data.text) {
                    $('body').find('{$summaryIdentifier}').html(data.text);
                }
                var pjaxId = '{$pjaxId}';
                if (pjaxId) {
                    var pjax = $('#' + pjaxId); 
                    if (pjax.length) {
                        $.pjax.reload({container: ('#' + pjaxId), async: false}); 
                    }
                }
                {$script}
           },
           error: function (error) {
               $('#{$modalId}').modal('toggle');
               createNotifyByObject({title: 'Error', text: 'Internal Server Error. Try again later.', type: 'error'});
           }
        })
        return false;
    }); 
    
})()

JS;

$this->registerJs($js);
