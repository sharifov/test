<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

    <h4>Receive SMS From communication service</h4>
<?php $form = ActiveForm::begin([
    'action' => ['/sms/inbox'],
    'method' => 'post',
    'id' => 'smsInboxForm',
    'options' => [
        'data-pjax' => 1
    ],
]); ?>
    <div class="col-md-3">
        <?= $form->field($model, 'action')->dropDownList([
            'all' => 'All items', 'last_id' => 'By latest ID', 'last_date' => 'From date', 'last_n' => 'Latest num'
        ], ['id' => 'action'])->label('Select Conditions') ?>

        <div class="form-group">
            <?= Html::button('Receive sms', ['class' => 'btn btn-primary', 'id' => 'receiveSmsBtn']) ?>
        </div>
    </div>
    <div class="col-md-3">
        <div id="last_n_area">
            <?= $form->field($model, 'last_n')->textInput(['class' => 'form-control']) ?>
        </div>
        <div id="last_date_area">
            <?= $form->field($model, 'last_date')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => true
                ]

            ]); ?></div>
    </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$js = <<<JS

    $(document).ready(function() {
         $("#last_n_area").val('').hide();
         $("#last_date_area").val('').hide();
         $("#action").val('all');
    });

     $(document).on('change','#action', function (e) {
        e.preventDefault();
        $("#last_n_area").val('').hide();
        $("#last_date_area").val('').hide();
        var action_val = $(this).val();

        if(action_val == 'last_date') {
            $("#last_date_area").show();
        }
        if(action_val == 'last_n') {
            $("#last_n_area").show();
        }
    });

    $(document).on('click','#receiveSmsBtn', function (e) {
        e.preventDefault();
        var act_id = $("#action").val();
        $("#smsInboxForm").submit();
    });



JS;
$this->registerJs($js);

?>