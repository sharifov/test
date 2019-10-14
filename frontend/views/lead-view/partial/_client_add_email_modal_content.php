<?php
/**
 * @var $form ActiveForm
 * @var $this View
 * @var $lead Lead
 * @var $addEmail EmailCreateForm
 */

use common\models\ClientEmail;
use common\models\Lead;
use sales\forms\lead\EmailCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
$addEmail->client_id = $lead->client_id;
?>

<div class="edit-email-modal-content-ghj">
	<?php $form = ActiveForm::begin([
		'id' => 'client-add-email-form',
		'action' => Url::to(['lead-view/ajax-add-client-email', 'gid' => $lead->gid]),
		'enableClientValidation' => false,
		'enableAjaxValidation' => true,
		'validateOnChange' => false,
		'validateOnBlur' => false,
		'validationUrl' => Url::to(['lead-view/ajax-add-client-email-validation', 'gid' => $lead->gid])
	]); ?>

	<?= $form->errorSummary($addEmail); ?>

	<? if ($lead->isOwner(Yii::$app->user->id) || $user->isAnySupervision() || $user->isAdmin() || $user->isSuperAdmin()): ?>

        <?=
        $form->field($addEmail, 'email', [
        'template' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}',
        'options' => [
        'class' => 'form-group'
        ]
        ])->textInput([
        'class' => 'form-control email lead-form-input-element',
        'type' => 'email'
        ]);
        ?>
	<? endif; ?>

	<?=
	$form->field($addEmail, 'type')->dropDownList(ClientEmail::EMAIL_TYPE);
	?>
	<?= Html::submitButton('Submit', [
		'class' => 'btn btn-warning'
	]);
	?>
	<?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-add-email-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#pjax-client-manage-email').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                new PNotify({
                    title: 'Email successfully added',
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
