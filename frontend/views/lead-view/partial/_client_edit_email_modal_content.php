<?php
/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editEmail EmailCreateForm
 * @var $lead Lead
 */

use common\models\ClientEmail;
use common\models\Lead;
use sales\forms\lead\EmailCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
?>

<div class="edit-phone-modal-content-ghj">
	<?php $form = ActiveForm::begin([
		'id' => 'client-edit-email-form',
		'action' => Url::to(['lead-view/ajax-edit-client-email', 'gid' => $lead->gid]),
		'enableClientValidation' => false,
		'enableAjaxValidation' => true,
		'validateOnChange' => false,
		'validateOnBlur' => false,
		'validationUrl' => Url::to(['lead-view/ajax-edit-client-email-validation', 'gid' => $lead->gid])
	]); ?>

	<?= $form->errorSummary($editEmail) ?>

	<?php if ($user->isAdmin() || $user->isSuperAdmin()): ?>
		<?=
		$form->field($editEmail, 'email', [
			'template' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}',
			'options' => [
				'class' => 'form-group'
			]
		])->textInput([
			'class' => 'form-control email lead-form-input-element',
			'type' => 'email',
            'required' => true
		])
		?>
	<?php endif; ?>

	<?=
	$form->field($editEmail, 'type')->dropDownList(ClientEmail::EMAIL_TYPE)
	?>

	<?=
	$form->field($editEmail, 'id')->hiddenInput()->label(false)->error(false)
	?>

	<?=
	$form->field($editEmail, 'client_id')->hiddenInput()->label(false)->error(false)
	?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Save email', [
            'class' => 'btn btn-warning'
        ])
        ?>
    </div>
	<?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-edit-email-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#client-manage-email').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                new PNotify({
                    title: 'Email successfully updated',
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
