<?php

/**
 * @var $this \yii\web\View
 * @var $leadForm LeadForm
 */

use common\models\Employee;
use common\models\User;
use frontend\models\LeadForm;
use frontend\themes\gentelella_v2\assets\groups\GentelellaAsset;
use sales\access\EmployeeProductAccess;
use sales\access\ListsAccess;
use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\widgets\objectMenu\QaTaskObjectMenuWidget;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Lead;
use yii\bootstrap4\Modal;

$leadModel = $leadForm->getLead();
$urlUserActions = Url::to(['lead/get-user-actions', 'id' => $leadModel->id]);
$userId = Yii::$app->user->id;

/** @var Employee $user */
$user = Yii::$app->user->identity;


?>
<?php

    $buttonTakeOver = Html::a('<i class="fa fa-share fa-rotate-0"></i> Take Over', [
        'lead/take',
        'gid' => $leadModel->gid,
        'over' => true
    ], [
        'class' => 'take-processing-btn btn btn-sm btn-info',
        'data-status' => $leadModel->status
    ]);

    $buttonTake = Html::a('<i class="fa fa-share fa-rotate-0"></i> Take', [
        'lead/take',
        'gid' => $leadModel->gid
    ], [
        'class' => 'btn btn-sm btn-info',
    ]);

    $buttonClone = Html::a('<i class="fa fa-copy"></i> Clone lead', '#', [
        'id' => 'clone-lead',
        'class' => 'btn btn-primary',
        'data-url' => Url::to(['lead/clone', 'id' => $leadModel->id])
    ]);

    $buttonFollowUp = Html::a('<i class="fa fa-share-square fa-rotate-180"></i> Follow Up', '#', [
        'class' => 'add-reason btn btn-primary text-warning',
        'data-url' => Url::to(['lead-change-state/follow-up', 'gid' => $leadModel->gid]),
        'title' => 'Follow Up'
    ]);

    $buttonTrash = Html::a('<i class="fa fa-trash"></i> Trash', '#', [
        'class' => 'add-reason btn btn-danger',
        'data-url' => Url::to(['lead-change-state/trash', 'gid' => $leadModel->gid]),
        'title' => 'Trash'
    ]);

    $buttonSnooze = Html::a('<i class="fa fa-clock-o"></i> Snooze', '#', [
        'class' => 'add-reason btn btn-primary',
        'data-url' => Url::to(['lead-change-state/snooze', 'gid' => $leadModel->gid]),
        'title' => 'Snooze'
    ]);


    $buttonOnWake = Html::a('<i class="fa fa-street-view"></i> On Wake', Url::to([
        'lead/take',
        'class' => 'btn btn-primary',
        'gid' => $leadModel->gid
    ]));

    $buttonReturnLead = Html::a('<i class="fa fa-share fa-rotate-180"></i> Return Lead', '#', [
        'class' => 'add-reason btn btn-primary',
        'data-url' => \yii\helpers\Url::to(['lead-change-state/return', 'gid' => $leadModel->gid]),
        'title' => 'Return Lead'
    ]);

    $buttonReject = Html::a('<i class="fa fa-times"></i> Reject', '#', [
        'class' => 'add-reason btn btn-primary',
        'data-url' => \yii\helpers\Url::to(['lead-change-state/reject', 'gid' => $leadModel->gid]),
        'title' => 'Reject'
    ]);

    //$buttonAnswer = Html::a('<i class="fa fa-commenting-o"></i> </span>'. ($leadModel->l_answered ? 'UnAnswered' : 'Answered'), ['lead/update2', 'act' => 'answer', 'id' => $leadModel->id], [
    //    'class' => 'add-comment btn btn-default',
    //    //'data-url' => Url::to(['lead/update2', 'act' => 'answer', 'id' => $leadModel->id]),
    //    'data-pjax' => 0
    //]);

    $viwModeSuperAdminCondition = ($leadForm->mode === $leadForm::VIEW_MODE && ($user->isAdmin() || $user->isSupervision()));
    $buttonsSubAction = [];

    $takeConditions = ($leadForm->viewPermission && ($leadModel->isOnHold() || $leadModel->isFollowUp() || $leadModel->isPending() || $leadModel->isProcessing()) && $leadModel->getAppliedAlternativeQuotes() === null);
    $processingConditions = $leadModel->isOwner($user->id) && $leadModel->isProcessing() && $leadModel->getAppliedAlternativeQuotes() === null;

    if ($processingConditions) {
    //    if ($user->isAdmin() || $user->isSupervision()) {
    //        $buttonsSubAction[] = $buttonAnswer;
    //    }
        //$buttonsSubAction[] = $buttonHoldOn;
        $buttonsSubAction[] = $buttonFollowUp;
        if (Auth::can('/lead-change-state/snooze')) {
            $buttonsSubAction[] = $buttonSnooze;
        }
        $buttonsSubAction[] = $buttonTrash;
        if ($leadModel->isSold()) {
            if ($user->isAdmin()) {
                $buttonsSubAction[] = $buttonClone;
            }
        } else {
            $buttonsSubAction[] = $buttonClone;
        }
    }
    if ($leadModel->isSnooze()) {
        $buttonsSubAction[] = $buttonOnWake;
    }
    if ($leadModel->isTrash()) {
        $buttonsSubAction[] = $buttonReturnLead;
        $buttonsSubAction[] = $buttonReject;
    }
    if ($viwModeSuperAdminCondition) {
        if ($leadModel->isSold()) {
            if ($user->isAdmin()) {
                $buttonsSubAction[] = $buttonClone;
            }
        } else {
            $buttonsSubAction[] = $buttonClone;
        }
    }

    if ($user->isAgent() && ($leadModel->isBooked() || $leadModel->isSold())) {
        if ($leadModel->isSold()) {
            if ($user->isAdmin()) {
                $buttonsSubAction[] = $buttonClone;
            }
        } else {
            $buttonsSubAction[] = $buttonClone;
        }
    }


    $project = $leadModel->project;
    $projectStyles = '';
    if ($project) {
        $styleParams = $project->getParams()->style;
        if (!$styleParams->isEmpty()) {
            $defaultStyle = 'background-image:url(https://communication.travelinsides.com/imgs/' . strtolower($project->name) . '/logo_white.png);background-repeat: no-repeat;background-position: center right;background-size: 101px;background-origin: content-box;';
            $projectStyles = ' style="' . $defaultStyle . $styleParams->toString() . '"';
        }
    }

    ?>
