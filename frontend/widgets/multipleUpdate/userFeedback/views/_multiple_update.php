<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var  $updateForm \frontend\widgets\multipleUpdate\userFeedback\MultipleUpdateForm */
/** @var  $validationUrl string */
/** @var  $action string */
/** @var  $modalId string */
/** @var  $pjaxId string */
/** @var  $script string */
/** @var  $formId string */
/** @var  $buttonHeader string */
/** @var  $notifyHeader string */
/** @var  $summaryIdentifier string */
/** @var  $this View */

$form = ActiveForm::begin([
    'id' => $formId,
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'validationUrl' => $validationUrl,
    'action' => $action,
]);

?>

<?= $form->errorSummary($updateForm) ?>

<?= $form->field($updateForm, 'ids', [
    'template' => '{input}',
    'options' => ['tag' => false]
])->hiddenInput()->label(false) ?>

    <div>

        <?= $form->field($updateForm, 'statusId')->dropDownList($updateForm->statusList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($updateForm, 'typeId')->dropDownList($updateForm->typeList(), ['prompt' => 'Select type']) ?>

    </div>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> ' . $buttonHeader, ['class' => 'btn btn-info']) ?>
    </div>

<?php
ActiveForm::end();

$statusId = Html::getInputId($updateForm, 'statusId');

$js = <<<JS

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
                createNotifyByObject({title: '{$notifyHeader}', text: message, type: 'info'});
            } else {
                message = 'Error. Try again later.';
                if (data.message) {
                    message = data.message;
                }
                createNotifyByObject({title: '{$notifyHeader}', text: message, type: 'error'});
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
JS;
$this->registerJs($js);
