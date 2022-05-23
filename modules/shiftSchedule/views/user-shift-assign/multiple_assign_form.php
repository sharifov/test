<?php

use common\models\Employee;
use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\forms\UserShiftMultipleAssignForm;
use src\dictionary\ActionDictionary;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var UserShiftMultipleAssignForm $userShiftMultipleAssignForm */
/* @var ActiveForm $form */

?>

<div class="user-shift-assign">
    <div class="col-md-12">

        <?php $form = ActiveForm::begin([
            'id' => 'user-shift-assign-form',
            'options' => ['data-pjax' => 0, 'class' => ''],
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validationUrl' => Url::to(['/shift/user-shift-assign/multiple-assign-validation'])
        ]); ?>

        <?= $form->errorSummary($userShiftMultipleAssignForm) ?>

        <?php echo $form->field($userShiftMultipleAssignForm, 'userIds', ['options' => []])->widget(Select2::class, [
            'data' => Employee::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>

        <?php echo $form->field($userShiftMultipleAssignForm, 'shftIds', ['options' => []])->widget(Select2::class, [
            'data' => Shift::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select Shift', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>

        <?= $form->field($userShiftMultipleAssignForm, 'formAction')->dropDownList(UserShiftMultipleAssignForm::ACTION_LIST) ?>
        <br />
        <br />
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success js_user_shift_assign_btn']) ?>
        </div>

        <?php
            ActiveForm::end();
        ?>
    </div>
</div>

<?php
$multipleAssignUrl = Url::to(['/shift/user-shift-assign/multiple-assign']);
$js = <<<JS

var multipleAssignUrl = '$multipleAssignUrl';

$('#user-shift-assign-form').on('beforeSubmit', function (e) {
    e.preventDefault();

    let btnSave = $('.js_user_shift_assign_btn');
    let btnContent = btnSave.html();
    btnSave.html('<i class="fa fa-cog fa-spin"></i> Processing')
        .addClass('btn-default')
        .prop('disabled', true);

    $.ajax({
       type: 'POST',
       url: multipleAssignUrl,
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(dataResponse) {
            if (dataResponse.status === 1) {
                $.pjax.reload({container: '#pjax-user-shift-assign', timeout: 10000, async: false});
                
                $('#multiple_assign_modal').modal('hide');
                createNotify('Success', dataResponse.message, 'success');
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            setTimeout(function () {
                btnSave.html(btnContent).removeClass('btn-default').prop('disabled', false);  
            }, 1000);
       },
       error: function (error) {
            createNotify({title: 'Error', text: 'Internal Server Error. Try again letter.', type: 'error'});
            setTimeout(function () {
                btnSave.html(btnContent).removeClass('btn-default').prop('disabled', false);  
            }, 1000);
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
