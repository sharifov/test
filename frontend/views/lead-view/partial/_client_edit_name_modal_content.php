<?php
/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editName ClientCreateForm
 * @var $lead Lead
 */

use common\models\Lead;
use sales\forms\lead\ClientCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
?>

<div class="edit-name-modal-content-ghj">
	<?php $form = ActiveForm::begin([
		'id' => 'client-edit-name-form',
		'action' => Url::to(['lead-view/ajax-edit-client-name', 'gid' => $lead->gid]),
		'enableClientValidation' => false,
		'enableAjaxValidation' => true,
		'validateOnChange' => false,
		'validateOnBlur' => false,
		'validationUrl' => Url::to(['lead-view/ajax-edit-client-name-validation', 'gid' => $lead->gid])
	]);
	?>

	<?= $form->errorSummary($editName); ?>

	<?= $form->field($editName, 'firstName')->textInput() ?>

	<?= $form->field($editName, 'lastName')->textInput() ?>

	<?= $form->field($editName, 'middleName')->textInput() ?>

	<?=
	$form->field($editName, 'id')->hiddenInput()->label(false)->error(false);
	?>

	<?= Html::submitButton('Submit', [
		'class' => 'btn btn-warning'
	]);
	?>
	<?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-edit-name-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#pjax-client-manage-name').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                new PNotify({
                    title: 'User name successfully updated',
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