<div class="panel-main__header" id="actions-header"<?= $projectStyles?>>

    <?php $productTypes = (new EmployeeProductAccess(Yii::$app->user))->getProductList(); ?>
    <?php if (count($productTypes)) : ?>
        <div class="dropdown">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-plus"></i> Product
            </button>
            <div class="dropdown-menu">
                <?php foreach ($productTypes as $id => $name) :?>
                    <a class="dropdown-item add-product" href="#" data-product-type-id="<?=Html::encode($id)?>">add <?=Html::encode($name)?></a>
                <?php endforeach; ?>
            </div>
        </div> &nbsp;
    <?php endif; ?>

    <?php if (!$user->isQa()) : ?>
            <div class="panel-main__actions">
        <?php if ($takeConditions) {
            if (!$leadModel->isOwner($user->id) && ($leadModel->isProcessing() || $leadModel->isOnHold())) {
                echo $buttonTakeOver;
            } elseif ($leadModel->isPending() || $leadModel->isFollowUp()) {
                echo $buttonTake;
            }
        }?>

        <?php if ($buttonsSubAction) : ?>
            <?php foreach ($buttonsSubAction as $btn) :?>
                <?= $btn ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php  if (!empty($leadModel->bo_flight_id) && $leadModel->isOwner($user->id) && $leadModel->isBooked()) {
            $title = empty($leadModel->getAdditionalInformationFormFirstElement()->pnr)
                ? 'Create PNR' : 'PNR Created';
            $options = empty($leadModel->getAdditionalInformationFormFirstElement()->pnr) ? [
                'class' => 'btn btn-success add-pnr',
                'id' => 'create-pnr',
                'data-url' => Url::to(['lead/add-pnr', 'leadId' => $leadModel->id])
            ] : [
                'class' => 'btn btn-default',
            ];
            echo Html::button('<i class="fa fa-plus"></i> ' . $title . '', $options);
        }  ?>

        <?php if (Auth::can('lead/split-profit', ['lead' => $leadModel]) && Auth::can('/lead/split-profit')) :?>
            <?= Html::button('<i class="fa fa-money"></i> Split profit', [
                    'class' => 'btn btn-default',
                    'id' => 'split-profit',
                    'data-url' => Url::to(['lead/split-profit', 'id' => $leadModel->id]),
                ])?>

            <?php Modal::begin(['id' => 'split-profit-modal',
                'title' => 'Split profit',
                'size' => Modal::SIZE_LARGE
            ])?>
            <?php Modal::end()?>
        <?php endif;?>

        <?php if (Auth::can('lead/split-tips', ['lead' => $leadModel]) && Auth::can('/lead/split-tips')) :?>
            <?= Html::button('<i class="fa fa-money"></i> Split tips', [
                'class' => 'btn btn-default',
                'id' => 'split-tips',
                'data-url' => Url::to(['lead/split-tips', 'id' => $leadModel->id]),
            ])?>

            <?php Modal::begin(['id' => 'split-tips-modal',
                'title' => 'Split tips',
                'size' => Modal::SIZE_LARGE
            ])?>
            <?php Modal::end()?>
        <?php endif;?>
    </div>

    <?php endif; ?>

    <?php
    $canStatusLog = Auth::can('/lead/flow-transition');
    $canDataLogs = Auth::can('/global-log/ajax-view-general-lead-log');
    $canVisitorLogs = Auth::can('/visitor-log/index');
    ?>

    <?php if ($canStatusLog || $canDataLogs || $canVisitorLogs) : ?>
        &nbsp; <div class="dropdown">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bars"> </i> Logs
            </button>
            <div class="dropdown-menu">

                <?php if ($canStatusLog) : ?>
                    <?= Html::a('<i class="fa fa-bars"> </i> Status Logs', null, [
                        'id' => 'view-flow-transition',
                        'class' => 'dropdown-item',
                        'title' => 'Status Logs LeadID #' . $leadForm->lead->id
                    ]) ?>
                <?php endif;?>

                <?php if ($canDataLogs) : ?>
                    <?= Html::a('<i class="fa fa-list"> </i> Data Logs', null, [
                        'id' => 'btn-general-lead-log',
                        'class' => 'dropdown-item showModalButton',
                        'data-modal_id' => 'lg',
                        'title' => 'General Lead Log #' . $leadForm->lead->id,
                        'data-content-url' => Url::to(['global-log/ajax-view-general-lead-log', 'lid' => $leadForm->lead->id])
                    ]) ?>
                <?php endif; ?>

                <?php if ($canVisitorLogs) : ?>
                    <?= Html::a('<i class="fa fa-list"> </i> Visitor Logs', ['/visitor-log/index', 'VisitorLogSearch[vl_lead_id]' => $leadForm->lead->id], [
                        'class' => 'dropdown-item',
                        'title' => 'Visitor log #' . $leadForm->lead->id,
                    ]) ?>
                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

    <?php if (Auth::can('lead/view_QA_Tasks')) : ?>
        <?= QaTaskObjectMenuWidget::widget([
                'objectType' => QaTaskObjectType::LEAD,
                'objectId' => $leadModel->id,
        ]) ?>
    <?php endif; ?>

