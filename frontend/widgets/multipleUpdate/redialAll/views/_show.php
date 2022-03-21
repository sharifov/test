<?php

use common\models\Lead;
use common\models\Project;
use frontend\widgets\multipleUpdate\redialAll\UpdateAllForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var  $updateForm UpdateAllForm */
/** @var  $validationUrl string */
/** @var  $action string */
/** @var  $this View */
/** @var  $modalId string */
/** @var $script string */

$form = ActiveForm::begin([
    'id' => 'update-all-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'validationUrl' => $validationUrl,
    'action' => $action,
]);

?>

    <?= $form->errorSummary($updateForm) ?>

    <?= $form->field($updateForm, 'projectId')->dropDownList(Project::getList(), ['prompt' => 'Select project']) ?>

    <?= $form->field($updateForm, 'statusId')->dropDownList(Lead::STATUS_LIST, ['prompt' => 'Select status']) ?>

    <div class="update-all-form-remove-wrapper">

        <?= $form->field($updateForm, 'weight')->textInput(['type' => 'number']) ?>

    </div>

    <?= $form->field($updateForm, 'remove')->dropDownList(
        [0 => 'No', 1 => 'Yes'],
        ['onChange' => 'let remove = $(this).val(); if (remove == 1) $(".update-all-form-remove-wrapper").hide(); else $(".update-all-form-remove-wrapper").show();']
    ) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Update', ['class' => 'btn btn-info']) ?>
    </div>

<?php
ActiveForm::end();

$js = <<<JS
$('body').find('#update-all-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            $('#{$modalId}').modal('toggle');
            if (data.success) {
                let text = 'Success';
                if (data.text) {
                    text = data.text;
                }
                createNotifyByObject({title: 'Update all', text: text, type: 'info'});
            } else {
                let text = 'Error. Try again later.';
                if (data.text) {
                    text = data.text;
                }
                createNotifyByObject({title: 'Update all', text: text, type: 'error'});
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
