<?php

/* @var LeadUserConversionAddForm $leadUserConversionAddForm
 * @var int $leadId
 * @var array $userList
*/

use common\components\bootstrap4\activeForm\ClientBeforeSubmit;
use common\models\Employee;
use src\auth\Auth;
use src\model\leadUserConversion\form\LeadUserConversionAddForm;
use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

?>

<div class="form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'lead-user-conversion-add-form',
        'action' => ['/lead-user-conversion/add', 'lead_id' => $leadId],
        'clientBeforeSubmit' => new ClientBeforeSubmit(
            'Add conversion',
            true,
            'modal-sm',
            'if ($("#pjax-user-conversation-list").length) {pjaxReload({container:"#pjax-user-conversation-list"})}',
            null,
            null,
            'lead-user-conversion-add_submit_btn'
        ),
        'enableAjaxValidation' => false,
    ]);
    ?>

    <?php echo $form->field($leadUserConversionAddForm, 'leadId')->hiddenInput()->label(false) ?>

    <?php echo $form->field($leadUserConversionAddForm, 'userId')
        ->dropDownList($userList, ['prompt' => '-', 'required' => 'required'])
        ->label('Employee') ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Add', ['class' => 'btn btn-success', 'id' => 'lead-user-conversion-add_submit_btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
