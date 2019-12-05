<?php

use common\models\Department;
use common\models\Lead;
use common\models\Sources;
use frontend\assets\EditToolAsset;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var  $this View */
/** @var  $editForm \frontend\widgets\lead\editTool\Form */
/** @var  $modalId string */

$form = ActiveForm::begin([
    'id' => 'edit-lead-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'validationUrl' => ['/leads/edit-validation'],
    'action' => ['/leads/edit'],
]);

$departments = Department::DEPARTMENT_LIST;
unset($departments[Department::DEPARTMENT_SUPPORT]);

?>

<?= $form->errorSummary($editForm) ?>

<?= Html::hiddenInput('id', $editForm->leadId) ?>

<?= $form->field($editForm, 'client_id')->textInput() ?>
<?= $form->field($editForm, 'source_id')->dropDownList(Sources::getList()) ?>
<?= $form->field($editForm, 'request_ip')->textInput() ?>
<?= $form->field($editForm, 'offset_gmt')->textInput() ?>
<?= $form->field($editForm, 'discount_id')->textInput() ?>
<?= $form->field($editForm, 'final_profit')->textInput() ?>
<?= $form->field($editForm, 'tips')->textInput() ?>
<?= $form->field($editForm, 'l_call_status_id')->dropDownList(Lead::CALL_STATUS_LIST) ?>
<?= $form->field($editForm, 'l_duplicate_lead_id')->textInput() ?>
<?= $form->field($editForm, 'l_dep_id')->dropDownList($departments) ?>

<div class="form-group text-right">
    <?= Html::submitButton('<i class="fa fa-check-square"></i> Update', ['class' => 'btn btn-info']) ?>
</div>

<?php

ActiveForm::end();

EditToolAsset::register($this);

$js =<<<JS
$('body').find('#edit-lead-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    var script = "var pjaxId = 'pjax-leads-view'; var pjax = $('#' + pjaxId); if (pjax.length) { $.pjax.reload({container: '#' + pjaxId, async: false}); }";
    editToolSend($(this), '{$modalId}', script, 'Edit', true)
    return false;
}); 

JS;
$this->registerJs($js);
