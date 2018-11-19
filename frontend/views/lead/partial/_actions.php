<?php
/**
 * @var $this \yii\web\View
 * @var $leadForm LeadForm
 */

use frontend\models\LeadForm;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Lead;
use yii\bootstrap\Modal;

$urlUserActions = Url::to(['lead/get-user-actions', 'id' => $leadForm->getLead()->id]);
$userId = Yii::$app->user->id;

if ($leadForm->mode != $leadForm::VIEW_MODE || ($leadForm->mode == $leadForm::VIEW_MODE && (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id)))) {
    $modelFormName = sprintf('%s-', strtolower($leadForm->formName()));
    $formLeadId = sprintf('%s-form', $leadForm->getLead()->formName());
    $formClientId = sprintf('%s-form', $leadForm->getClient()->formName());
    $formPreferenceId = sprintf('%s-form', $leadForm->getLeadPreferences()->formName());

    $js = <<<JS

    $('#submit-lead-form-btn').click(function(event) {
        event.preventDefault();

        var btn = $(this);
        btn.attr('disabled', true).prop('disabled', true);
        btn.find('span i').attr('class', 'fa fa-spinner');

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
                                if ($(inputId).hasClass('depart-date') || $(inputId).attr('type') == 'tel' || $(inputId).attr('type') == 'email') {
                                    $(inputId).parent().parent().parent().addClass('has-error');
                                    $(inputId).parent().parent().parent().find('.help-block').html(errors[0]);
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
                //console.log(data.errors);
                btn.attr('disabled', false).prop('disabled', false);
                btn.find('span i').attr('class', 'fa fa-check');
            } else {
                //console.log(data);
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
    $(document).on('click','.add-clone-alt-quote', function (e) {
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

    /***  Split profit  ***/
    $('#split-profit').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var uid = $(this).data('uid');
        var editBlock = $('#split-profit-modal');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
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

    /***  Quick search quotes ***/
    $(document).on('click','#quick-search-quotes-btn', function (e) {
        $('#popover-quick-search').popover('hide');
        e.preventDefault();
        var url = $('#quick-search-quotes-btn').data('url');
        $('#preloader').removeClass('hidden');
        var modal = $('#search-results__modal');

         $.ajax({
            type: 'post',
            data: {'gds': $('#gds-selector').val()},
            url: url,
            success: function (data) {
                $('#preloader').addClass('hidden');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            },
            error: function (error) {
                $('#preloader').removeClass('hidden');
                console.log('Error: ' + error);
            }
        });
    });

    /*** Send call expert request ***/
    $('#btn-call-expert').click(function (e) {
        e.preventDefault();
        if ($('#lead-notes_for_experts').val().length != 0) {
            $('#lead-notes_for_experts').parent().find('.help-block').html('')
            $('#lead-notes_for_experts').parent().removeClass('has-error');
            $.post($(this).data('url'), {notes: $('#lead-notes_for_experts').val()});
        } else {
            $('#lead-notes_for_experts').parent().find('.help-block').html('Notes for Expert cannot be blank.')
            $('#lead-notes_for_experts').parent().addClass('has-error');
        }
    });

    /*** Send email ***/
    $('#send-email-action').click(function (e) {
        e.preventDefault();
        var editBlock = $('#create-quote');
        editBlock.find('.modal-title').html('Send Email');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load($(this).data('url'), function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });
JS;
    $this->registerJs($js);
}


$urlCreateQuoteFromSearch = Url::to(['quote/create-quote-from-search', 'leadId' => $leadForm->getLead()->id]);

$js = <<<JS
    $(document).on('click','.create_quote__btn', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        var gds = $(this).data('gds');
        var searchResId = $(this).data('result');
        $('#preloader').removeClass('hidden');
        $.ajax({
        url: '$urlCreateQuoteFromSearch',
            type: 'post',
            data: {'key': key, 'gds': gds},
            success: function (data) {
                $('#preloader').addClass('hidden');
                if(data.status == true){
                    //$('#search-results__modal').modal('hide');
                    $('#flight-details__modal').modal('hide');
                    $('#'+searchResId).addClass('quote--selected');

                    $.pjax.reload({container: '#quotes_list', async: false});
                    $('.popover-class[data-toggle="popover"]').popover();
                }else{
                    alert('Some errors was happened during create quote. Please try again later.');
                }
            },
            error: function (error) {
                console.log('Error: ' + error);
            }
        });
    });

    /** -------- Popovers -------- **/
    $('#popover-link-add-note').popover({
        html: true,
        content: function () {
            return $("#popover-content-add-note").html();
        }
    });

    $('.popover-class[data-toggle="popover"]').popover();

    $('[data-toggle="popover"]').on('click', function (e) {
        $('[data-toggle="popover"]').not(this).popover('hide');
    });

    $('.client-comment-email-button, .client-comment-phone-button').popover({
        html: true
    });

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

    $('.take-processing-btn').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if ($.inArray($(this).data('status'), [2, 8]) != -1) {
            var editBlock = $('#modal-error');
            editBlock.find('.modal-body').html('');
            editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
                editBlock.modal('show');
            });
        } else {
            window.location = url;
        }
    });

    $('#view-client-actions-btn').click(function() {
        var editBlock = $('#log-events');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load('$urlUserActions', function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });

    /***  Add PNR  ***/
    $('#create-pnr').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var editBlock = $('#create-quote');
        editBlock.find('.modal-title').html('PAX INFO');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });

    $('#clone-lead').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var editBlock = $('#modal-error');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            editBlock.modal('show');
        });
    });

