<?php

use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\useCase\uploadFile\UploadForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
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
        'validateOnBlur' => false,
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

        <div class="alert alert-secondary alert-dismissible fade show info-upload-block" role="alert">
          Maximum upload files - 3. <br />
          Allowed extensions - <?php echo implode(', ', array_keys(FileStorageSettings::getAllowExt())) ?>.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

    <?php ActiveForm::end() ?>
<?php Modal::end() ?>

<?php
$fileId = Html::getInputId($form, 'files');

$js = <<<JS

$('#{$modalId}').on('hidden.bs.modal', function (e) {
    cleanErrors();
    $('#{$fileId}').fileinput('clear');
});

$('#{$modalId} .input-group-btn .btn').on('click', function (e) {
    cleanErrors();
});

$('#{$idForm}').on('beforeSubmit', function (e) {
    e.preventDefault();
    let yiiform = $(this);
    fileStorageUploadButtonDisable();
    cleanErrors();

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
                $('.file-caption').addClass('is-valid').addClass('is-invalid');
                $('#{$idForm} .field-uploadform-files').addClass('has-error');
                $('#{$idForm} .help-block-error').text(data.message);
            } else {
                yiiform.yiiActiveForm('updateAttribute', '{$fileId}', data.errors.files);
            }
        } else {
            $('#{$modalId}').modal('hide');
            createNotify('Upload file', 'Success', 'success');
            $('#{$fileId}').fileinput('clear');
            $('.file-caption').removeClass('is-valid').removeClass('is-invalid');
            if ($('#pjax-file-list').length > 0) {
                pjaxReload({container: '#pjax-file-list'});
            }
            if ($('#pjax-file-count').length > 0) {
                pjaxReload({container: '#pjax-file-count'});
            }
        }
        fileStorageUploadButtonEnable();
    })
    .fail(function() {
        createNotify('Upload file', 'Server error', 'error');
        fileStorageUploadButtonEnable();
    });
    return false;
});

function cleanErrors() {
    $('#{$idForm} .file-caption').removeClass('is-valid').removeClass('is-invalid');
    $('#{$idForm} .field-uploadform-files').removeClass('has-error');
    $('#{$idForm} .help-block-error').text('');
}

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

$css = <<<CSS
    .hand {
        margin-left: 8px;
        display: block;
    }
    .info-upload-block {
        color: #7890a2;
        margin-top: 12px;
    }
CSS;
$this->registerCss($css);