</div>


<!----Popover for adding notes START---->
<div id="popover-content-add-note" class="d-none popover-form">
    <?php
    $note = new \common\models\Note();
    $addNoteUrl = Url::to([
        '/lead/add-note',
        'id' => $leadModel->id
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
<style>
    #search-results__modal .modal-dialog {
        width: 1150px;
    }

    #modal-info-d .modal-dialog {
        width: 900px;
    }

</style>

<?php Modal::begin(['id' => 'search-results__modal',
    'title' => 'Search results',
    'size' => Modal::SIZE_LARGE
])?>
<?php Modal::end()?>

<?php Modal::begin(['id' => 'flight-details__modal',
    'title' => 'Flight details',
    'size' => Modal::SIZE_DEFAULT,
])?>
<?php Modal::end()?>

<?php Modal::begin(['id' => 'search-result-quote__modal',
    'title' => 'Add quote',
    'size' => Modal::SIZE_LARGE,
])?>
<?php Modal::end()?>
<?php Modal::begin(['id' => 'preview-send-quotes',
    'title' => 'Preview email',
    'size' => Modal::SIZE_LARGE,
])?>
<?php Modal::end()?>


<?php

if ($leadForm->mode !== $leadForm::VIEW_MODE || ($leadForm->mode === $leadForm::VIEW_MODE && ($user->isAdmin() || $user->isSupervision()))) {
    $modelFormName = sprintf('%s-', strtolower($leadForm->formName()));
    $formLeadId = sprintf('%s-form', $leadModel->formName());
    $formClientId = sprintf('%s-form', $leadForm->getClient()->formName());


    //\yii\helpers\VarDumper::dump($leadForm->getLeadPreferences(), true); exit;

    $formPreferenceId = sprintf('%s-form', $leadForm->getLeadPreferences()->formName());

    $addProductUrl = Url::to(['/product/product/create-ajax', 'id' => $leadModel->id]);

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

   

    /***  Split profit  ***/
    $('#split-profit').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var uid = $(this).data('uid');
        var editBlock = $('#split-profit-modal');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                editBlock.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });

    /***  Split tips  ***/
    $('#split-tips').click(function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var uid = $(this).data('uid');
        var editBlock = $('#split-tips-modal');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                editBlock.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });

    /***  Quick search quotes ***/
    $('#quick-search-quotes').on('click', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        $('#modal-lg-label').html('Quick search quotes');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });

    /***  Quick search quotes ***/
    $(document).on('click','#quick-search-quotes-btn', function (e) {
        //$('#popover-quick-search').popover('hide');
        e.preventDefault();
        let url = $('#quick-search-quotes-btn').data('url');
        $('#preloader').removeClass('d-none');
        var modal = $('#search-results__modal');
        
        

         $.ajax({
            type: 'post',
            data: {'gds': $('#gds-selector').val()},
            url: url,
            success: function (data) {
                $('#preloader').addClass('d-none');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            },
            error: function (error) {
               // var obj = JSON.parse(error.data); // $.parseJSON( e.data );
                $('#preloader').addClass('d-none');
                console.error(error.responseText);
                
                alert('Server Error: ' + error.statusText);
            }
        });
    });
    
    /***  Quick search quotes ***/
    $(document).on('click','#search-quotes-btn', function (e) {
        //$('#popover-quick-search').popover('hide');
        e.preventDefault();
        let url = $('#search-quotes-btn').data('url');
        $('#preloader').removeClass('d-none');
        var modal = $('#search-results__modal');
        
         $.ajax({
            type: 'post',
            data: {'gds': $('#gds-selector').val()},
            url: url,
            success: function (data) {
                $('#preloader').addClass('d-none');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            },
            error: function (error) {
               // var obj = JSON.parse(error.data); // $.parseJSON( e.data );
                $('#preloader').addClass('d-none');
                createNotify('Error', error.statusText, 'error');
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
            //alert('Notes for Expert cannot be blank.');
            
            new PNotify({
                title: 'Error: notes',
                type: 'error',
                text: 'Notes for Expert cannot be blank',
                hide: true
            });
            
            //$('#lead-notes_for_experts').parent().find('.help-block').html('Notes for Expert cannot be blank.')
            //$('#lead-notes_for_experts').parent().addClass('has-error');
        }
    });
    
    let addProductUrl = '$addProductUrl';
    
    $(document).on('click', '.add-product', function (e) {
        e.preventDefault();
        
        //let url = $('#quick-search-quotes-btn').data('url');
        //$('#preloader').removeClass('d-none');   
        
        let productType = $(this).data('product-type-id'); 
        let modal = $('#modal-sm');
        $('#modal-sm-label').html('Add new product');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(addProductUrl + "&typeId=" + productType, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            
            /*console.log(status);
            console.log(response);
            console.log(xhr);*/
            
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });         
        
        /* $.ajax({
            type: 'post',
            data: {'gds': $('#gds-selector').val()},
            url: url,
            success: function (data) {
                $('#preloader').addClass('d-none');
                modal.find('.modal-body').html(data);
                modal.modal('show');
            },
            error: function (error) {
               // var obj = JSON.parse(error.data); // $.parseJSON( e.data );
                $('#preloader').addClass('d-none');
                console.error(error.responseText);
                
                alert('Server Error: ' + error.statusText);
            }
        });
            
        new PNotify({
            title: 'Error: notes',
            type: 'error',
            text: 'Notes for Expert cannot be blank',
            hide: true
        });*/            
        
        
    });
    
    $(document).on('click', '#add_product_scenario_one', function (e) {
        e.preventDefault(); 
        
        let modal = $('#modal-sm');
        $('#modal-sm-label').html('Add new product');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(addProductUrl, function( response, status, xhr ) {            
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
        
    });  
    
    
JS;
    $this->registerJs($js);
}


$urlCreateQuoteFromSearch = Url::to(['quote/create-quote-from-search', 'leadId' => $leadModel->id]);

$js = <<<JS
    $(document).on('click','.create_quote__btn', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        var gds = $(this).data('gds');
        var searchResId = $(this).data('result');
        $('#preloader').removeClass('d-none');
        $.ajax({
        url: '$urlCreateQuoteFromSearch',
            type: 'post',
            data: {'key': key, 'gds': gds},
            success: function (data) {
                var error = '';
                
                $('#preloader').addClass('d-none');
                if(data.status == true){
                    //$('#search-results__modal').modal('hide');
                    $('#flight-details__modal').modal('hide');
                    $('#'+searchResId).addClass('quote--selected');

                    $.pjax.reload({container: '#quotes_list', async: false});
                    $('.popover-class[data-toggle="popover"]').popover({ sanitize: false });
                    
                    new PNotify({
                        title: "Create quote - search",
                        type: "success",
                        text: 'Added new quote id: ' + searchResId,
                        hide: true
                    });
                    
                } else {
                   
                     if(data.error) {
                        error = data.error;    
                    } else {
                        error = 'Some errors was happened during create quote. Please try again later';
                    }
                    
                    new PNotify({
                        title: "Error: Create quote - search",
                        type: "error",
                        text: error,
                        hide: true
                    });
                    
                   
                }
            },
            error: function (error) {
                console.log('Error: ' + error);
                $('#preloader').addClass('d-none');
            }
        });
    });

    /** -------- Popovers -------- **/
    $('#popover-link-add-note').popover({
        sanitize: false,
        html: true,
        content: function () {
            return $("#popover-content-add-note").html();
        }
    });

    $('.popover-class[data-toggle="popover"]').popover({sanitize: false});

    $('[data-toggle="popover"]').on('click', function (e) {
        $('[data-toggle="popover"]').not(this).popover('hide');
    });

    $('.client-comment-email-button, .client-comment-phone-button').popover({
        sanitize: false,
        html: true
    });

    /*** Change Lead Status ***/
    $('.add-reason').on('click', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-df');
        let title = $(this).attr('title');
        $('#modal-df-label').html(title);
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            modal.modal('show');
        });
    });

    $('.take-processing-btn').on('click', function (e) {
        e.preventDefault();
        let url = $(this).attr('href');
        if ($.inArray($(this).data('status'), [2, 8]) != -1) {
            let modal = $('#modal-sm');
            $('#modal-sm-label').html('Take processing');
            modal.find('.modal-body').html('');
            modal.find('.modal-body').load(url, function( response, status, xhr ) {
                modal.modal('show');
            });
        } else {
            window.location = url;
        }
    });

    
       

    /***  Add PNR  ***/
    $('#create-pnr').on('click', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-df');
        $('#modal-df-label').html('Create PNR');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            modal.modal('show');
        });
    });

    $('#clone-lead').on('click', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-sm');
        $('#modal-sm-label').html('Clone Lead');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            modal.modal('show');
        });
    });
    
    
    $('#btn-lead-logs').on('click', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        $('#modal-lg-label').html('Old Lead Activity Logs');
        modal.find('.modal-body').html('');
        $('#preloader').removeClass('hidden');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            modal.modal('show');
        });
        return false;
    });
    
    
     $('.btn-reservation-dump').on('click', function(e) {
        e.preventDefault();
        let modal = $('#modal-df');
        let title = $(this).attr('title');
        $('#modal-df-label').html(title);
        modal.find('.modal-body').html('');
        $('#preloader').removeClass('hidden');
        let content = $(this).data('content');
        let content2 = '<textarea rows="5" id="text-quote-dump" readonly="readonly" style="width: 100%">' + content + '</textarea><br><br><div><button class="btn btn-primary btn-clipboard" data-clipboard-target="#text-quote-dump"><i class="fas fa-copy"></i> Copy to clipboard</button></div>';
        
        modal.find('.modal-body').html(content2);
        modal.modal('show');
        //return false;
    });
    
    
    
    
    

    //$(document).ready(function() {
    let clipboard = new ClipboardJS('.btn-clipboard');

    clipboard.on('success', function(e) {
        $('.btn-clipboard i').attr('class', 'fas fa-check');
        //alert('Reservation dump copied successfully to clipboard');
        e.clearSelection();
    });
    //});
    
    // $(document).on('pjax:error', function(xhr, textStatus, error, options) {
    //     alert(textStatus);
    //     console.error(error);
    //     console.log(options);
    // });
    
JS;
$this->registerJs($js);

?>


<?php $this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.css', [
    'depends' => [GentelellaAsset::class],
]);?>
<?php //$this->registerCssFile('//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/css/bootstrap-modal.css',[
//    'depends' => [\yii\bootstrap4\BootstrapAsset::class],
//]);?>
<?php //$this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modal.min.js', ['depends' => [yii\web\JqueryAsset::class]])?>
<?php //$this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/bootstrap-modal/2.2.6/js/bootstrap-modalmanager.min.js', ['depends' => [yii\web\JqueryAsset::class]])?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.js', ['depends' => [yii\web\JqueryAsset::class]])?>
<?php $this->registerJsFile('//cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js', ['depends' => [yii\web\JqueryAsset::class]])?>
<?php $this->registerJsFile('/js/search-result.js', ['depends' => [yii\web\JqueryAsset::class]])?>