$(document).ready(function() {
    var clipboard = new ClipboardJS('.btn-clipboard');

    clipboard.on('success', function(e) {
        alert('Reservation dump copied successfully to clipboard');
        e.clearSelection();
    });
});
JS;
$this->registerJs($js);

?>

<div class="panel-main__header" id="actions-header">
    <div class="panel-main__actions">
        <?php if (!$leadForm->getLead()->isNewRecord) {
            $takeConditions = ($leadForm->viewPermission &&
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
                                        'id' => $leadForm->getLead()->id,
                                        'over' => true
                                    ]), [
                                        'class' => 'take-processing-btn',
                                        'data-status' => $leadForm->getLead()->status
                                    ]);
                                } else if (($leadForm->getLead()->status == Lead::STATUS_ON_HOLD && $leadForm->getLead()->employee_id == Yii::$app->user->identity->getId()) ||
                                    in_array($leadForm->getLead()->status, [Lead::STATUS_PENDING, Lead::STATUS_FOLLOW_UP])
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

                            <?php if(Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) : ?>
                            <li>
                                <?= Html::a('<i class="fa fa-commenting-o"></i> </span>'. ($leadForm->getLead()->l_answered ? 'Make UnAnswered' : 'Make Answered'), ['lead/update2', 'act' => 'answer', 'id' => $leadForm->getLead()->id], [
                                    'class' => 'add-comment',
                                    //'data-url' => Url::to(['lead/update2', 'act' => 'answer', 'id' => $leadForm->getLead()->id]),
                                    'data-pjax' => 0
                                ]) ?>
                            </li>
                            <? endif; ?>

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
                                    'id' => 'send-email-action',
                                    'data-url' => Url::to(['lead/send-email', 'id' => $leadForm->getLead()->id])
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
                            <li>
                                <?= Html::a('<i class="fa fa-copy"></i> Clone lead', '#', [
                                    'id' => 'clone-lead',
                                    'data-url' => Url::to(['lead/clone', 'id' => $leadForm->getLead()->id])
                                ]) ?>
                            </li>
                    </ul>
                </div>
                <!--endregion-->
            <?php endif;
        } ?>

        <?= Html::a('<span class="btn-icon"><i class="fa fa-file-text-o"></i></span> <span class="btn-text">Add Note</span>', null, [
            'class' => 'btn btn-primary btn-with-icon',
            'data-toggle' => 'popover',
            'title' => '',
            'data-content' => '',
            'id' => 'popover-link-add-note',
            'data-placement' => 'bottom',
            'data-original-title' => 'Add note',
        ]) ?>

        <?php if ($leadForm->getLead()->employee_id == Yii::$app->user->getId() &&
            $leadForm->getLead()->status == Lead::STATUS_BOOKED &&
            !empty($leadForm->getLead()->bo_flight_id)
        ) {
            $title = empty($leadForm->getLead()->additionalInformationForm->pnr)
                ? 'Create PNR' : 'PNR Created';
            $options = empty($leadForm->getLead()->additionalInformationForm->pnr) ? [
                'class' => 'btn btn-success btn-with-icon add-pnr',
                'id' => 'create-pnr',
                'data-url' => Url::to(['lead/add-pnr', 'leadId' => $leadForm->getLead()->id])
            ] : [
                'class' => 'btn btn-default btn-with-icon',
            ];
            echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span> <span class="btn-text">' . $title . '</span>', $options);
        } ?>
		<?php if ($leadForm->mode != $leadForm::VIEW_MODE) {
            $title = '<span class="btn-icon"><i class="fa fa-check"></i></span><span class="btn-text">'.($leadForm->getLead()->isNewRecord ? 'Create' : 'Save').'</span>';
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

                /* echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Quick Search Quote</span>', [
                    'class' => 'btn btn-warning btn-with-icon',
                    'id' => 'quick-search-quotes',
                    'data-url' => Url::to(['quote/get-online-quotes-old', 'leadId' => $leadForm->getLead()->id]),
                ]); */

                echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Quick Search Quote</span>', [
                    'class' => 'btn btn-success btn-with-icon popover-class',
                    'data-toggle' => 'popover',
                    'id' => 'popover-quick-search',
                    'data-html' => 'true',
                    'data-title' => 'Choose GDS',
                    'data-trigger' => 'click',
                    'data-content' => '<div style="width:250px;">'.Html::dropDownList('gds', null, ['S' => 'Sabre'], ['class' => 'form-control','id' => 'gds-selector']).Html::button('Search', [
                        'class' => 'btn btn-success',
                        'style' => 'margin-top:10px;',
                        'id' => 'quick-search-quotes-btn',
                        'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id]),
                    ]).'</div>',
                ]);
            }

            if ($leadForm->getLead()->status == Lead::STATUS_PROCESSING &&
                $leadForm->getLead()->employee_id == Yii::$app->user->identity->getId()
            ) {
                $title = (!$leadForm->getLead()->called_expert) ? 'Call Expert' : ' Expert Called';
                $options = (!$leadForm->getLead()->called_expert) ? [
                    'class' => 'btn btn-success btn-with-icon',
                    'id' => 'btn-call-expert',
                    'data-url' => Url::to(['lead/call-expert', 'id' => $leadForm->getLead()->id])
                ] : [
                    'class' => 'btn btn-default btn-with-icon',
                ];
                echo Html::a('<span class="btn-icon"><i class="fa fa-bell"></i></span> <span class="btn-text">' . $title . '</span>', null, $options);
            }
        }elseif($leadForm->mode == $leadForm::VIEW_MODE && (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id))){
            if (!$leadForm->getLead()->isNewRecord) {
                echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Add Quote</span>', [
                    'class' => 'btn btn-success btn-with-icon add-clone-alt-quote',
                    'data-uid' => 0,
                    'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0]),
                ]);

                /* echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Quick Search Quote</span>', [
                    'class' => 'btn btn-warning btn-with-icon',
                    'id' => 'quick-search-quotes',
                    'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id]),
                ]); */

                echo Html::button('<span class="btn-icon"><i class="fa fa-plus"></i></span><span class="btn-text">Quick Search Quote</span>', [
                    'class' => 'btn btn-success btn-with-icon popover-class',
                    'data-toggle' => 'popover',
                    'id' => 'popover-quick-search',
                    'data-html' => 'true',
                    'data-title' => 'Choose GDS',
                    'data-trigger' => 'click',
                    'data-content' => '<div style="width:250px;">'.Html::dropDownList('gds', null, ['S' => 'Sabre'], ['class' => 'form-control','id' => 'gds-selector']).Html::button('Search', [
                        'class' => 'btn btn-success',
                        'style' => 'margin-top:10px;',
                        'id' => 'quick-search-quotes-btn',
                        'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id]),
                    ]).'</div>',
                ]);
            }
        }?>

        <?php if($leadForm->getLead()->status == Lead::STATUS_SOLD && (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id))):?>
        	<?= Html::button('<span class="btn-icon"><i class="fa fa-money"></i></span><span class="btn-text">Split profit</span>', [
                    'class' => 'btn btn-warning btn-with-icon',
                    'id' => 'split-profit',
                    'data-url' => Url::to(['lead/split-profit', 'id' => $leadForm->getLead()->id]),
                ])?>


        <div class="modal modal-quote fade" id="split-profit-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        Split profit
                        <button type="button" class="close" data-dismiss="modal">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>


