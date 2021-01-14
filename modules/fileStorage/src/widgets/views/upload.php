<?php

use modules\fileStorage\src\useCase\uploadFile\UploadForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var $form UploadForm */
/** @var $this View */
/** @var $url string */

$idForm = 'file-storage-upload-form-id';

?>

    <div class="x_panel">
        <div class="x_title">
            <h2>Upload file</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none;">
            <?php $activeForm = ActiveForm::begin([
                'id' => $idForm,
                'action' => $url,
            ]) ?>
                <?= $activeForm->field($form, 'file')->fileInput() ?>
                <?= Html::submitButton('Upload', ['class' => 'file-storage-upload-btn btn btn-success']) ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>

<?php

$fileId = Html::getInputId($form, 'file');
$fileName = Html::getInputName($form, 'file');

$js = <<<JS
$('#{$idForm}').on('beforeSubmit', function (e) {
    e.preventDefault();
    let yiiform = $(this);
    let file = $('#{$fileId}').prop('files')[0];
    let formData = new FormData();
    formData.append('{$fileName}', file);
    fileStorageUploadButtonDisable();
    $.ajax({
        url: yiiform.attr('action'),
        type: yiiform.attr('method'),
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(data) {
        if (data.error) {
            if (data.message) {
                createNotify('Upload file', data.message, 'error');
            } else {
                yiiform.yiiActiveForm('updateAttribute', '{$fileId}', data.errors.file);
            }
        } else {
            createNotify('Upload file', 'Success', 'success');
        }
        $('#{$fileId}').val('');
        fileStorageUploadButtonEnable();
    })
    .fail(function() {
        createNotify('Upload file', 'Server error', 'error');
        fileStorageUploadButtonEnable();
    });
    return false;
});

function fileStorageUploadButtonDisable() {
    $('.file-storage-upload-btn')
        .prop('disabled', 'disabled')
        .removeClass('btn-success')
        .addClass('btn-default')
        .html('<i class="fa fa-spinner fa-spin"></i> Upload');
}

function fileStorageUploadButtonEnable() {
    $('.file-storage-upload-btn')
        .prop('disabled', false)
        .removeClass('btn-default')
        .addClass('btn-success')
        .html('Upload');
}
JS;

$this->registerJs($js);
