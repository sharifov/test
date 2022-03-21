<?php

use dosamigos\datetimepicker\DateTimePicker;
use frontend\widgets\multipleUpdate\redial\MultipleUpdateForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use yii\helpers\Html;

/** @var string $script */
/** @var string $gridId */
/** @var string $actionUrl */
/** @var string $validationUrl */
/** @var string $reportWrapperId */

?>

        <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info multiple-update-btn']) ?>


<?php

Modal::begin([
    'id' => 'multiple-update-modal',
    'title' => 'Multiple update',
]);
?>

    <?php
    $updateForm = new MultipleUpdateForm();
    $form = ActiveForm::begin([
        'id' => 'multiple-update-form',
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => $validationUrl,
        'action' => $actionUrl,
    ]);
    ?>

    <?= $form->errorSummary($updateForm) ?>

    <?= $form->field($updateForm, 'ids', [
        'template' => '{input}',
        'options' => ['tag' => false]
    ])->hiddenInput()->label(false) ?>

    <div class="remove-wrapper">
        <?= $form->field($updateForm, 'attempts')->textInput(['type' => 'number']) ?>
        <?= $form->field($updateForm, 'weight')->textInput(['type' => 'number']) ?>
        <?= $form->field($updateForm, 'created')->widget(DateTimePicker::class, [
            'clientOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd HH:ii',],
            'options' => ['autocomplete' => 'off', 'placeholder' => 'Choose Date'],
        ]) ?>
        <?= $form->field($updateForm, 'from')->widget(DateTimePicker::class, [
            'clientOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd HH:ii',],
            'options' => ['autocomplete' => 'off', 'placeholder' => 'Choose Date'],
        ]) ?>
        <?= $form->field($updateForm, 'to')->widget(DateTimePicker::class, [
            'clientOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd HH:ii',],
            'options' => ['autocomplete' => 'off', 'placeholder' => 'Choose Date'],
        ]) ?>
    </div>
    <?= $form->field($updateForm, 'remove')->dropDownList(
        [0 => 'No', 1 => 'Yes'],
        ['onChange' => 'let remove = $(this).val(); if (remove == 1) $(".remove-wrapper").hide(); else $(".remove-wrapper").show();']
    ) ?>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fa fa-check-square"></i> Update', ['class' => 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end() ?>

<?php Modal::end(); ?>

<?php
$js = <<<JS

$('#multiple-update-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            $('#multiple-update-modal').modal('toggle');
            if (data.success) {
                createNotifyByObject({title: 'Multiple update', text: '', type: 'info'});
                let report = formatReport(data.report); 
                $('#{$reportWrapperId}').html(report);
                {$script}
            } else {
                createNotifyByObject({title: 'Multiple update', text: 'Error. Try again later.', type: 'error'});
            }
       },
       error: function (error) {
           $('#multiple-update-modal').modal('toggle');
            createNotifyByObject({title: 'Error', text: 'Internal Server Error. Try again later.', type: 'error'});
       }
    })
    return false;
}); 

function formatReport(report)
{
    return '' + 
     '<div class="card" id="multiple-update-panel" style="margin-top:10px">' + 
        '<h5 class="card-header"> Multiple update ' +
            ' <span class="pull-right clickable close-icon" data-effect="fadeOut"><i class="fa fa-times"></i></span>' +
         '</h5> ' +
         '<div class="card-body"><div class="card-text"> ' + report + ' </div></div>' +
     '</div>';
}

$('body').on('click', '.multiple-update-btn', function(e) {
        let ids = $('body').find('#{$gridId}').yiiGridView('getSelectedRows');
        // console.log(ids);
        if (ids.length < 1) {
            createNotifyByObject({title: "Multiple update", type: "error", text: 'Not selected rows', hide: true});
            return;
        }
        resetMultipleUpdateForm();
        $('#multipleupdateform-ids').val(ids);
        $('#multiple-update-modal').modal();        
});

function resetMultipleUpdateForm()
{
    $('#multiple-update-form')[0].reset();
    $(".remove-wrapper").show();
}

$('body').on('click', '.close-icon' ,function() {
  $(this).closest('.card').fadeOut();
})

JS;
$this->registerJs($js);
