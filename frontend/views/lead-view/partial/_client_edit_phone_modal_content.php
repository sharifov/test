<?php
/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editPhone PhoneCreateForm
 * @var $lead Lead
 */

use borales\extensions\phoneInput\PhoneInput;
use common\models\ClientPhone;
use common\models\Lead;
use sales\forms\lead\PhoneCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
?>

<div class="edit-phone-modal-content-ghj">
	<?php $form = ActiveForm::begin([
		'id' => 'client-edit-phone-form',
		'action' => Url::to(['lead-view/ajax-edit-client-phone', 'gid' => $lead->gid]),
		'enableClientValidation' => false,
		'enableAjaxValidation' => true,
		'validateOnChange' => false,
		'validateOnBlur' => false,
		'validationUrl' => Url::to(['lead-view/ajax-edit-client-phone-validation', 'gid' => $lead->gid])
	]); ?>

	<?= $form->errorSummary($editPhone); ?>

	<? if ($lead->isOwner(Yii::$app->user->id) || $user->isAnySupervision() || $user->isAdmin() || $user->isSuperAdmin()): ?>
		<?= $form->field($editPhone, 'phone', [
			'options' => [
				'class' => 'form-group',
			],
		])->widget(PhoneInput::class, [
			'options' => [
				'class' => 'form-control lead-form-input-element',
				'id' => 'edit-phone'
			],
			'jsOptions' => [
				'nationalMode' => false,
				'preferredCountries' => ['us'],
			]
		]); ?>
	<? endif; ?>

	<?=
	$form->field($editPhone, 'type')->dropDownList(ClientPhone::PHONE_TYPE);
	?>

	<?=
	$form->field($editPhone, 'id')->hiddenInput()->label(false)->error(false);
	?>

	<?=
	$form->field($editPhone, 'client_id')->hiddenInput()->label(false)->error(false);
	?>

	<?= Html::submitButton('Submit', [
		'class' => 'btn btn-warning'
	]);
	?>
	<?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-edit-phone-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#client-manage-phone').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                new PNotify({
                    title: 'Phone successfully updated',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            new PNotify({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>
