<?php
/**
 * @var $form ActiveForm
 * @var $this View
 * @var $leadPreferencesForm LeadPreferencesForm
 * @var $gid string
 */

use sales\forms\lead\LeadPreferencesForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;


?>
<div class="edit-name-modal-content-ghj">
	<?php $form = ActiveForm::begin([
		'id' => 'lead-preferences-edit-form',
		'action' => Url::to(['lead-view/ajax-edit-lead-preferences', 'gid' => $gid]),
		'enableClientValidation' => true,
		'enableAjaxValidation' => false,
		'validateOnChange' => false,
		'validateOnBlur' => false,
		'validationUrl' => Url::to(['lead-view/ajax-edit-lead-preferences-validation'])
	]);
	?>

	<?= $form->errorSummary($leadPreferencesForm) ?>

    <div class="row">
    <div class="col-md-6">
	    <?= $form->field($leadPreferencesForm, 'marketPrice')->input('number', ['min' => 0, 'max' => 99000]) ?>
    </div>
    <div class="col-md-6">
	    <?= $form->field($leadPreferencesForm, 'clientsBudget')->input('number', ['min' => 0, 'max' => 99000]) ?>
    </div>
    </div>
    <div class="row">
    <div class="col-md-6">
	<?= $form->field($leadPreferencesForm, 'numberStops')->dropDownList(\sales\helpers\lead\LeadPreferencesHelper::listNumberStops(), ['prompt' => '-']) ?>
    </div>
    </div>

    <div class="row">
    <div class="col-md-12">
	<?= $form->field($leadPreferencesForm, 'delayedCharge')->checkbox() ?>
    </div>
    </div>

    <div class="row">
    <div class="col-md-12">
	<?= $form->field($leadPreferencesForm, 'notesForExperts')->textarea([
        'style' => 'resize: vertical;',
        'rows' => 8
    ]) ?>
    </div>
    </div>

	<div class="text-center">
		<?= Html::submitButton('<i class="fa fa-check-square-o"></i> Update Lead Preferences', [
			'class' => 'btn btn-warning'
		])
		?>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#lead-preferences-edit-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            var type = 'error',
                text = data.message,
                title = 'Lead preferences error';
       
            if (!data.error) {
                $('#lead-preferences').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                type = 'success';
                title = 'Lead preferences successfully updated';
            }
            
            new PNotify({
                title: title,
                text: data.message,
                type: type
            });
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