<?php

use dosamigos\datetimepicker\DateTimePicker;
use frontend\widgets\multipleUpdate\redial\MultipleUpdateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/** @var string $script */
/** @var string $gridId */
/** @var string $actionUrl */
/** @var string $validationUrl */
/** @var string $reportWrapperId */

?>
    <p>
        <?= Html::button('<i class="fa fa-edit"></i> Multiple update', ['class' => 'btn btn-info multiple-update-btn']) ?>
    </p>

<?php

Modal::begin([
    'id' => 'multiple-update-modal',
    'header' => '<b>Multiple update</b>',
//         'toggleButton' => ['label' => 'click me'],
    // 'size' => 'modal-lg',
]);
?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">

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
                        <?= $form->field($updateForm, 'attempts')->textInput() ?>
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

                </div>
            </div>
        </div>
    </div>

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
                new PNotify({title: 'Multiple update', text: '', type: 'info'});
                let report = formatReport(data.report); 
                $('#{$reportWrapperId}').html(report);
                {$script}
            } else {
                new PNotify({title: 'Multiple update', text: 'Error. Try again later.', type: 'error'});
            }
       },
       error: function (error) {
           $('#multiple-update-modal').modal('toggle');
            new PNotify({title: 'Error', text: 'Internal Server Error. Try again later.', type: 'error'});
       }
    })
    return false;
}); 

function formatReport(report)
{
    return '<br> ' +
     '<div class="panel panel-default fade in collapse" id="multiple-update-panel">' +
        '<div class="panel-heading">Multiple update' +
            '<button type="button" class="close" data-target="#multiple-update-panel" data-dismiss="alert"> ' +
                '<span aria-hidden="true">&times;</span> <span class="sr-only">Close</span>' +
             '</button>' +
         '</div> ' +
         '<div class="panel-body">' + report + '</div>' +
     '</div>';
}

$('body').on('click', '.multiple-update-btn', function(e) {
        let ids = $('body').find('#{$gridId}').yiiGridView('getSelectedRows');
        // console.log(ids);
        if (ids.length < 1) {
            new PNotify({title: "Multiple update", type: "error", text: 'Not selected rows', hide: true});
            return;
        }
        $('#multipleupdateform-ids').val(ids);
        $('#multiple-update-modal').modal();        
});

JS;
$this->registerJs($js);