<!----Popover for adding notes START---->
<div id="popover-content-add-note" class="hidden popover-form">
    <?php
    $note = new \common\models\Note();
    $addNoteUrl = Url::to([
        '/lead/add-note',
        'id' => $leadForm->getLead()->id
    ]);
    $noteForm = \yii\widgets\ActiveForm::begin([
        'action' => $addNoteUrl,
        'id' => 'note-form'
    ]) ?>
    <?= $noteForm->field($note, 'message')->textarea(['rows' => 5]) ?>
    <?= Html::submitButton('Add', [
        'class' => 'btn btn-success popover-close-btn',
        'onclick' => '$(\'#popover-link-add-note\').popover(\'hide\'); $(\'#preloader\').removeClass(\'hidden\');'
    ]) ?>
    <?php \yii\widgets\ActiveForm::end() ?>
</div>
<!--endregion-->

<?php Modal::begin(['id' => 'search-results__modal',
    'header' => '<h2>Search results</h2>',
    'size' => Modal::SIZE_LARGE
])?>
<?php Modal::end()?>

<?php Modal::begin(['id' => 'flight-details__modal',
    'header' => '<h2></h2>',
    'size' => Modal::SIZE_DEFAULT,
])?>
<?php Modal::end()?>

<?php Modal::begin(['id' => 'search-result-quote__modal',
    'header' => '<h2>Add quote</h2>',
    'size' => Modal::SIZE_LARGE,
])?>
<?php Modal::end()?>
<?php Modal::begin(['id' => 'preview-send-quotes',
    'header' => '<h2>Preview email</h2>',
    'size' => Modal::SIZE_LARGE,
])?>
<?php Modal::end()?>
<?php $this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.css',[
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);?>
<?php $this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/css/bootstrap-modal.css',[
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modal.min.js', ['depends' => [yii\web\JqueryAsset::className()]])?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js', ['depends' => [yii\web\JqueryAsset::className()]])?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.js', ['depends' => [yii\web\JqueryAsset::className()]])?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js', ['depends' => [yii\web\JqueryAsset::className()]])?>
<?php $this->registerJsFile('/js/search-result.js', ['depends' => [yii\web\JqueryAsset::className()]])?>