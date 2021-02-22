<?php

use common\models\Employee;
use dosamigos\datetimepicker\DateTimePicker;
use sales\auth\Auth;
use sales\model\userClientChatData\service\UserClientChatDataService;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;

/* @var yii\web\View $this */
/* @var sales\model\userClientChatData\entity\UserClientChatData $model */
/* @var ActiveForm $form */
?>

<div class="user-client-chat-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

            <?php $form->errorSummary($model) ?>

            <?php echo $form->field($model, 'uccd_employee_id')
                ->dropDownList(UserClientChatDataService::getUserList(), ['id' => 'employee_id', 'prompt' => '---']) ?>

            <?php echo $form->field($model, 'uccd_name')
                ->textInput(['maxlength' => true, 'title' => 'Display name of the user', 'id' => 'rcName']) ?>

            <?php echo $form->field($model, 'uccd_username')
                ->textInput(['maxlength' => true, 'title' => 'Username for the user', 'id' => 'rcUserName']) ?>

            <?php echo $form->field($model, 'uccd_password')->passwordInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?php echo Html::submitButton('<i class="fa fa-rocket"></i> Save and register to Rocket Chat', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$getUserUrl = Url::to(['/user-client-chat-data/user-info']);

$js = <<<JS

    $(document).on('change', '#employee_id', function(e) { 
        e.preventDefault();
        
        $.ajax({
            url: '{$getUserUrl}',
            type: 'POST',
            data: {id: $(this).val()},
            dataType: 'json'    
        })
        .done(function(dataResponse) {
            
            if (dataResponse.name) {
                $('#rcName').val(dataResponse.name);
            }
            if (dataResponse.username) {
                $('#rcUserName').val(dataResponse.username);
            }     
        })
        .fail(function(error) {
            createNotify('Error', 'Server error. Please try again later', 'error');
        })
        .always(function() {
        });        
    });
JS;
$this->registerJs($js);
