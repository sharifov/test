<?php

use common\models\Lead;
use sales\forms\lead\CloneQuoteByUidForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\bootstrap4\Modal;

/** @var Lead $lead */

Modal::begin([
    'id' => 'modal-clone-quote-by-id',
    'size' => Modal::SIZE_SMALL,
    //'clientOptions' => ['backdrop' => 'static'],
    'title' => 'Clone Quote',
]);

$cloneQuoteForm = new CloneQuoteByUidForm();

?>

<?php $form = ActiveForm::begin([
    'id' => 'clone-quote-by-id-form',
    'action' => ['lead-view/clone-quote-by-uid'],
    'validationUrl' => ['lead-view/clone-quote-by-uid-validate'],
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validateOnChange' => false,
    'validateOnBlur' => false
]) ?>

    <?= $form->field($cloneQuoteForm, 'confirm', ['template' => '{input}'])->hiddenInput()->label(false) ?>

    <?= $form->field($cloneQuoteForm, 'leadGid')->hiddenInput(['value' => $lead->gid])->label(false) ?>

    <?= $form->field($cloneQuoteForm, 'uid')->textInput() ?>

    <?= Html::submitButton('Clone', ['class' => 'btn btn-success popover-close-btn']) ?>

<?php ActiveForm::end() ?>

<?php

Modal::end();

?>

<?= Html::a('<i class="fa fa-plus-circle success"></i> Clone Quote by UID', null, [
    'class' => 'clone-quote-by-uid',
]) ?>

<?php

$js = <<<JS
    function resetCloneForm() {
        $(".form-group.field-clonequotebyuidform-uid").removeClass("has-error");
        $(".form-group.field-clonequotebyuidform-uid .help-block").html("");
        $('#clonequotebyuidform-uid').val('');
        $('#clonequotebyuidform-confirm').val(0);
    }
    $(document).on('click', '.clone-quote-by-uid', function(e) {
        e.preventDefault();
        resetCloneForm();
        $('#modal-clone-quote-by-id').modal();
    }); 
    $(document).on('click', '.clone-quote-by-uid-self', function(e) {
        e.preventDefault();
        resetCloneForm();
        let uid = $(this).data('uid');
        $('#clonequotebyuidform-uid').val(uid);
        $('#clonequotebyuidform-confirm').val(0);
        $('#modal-clone-quote-by-id').modal();
    }); 

    $('#clone-quote-by-id-form').on('afterValidate', function (event, messages, attrError) {
        if (typeof attrError !== 'undefined' && Array.isArray(attrError)) {
            for (let i = 0; i < attrError.length; ++i) {
                if (attrError[i]['id'] != 'clonequotebyuidform-confirm') {
                    $('#clonequotebyuidform-confirm').val(0);
                }
            }
        }
        if (typeof messages['clonequotebyuidform-confirm'] !== 'undefined') {
            if (messages['clonequotebyuidform-confirm'].length > 0) {
                $('#clonequotebyuidform-confirm').val(messages['clonequotebyuidform-confirm']);    
            }
            
        }
    });
    
$('#clone-quote-by-id-form').on('beforeSubmit', function (e) {
     e.preventDefault();
    let yiiform = $(this);
    $.ajax({
            type: yiiform.attr('method'),
            url: yiiform.attr('action'),
            data: yiiform.serializeArray()
        }
    )
    .done(function(data) {
       $('#modal-clone-quote-by-id').modal('hide');
       resetCloneForm();
       if(data.success) {
           let text = 'Cloned new quote';
           if (data.message) {
               text = data.message;
           }
            $.pjax.reload({container: '#quotes_list', async: false});
            new PNotify({
                title: "Clone Quote by UID",
                type: "success",
                text: text,
                hide: true
            });
        } else {
           let text = 'Error. Try again later';
           if (data.message) {
               text = data.message;
           }
            new PNotify({
                title: "Clone Quote by UID",
                type: "error",
                text: text,
                hide: true
            });
        }
    })
    .fail(function () {
       $('#modal-clone-quote-by-id').modal('hide');
       resetCloneForm();
        new PNotify({
            title: "Clone Quote by UID",
            type: "error",
            text: 'Error. Try again later',
            hide: true
        });
    })
    return false;
})
JS;

$this->registerJs($js);
