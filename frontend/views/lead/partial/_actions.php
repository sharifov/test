<?php
/**
 * @var $this \yii\web\View
 * @var $leadForm LeadForm
 */

use frontend\models\LeadForm;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Lead;

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

$js = <<<JS
    /*** Change Lead Status ***/
    $('.add-reason').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var editBlock = $('#modal-error');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });
JS;
$this->registerJs($js);

?>

<div class="panel-main__header" id="actions-header">
    <div class="panel-main__actions">
        <?php if (!$leadForm->getLead()->isNewRecord) {
            $takeConditions = ($leadForm->mode != $leadForm::VIEW_MODE &&
                in_array($leadForm->getLead()->status, [Lead::STATUS_ON_HOLD, Lead::STATUS_FOLLOW_UP, Lead::STATUS_PENDING, Lead::STATUS_PROCESSING]) &&
                $leadForm->getLead()->getAppliedAlternativeQuotes() === null
            );
            $processingConditions = ($leadForm->getLead()->employee_id == Yii::$app->user->identity->getId() &&
                $leadForm->getLead()->status == Lead::STATUS_PROCESSING &&
                $leadForm->getLead()->getAppliedAlternativeQuotes() === null
            );
            $unSnoozeConditions = ($leadForm->getLead()->employee_id == Yii::$app->user->identity->getId() &&
                $leadForm->getLead()->status == Lead::STATUS_SNOOZE
            );
            $unTrashConditions = ($leadForm->getLead()->status == Lead::STATUS_TRASH);
            if ($takeConditions || $processingConditions || $unSnoozeConditions || $unTrashConditions) : ?>
                <!--region BTN 'Unassign'-->
                <div class="dropdown inline-block">
                    <?= Html::a('<span class="btn-icon"><i class="fa fa-ellipsis-v"></i></span><span class="btn-text">Action</span>', null, [
                        'class' => 'btn btn-default btn-with-icon',
                        'data-toggle' => 'dropdown'
                    ]) ?>
                    <ul class="dropdown-menu" aria-labelledby="dLabel">
                        <?php if ($takeConditions) : ?>
                            <li>
                                <?php
                                if (in_array($leadForm->getLead()->status, [Lead::STATUS_PROCESSING, Lead::STATUS_ON_HOLD]) && $leadForm->getLead()->employee_id != Yii::$app->user->identity->getId()) {
                                    echo Html::a('<i class="fa fa-share fa-rotate-0"></i> Take Over', Url::to([
                                        'lead/take',
                                        'id' => $leadForm->getLead()->id
                                    ]), [
                                        'class' => 'take-processing-btn',
                                        'data-status' => $leadForm->getLead()->status
                                    ]);
                                } else if (($leadForm->getLead()->status == Lead::STATUS_ON_HOLD && $leadForm->getLead()->employee_id == Yii::$app->user->identity->getId()) ||
                                    ($leadForm->getLead()->status == Lead::STATUS_PENDING)
                                ) {
                                    echo Html::a('<i class="fa fa-share fa-rotate-0"></i> Take', Url::to([
                                        'lead/take',
                                        'id' => $leadForm->getLead()->id
                                    ]));
                                }
                                ?>
                            </li>
                        <?php endif; ?>

                        <?php if ($processingConditions) : ?>
                            <li>
                                <?= Html::a('<i class="fa fa-share fa-rotate-180"></i></span> Hold On', '#', [
                                    'class' => 'add-reason',
                                    'data-url' => Url::to(['lead/change-state', 'queue' => 'processing', 'id' => $leadForm->getLead()->id]),
                                ]) ?>
                            </li>
                            <li>
                                <?= Html::a('<i class="fa fa-share-square fa-rotate-180"></i> Follow Up', '#', [
                                    'class' => 'add-reason',
                                    'data-url' => Url::to(['lead/change-state', 'queue' => 'follow-up', 'id' => $leadForm->getLead()->id]),
                                ]) ?>
                            </li>
                            <li>
                                <?= Html::a('<i class="fa fa-trash"></i> Trash', '#', [
                                    'class' => 'add-reason',
                                    'data-url' => Url::to(['lead/change-state', 'queue' => 'trash', 'id' => $leadForm->getLead()->id]),
                                ]) ?>
                            </li>
                            <li>
                                <?= Html::a('<i class="fa fa-clock-o"></i> Snooze', '#', [
                                    'class' => 'add-reason',
                                    'data-url' => Url::to(['lead/change-state', 'queue' => 'snooze', 'id' => $leadForm->getLead()->id]),
                                ]) ?>
                            </li>
                            <li>
                                <?= Html::a('<i class="fa fa-envelope"></i> Send email', '#', [
                                    'id' => 'send-email-action'
                                ]) ?>
                            </li>
                        <?php endif; ?>

                        <?php if ($unSnoozeConditions) : ?>
                            <li>
                                <?= Html::a('<i class="fa fa-street-view"></i> On Wake', Url::to([
                                    'lead/take',
                                    'id' => $leadForm->getLead()->id
                                ])) ?>
                            </li>
                        <?php endif; ?>

                        <?php if ($unTrashConditions) : ?>
                            <li>
                                <?= Html::a('<i class="fa fa-share fa-rotate-180"></i> Return Lead', '#', [
                                    'class' => 'add-reason',
                                    'data-url' => \yii\helpers\Url::to(['lead/change-state', 'queue' => 'return', 'id' => $leadForm->getLead()->id]),
                                ]) ?>
                            </li>
                            <li>
                                <?= Html::a('<i class="fa fa-times"></i> Reject', '#', [
                                    'class' => 'add-reason',
                                    'data-url' => \yii\helpers\Url::to(['lead/change-state', 'queue' => 'reject', 'id' => $leadForm->getLead()->id]),
                                ]) ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!--endregion-->
            <?php endif;
        } ?>

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