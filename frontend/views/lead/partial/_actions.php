<?php
/**
 * @var $this \yii\web\View
 * @var $leadForm LeadForm
 */

use frontend\models\LeadForm;
use yii\helpers\Html;
use yii\helpers\Url;

if ($leadForm->mode != $leadForm::VIEW_MODE) {
    $modelFormName = sprintf('%s-', strtolower($leadForm->formName()));
    $formLeadId = sprintf('%s-form', $leadForm->getLead()->formName());
    $formClientId = sprintf('%s-form', $leadForm->getClient()->formName());
    $formPreferenceId = sprintf('%s-form', $leadForm->getLeadPreferences()->formName());

    $js = <<<JS
    $('.client-comment-email-button, .client-comment-phone-button').popover({
        html: true
    });

    $('#submit-lead-form-btn').click(function() {
        event.preventDefault();
        var formData = $('#$formLeadId, #$formClientId, #$formPreferenceId').serialize();
        $.post($('#$formLeadId').attr('action'), formData, function( data ) {
            $('.has-error').each(function() {
                $(this).removeClass('has-error');
                $(this).find('.help-block').html('');
            });
            if (data.load && data.errors.length != 0) {
                $.each(data.errors, function( index, model ) {
                    var attrName = index.replace("$modelFormName", ""); 
                    $.each(model[0], function( attr, errors) {
                        if (jQuery.type(errors) == 'object') {
                            var objectModel = errors;
                            var keyModel = attr;
                            $.each(objectModel, function( attr, errors) {
                                var inputId = '#' + attrName + '-' + keyModel + '-' + attr;
                                if ($(inputId).hasClass('depart-date') || $(inputId).attr('type') == 'tel') {
                                    $(inputId).parent().parent().addClass('has-error');
                                    $(inputId).parent().parent().find('.help-block').html(errors[0]);
                                } else {
                                    $(inputId).parent().addClass('has-error');
                                    $(inputId).parent().find('.help-block').html(errors[0]);
                                }
                            });
                        } else if (jQuery.type(errors) == 'array') {
                            var inputId = '#' + attrName + '-' + attr;
                            if (!$(inputId).is('select')) {
                                $(inputId).parent().addClass('has-error');
                                $(inputId).parent().find('.help-block').html(errors[0]);
                            } else {
                                $(inputId).parent().parent().addClass('has-error');
                                $(inputId).parent().parent().find('.help-block').html(errors[0]);
                            }
                        }
                    });
                });
                console.log(data.errors);
            } else {
                console.log(data);
            }
        });
    });
    
    $('.lead-form-input-element').on("keyup", function(event) {
        event.preventDefault();
        if (event.keyCode === 13) {
            $('#submit-lead-form-btn').click();
        }
    });
    
    /***  Add/Clone quote  ***/
    $('.add-clone-alt-quote').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var uid = $(this).data('uid');
        var editBlock = $('#create-quote');
        if (uid != 0) {
            editBlock.find('.modal-title').html('Clone quote #' + uid);
        } else {
             editBlock.find('.modal-title').html('Add quote');
        }
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#cancel-alt-quote').attr('data-type', 'direct');
            editBlock.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
    
    /***  Quick search quotes ***/
    $('#quick-search-quotes').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var editBlock = $('#quick-search');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal({
              backdrop: 'static',
              show: true
            });
        });
    });
JS;
    $this->registerJs($js);
}

?>

<div class="panel-main__header" id="actions-header">
    <div class="panel-main__actions">
        <?php if ($leadForm->mode != $leadForm::VIEW_MODE) {
            $title = '<span class="btn-icon"><i class="fa fa-check"></i></span><span class="btn-text">Save</span>';
            echo Html::submitButton($title, [
                'id' => 'submit-lead-form-btn',
                'class' => 'btn btn-primary btn-with-icon'
            ]);

            if (!$leadForm->getLead()->isNewRecord) {
                echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Add Quote</span>', [
                    'class' => 'btn btn-success btn-with-icon add-clone-alt-quote',
                    'data-uid' => 0,
                    'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0]),
                ]);

                echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Quick Search Quote</span>', [
                    'class' => 'btn btn-success btn-with-icon',
                    'id' => 'quick-search-quotes',
                    'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id]),
                ]);
            }
        } ?>
    </div>
</div>