<?php

use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\useCase\uploadFile\UploadForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use kartik\file\FileInput;
use yii\bootstrap4\Modal;

/** @var UploadForm $form */
/** @var View $this */
/** @var string $url */

$idForm = 'file-storage-upload-form-id';
$modalId = 'file-input-modal';
?>

<?php Modal::begin([
    'id' => $modalId,
    'title' => 'File Input inside Modal',
    'toggleButton' => [
        'label' => '<i class="fa fa-plus-circle success"></i> Upload files',
        'class' => 'btn upload-modal-btn',
    ],
]); ?>
    <?php $activeForm = ActiveForm::begin([
        'id' => $idForm,
        'action' => $url,
        'options' => ['enctype' => 'multipart/form-data'],
    ]) ?>
        <?php echo $activeForm->field($form, 'files[]')->widget(FileInput::class, [
            'options' => [
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowedFileExtensions' => array_keys(FileStorageSettings::getAllowExt()),
                'allowedPreviewExtensions' => array_keys(FileStorageSettings::getAllowExt()),
            ],
        ]); ?>

        <?= Html::submitButton('Upload', ['class' => 'file-storage-upload-btn btn btn-success']) ?>
    <?php ActiveForm::end() ?>
<?php Modal::end() ?>

<?php
$fileId = Html::getInputId($form, 'files');

$js = <<<JS
$('#{$idForm}').on('beforeSubmit', function (e) {
    e.preventDefault();
    let yiiform = $(this);    
    fileStorageUploadButtonDisable();
    
    $.ajax({
        url: yiiform.attr('action'),
        type: yiiform.attr('method'),
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(data) {
        if (data.error) {
            if (data.message) {
                createNotify('Upload file', data.message, 'error');
            } else {
                yiiform.yiiActiveForm('updateAttribute', '{$fileId}', data.errors.files);
            }
        } else {  
            $('#{$modalId}').modal('hide');          
            createNotify('Upload file', 'Success', 'success');
            $('#{$fileId}').fileinput('clear');
            $('.file-caption').removeClass('is-valid').removeClass('is-invalid');
        }
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
