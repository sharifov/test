<?php
/**
 * @var $this \yii\web\View
 * @var $model Project
 * @var $template ProjectEmailTemplate
 * @var $types []
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\models\Project;
use common\models\ProjectEmailTemplate;

$url = \yii\helpers\Url::to([
    'settings/email-template',
    'id' => $model->id,
    'templateId' => $template->id
]);
$idForm = sprintf('%s_ID', $template->formName());

$js = <<<JS
    $('#SourceEmailTemplates_type').change(function(e) {
        var url = '$url' + '&type=' + $(this).val();
        var editBlock = $('#modal-email-templates');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {

        });
    });

    $('#save-template').click(function() {
        var form = $('#$idForm');
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: form.serialize(),
            success: function (data) {
                console.log(data);		
                var editBlock = $('#modal-email-templates');
                if (!data.success) {
                    editBlock.find('.modal-body').html(data.body);
                } else {
                    editBlock.modal('hide');
                }
            },
            error: function (error) {			
                console.log('Error: ' + error);			
            }
        });
    });
JS;

$this->registerJs($js);

?>

<div>
    <?php $form = ActiveForm::begin([
        'successCssClass' => '',
        'id' => $idForm
    ]) ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($template, 'type')
                ->dropDownList($types, [
                    'prompt' => 'Select type',
                    'disabled' => !$template->isNewRecord
                ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($template, 'layout_path')
                ->dropDownList(ProjectEmailTemplate::getEmailsLayout(), [
                    'prompt' => 'Select layout',
                ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($template, 'subject')->textInput() ?>
        </div>
    </div>
    <?= $form->field($template, 'template')->textarea([
        'style' => 'resize: none;',
        'rows' => 20
    ]) ?>
    <div class="btn-wrapper modal-footer">
        <?= Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Cancel', [
            'data-dismiss' => 'modal',
            'class' => 'btn btn-danger'
        ]) ?>
        <?= Html::button('<i class="fa fa-save"></i> Save', [
            'id' => 'save-template',
            'class' => 'btn btn-primary'
        ]) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>


