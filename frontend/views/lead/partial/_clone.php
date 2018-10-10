<?php
/**
 * @var $lead \common\models\Lead
 * @var $errors []
 */
use yii\helpers\Html;

$js = <<<JS
$('#clone-form').on('beforeSubmit', function () {
    if ($('#lead-description').val() == 0 && $('#clone-other').val() == '') {
        $('#clone-other').parent().addClass('has-error');
        return false
    }
    return true;
});

$('#cancel-btn').click(function (e) {
    e.preventDefault();
    $('#modal-error').modal('hide');
});
JS;
$this->registerJs($js);?>
<?php if(!empty($errors)):?>
<div class="alert alert-danger">Some errors happened! <br/>
<?php foreach ($errors as $error):?> <?= $error;?> <?php endforeach;?>
</div>
<?php endif;?>
<?php
$cloneForm = \yii\widgets\ActiveForm::begin([
    'id' => 'clone-form'
])?>
	<?= $cloneForm->field($lead, 'description', ['template' => '{label}<div class="select-wrap-label">{input}</div>'])->dropDownList(\common\models\Lead::CLONE_REASONS, ['prompt' => 'Select reason','onchange' => "
        var val = $(this).val();
        if (val == 0) {
            $('#other-wrapper').addClass('in');
        } else {
            $('#other-wrapper').removeClass('in');
        }
    "])?>
    <div class="form-group collapse" id="other-wrapper">
        <textarea rows="5" class="form-control" id="clone-other" name="other"></textarea>
    </div>
	<div class="btn-wrapper">
        <?=Html::button('<span class="btn-icon"><i class="glyphicon glyphicon-remove-circle"></i></span><span>Cancel</span>', ['id' => 'cancel-btn','class' => 'btn btn-danger btn-with-icon'])?>
        <?=Html::submitButton('<span class="btn-icon"><i class="fa fa-save"></i></span><span>Confirm</span>', ['id' => 'save-btn','class' => 'btn btn-primary btn-with-icon'])?>
    </div>
<?php \yii\widgets\ActiveForm::end() ?>