<?php

/**
 * @var $leadForm LeadForm
 * @var $comForm CommunicationForm
 * @var $previewEmailForm LeadPreviewEmailForm
 * @var $previewSmsForm LeadPreviewSmsForm
 * @var $quotesProvider ActiveDataProvider
 * @var $dataProviderCommunicationLog ActiveDataProvider
 * @var $dataProviderCallExpert ActiveDataProvider
 * @var $dataProviderNotes ActiveDataProvider
 * @var $modelLeadCallExpert LeadCallExpert
 * @var $modelNote Note
 * @var $modelLeadChecklist LeadChecklist
 * @var $dataProviderChecklist ActiveDataProvider
 * @var $itineraryForm \src\forms\lead\ItineraryEditForm
 * @var $dataProviderOffers ActiveDataProvider
 * @var $dataProviderOrders ActiveDataProvider
 * @var AbacCallFromNumberList $callFromNumberList
 * @var AbacSmsFromNumberList $smsFromNumberList
 * @var AbacEmailList $emailFromList
 * @var $isCreatedFlightRequest bool
 */

use common\models\Employee;
use common\models\LeadCallExpert;
use common\models\LeadChecklist;
use common\models\Note;
use frontend\models\CommunicationForm;
use frontend\models\LeadForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;
use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\services\access\FileStorageAccessService;
use modules\fileStorage\src\widgets\FileStorageListWidget;
use modules\fileStorage\src\widgets\FileStorageUploadWidget;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacObject;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\communicationBlock\LeadCommunicationBlockAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use src\model\call\useCase\createCall\fromLead\AbacCallFromNumberList;
use src\model\email\useCase\send\fromLead\AbacEmailList;
use src\model\sms\useCase\send\fromLead\AbacSmsFromNumberList;
use yii\bootstrap4\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

\frontend\themes\gentelella_v2\assets\AssetLeadCommunication::register($this);

// $this->params['breadcrumbs'][] = $this->title;

$userId = Yii::$app->user->id;
/** @var Employee $user */
$user = Yii::$app->user->identity;

$is_admin = $user->isAdmin();
$is_qa = $user->isQa();
$is_supervision = $user->canRole('supervision');

if ($is_admin || $is_supervision) {
    $is_manager = true;
} else {
    $is_manager = false;
}

$lead = $leadForm->getLead();

$clientProjectInfo = $lead->client->clientProjects;
$unsubscribe = false;
if (isset($clientProjectInfo) && $clientProjectInfo) {
    foreach ($clientProjectInfo as $item) {
        if ($lead->project_id == $item['cp_project_id']) {
            $unsubscribe = $item['cp_unsubscribe'];
        }
    }
} else {
    $unsubscribe = false;
}

$unsubscribedEmails = array_column($lead->project->emailUnsubscribes, 'eu_email');

$leadAbacDto = new LeadAbacDto($lead, Auth::id());

/** @abac new $leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK, Disable mask client data on Lead view */
$disableMasking = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::LOGIC_CLIENT_DATA, LeadAbacObject::ACTION_UNMASK);
?>
<?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-header', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 5000]) ?>

<?= $this->render('partial/_view_header', [
    'lead' => $lead,
    'title' => $this->title
]) ?>
<?php yii\widgets\Pjax::end() ?>

    <div class="main-sidebars">
        <div class="panel panel-main">
            <?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-header-sidebar', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 5000]) ?>
                <?= $this->render('partial/_actions', ['leadForm' => $leadForm]); ?>
            <?php yii\widgets\Pjax::end() ?>

            <div class="col-md-12">
                <?= \common\widgets\Alert::widget() ?>
            </div>

            <div class="col-md-6">

                <?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-products-wrap', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 5000]) ?>

                <?= $this->render('products/_products', [
                    'lead' => $lead,
                    'itineraryForm' => $itineraryForm,
                    'quotesProvider' => $quotesProvider,
                    'leadForm' => $leadForm,
                    'is_manager' => $is_manager,
                    'isCreatedFlightRequest' => $isCreatedFlightRequest
                ]) ?>

                <?php if ($lead->products) : ?>
                    <?= $this->render('offers/lead_offers', [
                        'lead' => $lead,
                        'leadForm' => $leadForm,
                        'dataProviderOffers' => $dataProviderOffers,
                        'is_manager' => $is_manager,
                    ]) ?>

                    <?= $this->render('orders/lead_orders', [
                        'lead' => $lead,
                        'leadForm' => $leadForm,
                        'dataProviderOrders' => $dataProviderOrders,
                        'is_manager' => $is_manager,
                    ]) ?>
                <?php endif; ?>
                <?php \yii\widgets\Pjax::end(); ?>

            </div>
            <div class="col-md-6">

                <?php /** @abac $leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS, Give access to Client Info block on lead */ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_BLOCK_CLIENT_INFO, LeadAbacObject::ACTION_ACCESS)) : ?>
                    <?= $this->render('client-info/client_info', [
                        'lead' => $lead,
                        'leadForm' => $leadForm,
                        'is_manager' => $is_manager,
                        'unsubscribe' => $unsubscribe,
                        'unsubscribedEmails' => $unsubscribedEmails,
                        'leadAbacDto' => $leadAbacDto,
                        'disableMasking' => $disableMasking
                    ]) ?>
                <?php endif; ?>

                <?php if (Auth::can('lead-view/lead-preferences/view', ['lead' => $lead])) : ?>
                    <div id="lead-preferences">
                        <?= $this->render('partial/_lead_preferences', [
                            'lead' => $lead
                        ]) ?>
                    </div>
                <?php endif; ?>

                <div id="lead-data">
                    <?= $this->render('partial/_lead_data', [
                        'lead' => $lead
                    ]) ?>
                </div>

                <?php /** @abac $leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_READ, View list User Conversation */ ?>
                <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_USER_CONVERSION, LeadAbacObject::ACTION_READ)) : ?>
                    <?php echo $this->render('user_conversion/lead_user_conversion', [
                        'lead' => $lead,
                        'leadAbacDto' => $leadAbacDto,
                    ]) ?>
                <?php endif; ?>

                <?php if (Auth::can('lead-view/check-list/view', ['lead' => $lead])) : ?>
                    <?= $this->render('checklist/lead_checklist', [
                        'lead' => $lead,
                        'comForm' => $comForm,
                        'leadId' => $lead->id,
                        'dataProvider' => $dataProviderChecklist,
                        'isAdmin' => $is_admin,
                        'modelLeadChecklist' => $modelLeadChecklist,
                    ]) ?>
                <?php endif; ?>

                <?php if (Auth::can('lead-view/task-list/view', ['lead' => $lead])) : ?>
                    <?= $this->render('partial/_task_list', [
                        'lead' => $lead
                    ]) ?>
                <?php endif; ?>

                <?php if (Auth::can('lead-view/notes/view', ['lead' => $lead])) : ?>
                    <?= $this->render('notes/agent_notes', [
                        'lead' => $lead,
                        'dataProviderNotes' => $dataProviderNotes,
                        'modelNote' => $modelNote,
                    ]) ?>
                <?php endif; ?>

                <?php $leadCommunicationBlockAbacDto = new LeadCommunicationBlockAbacDto($lead, [], [], [], $user->id); ?>
                <?php /** @abac $leadCommunicationBlockAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_VIEW, View communication block on Lead View page */ ?>
                <?php if (Yii::$app->abac->can($leadCommunicationBlockAbacDto, LeadCommunicationBlockAbacObject::NS, LeadCommunicationBlockAbacObject::ACTION_VIEW, $user)) : ?>
                    <?= $this->render('communication/lead_communication', [
                        'leadForm' => $leadForm,
                        'previewEmailForm' => $previewEmailForm,
                        'previewSmsForm' => $previewSmsForm,
                        'comForm' => $comForm,
                        'leadId' => $lead->id,
                        'dataProvider' => $dataProviderCommunicationLog,
                        'isAdmin' => $is_admin,
                        'lead' => $lead,
                        'unsubscribe' => $unsubscribe,
                        'unsubscribedEmails' => $unsubscribedEmails,
                        'disableMasking' => $disableMasking,
                        'callFromNumberList' => $callFromNumberList,
                        'smsFromNumberList' => $smsFromNumberList,
                        'emailFromList' => $emailFromList,
                    ]); ?>
                <?php endif; ?>

                <?php if (FileStorageSettings::isEnabled() && Auth::can('lead-view/files/view', ['lead' => $lead])) : ?>
                    <?= FileStorageListWidget::byLead(
                        $lead->id,
                        FileStorageAccessService::canLeadUploadWidget($lead)
                    ) ?>
                <?php endif; ?>

                <?php if (!$lead->client->isExcluded()) : ?>
                    <?php if (Auth::can('lead-view/call-expert/view', ['lead' => $lead])) : ?>
                        <?php if (Yii::$app->user->identity->isAllowCallExpert) : ?>
                            <?= $this->render('call-expert/lead_call_expert', [
                                'lead' => $lead,
                                'comForm' => $comForm,
                                'leadId' => $lead->id,
                                'dataProvider' => $dataProviderCallExpert,
                                'isAdmin' => $is_admin,
                                'modelLeadCallExpert' => $modelLeadCallExpert,
                            ]) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

            </div>

            <div class="col-md-6">

                <!--            --><?php //if (!$lead->isNewRecord) :?>
                <!--                <div class="row">-->
                <!--                    <div class="col-md-12">-->
                <!--                        --><?php //if(!$lead->l_answered):?>
                <!---->
                <!--                            --><?php //if($lead->isProcessing()):?>
                <!--                                --><?php //= Html::a(($lead->l_answered ? '<i class="fa fa-commenting-o"></i>Make UnAnswered' : '<i class="fa fa-commenting"></i> Make Answered'), ['lead/update2', 'id' => $lead->id, 'act' => 'answer'], [
                //                                    'class' => 'btn '.($lead->l_answered ? 'btn-success' : 'btn-info'),
                //                                    'data-pjax' => false,
                //                                    'data' => [
                //                                        'confirm' => 'Are you sure?',
                //                                        'method' => 'post',
                //                                        'pjax' => 0
                //                                    ],
                //                                ])?>
                <!--                            --><?php //else:?>
                <!--                                <span class="badge badge-warning"><i class="fa fa-commenting-o"></i> ANSWERED: false</span>-->
                <!--                            --><?php //endif;?>
                <!---->
                <!--                        --><?php //else:?>
                <!--                            <span class="badge badge-success"><i class="fa fa-commenting-o"></i> ANSWERED: true</span>-->
                <!--                        --><?php //endif;?>
                <!---->
                <!--                    </div>-->
                <!---->
                <!--                </div>-->
                <!--            --><?php //endif;?>


                <!--                --><?php //= $this->render('quotes/quote_list', [
                //                    'dataProvider' => $quotesProvider,
                //                    'lead' => $lead,
                //                    'leadForm' => $leadForm,
                //                    'is_manager' => $is_manager,
                //                ]);?>


            </div>

            <div class="clearfix"></div>
            <br/>
            <br/>

        </div>
    </div>

<?php
Modal::begin([
    'id' => 'modalLead',
    'title' => '',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
Modal::end();
?>

<?php

if (!$lead->isNewRecord) {
    $flowTransitionUrl = \yii\helpers\Url::to([
        'lead/flow-transition',
        'leadId' => $lead->id
    ]);

    $removeConversationUrl = Url::to(['/lead-user-conversion/delete']);
    $addConversationUrl = Url::to(['/lead-user-conversion/add', 'lead_id' => $lead->id]);

    $js = <<<JS

    $(document).on('click', '.js-add-conversation-btn', function (e) {
        e.preventDefault();
        let leadId = $(this).attr('data-lead-id');
        let modal = $('#modal-sm');
        
        $('#modal-sm-label').html('Add conversion');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load('{$addConversationUrl}', function(response, status, xhr) {
            $('#preloader').addClass('d-none');

            if (status === 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            } 
        });
    });

    $(document).on('click', '.js-remove-conversation-btn', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this item?')) {
            return false;
        }
    
        let leadId = $(this).attr('data-lead-id');
        let userId = $(this).attr('data-user-id');
        let btnSubmit = $(this);
        let btnContent = btnSubmit.html();
    
        btnSubmit.html('<i class="fa fa-cog fa-spin"></i> Loading...').prop('disabled', true);
    
        $.ajax({
            url: '{$removeConversationUrl}',
            type: 'POST',
            data: {lead_id: leadId, user_id: userId},
            dataType: 'json'
        })
        .done(function(dataResponse) {
            if (dataResponse.status > 0) {
                createNotify('Success', dataResponse.message, 'success');

                if ($("#pjax-user-conversation-list").length) {
                    pjaxReload({container:"#pjax-user-conversation-list"});
                }
            } else if (dataResponse.message.length) {
                createNotify('Error', dataResponse.message, 'error');
            } else {
                createNotify('Error', 'Error, please check logs', 'error');
            }
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            createNotify('Error', jqXHR.responseText, 'error');
            btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
        })
        .always(function(jqXHR, textStatus, errorThrown) {
            setTimeout(function () {
                btnSubmit.html(btnContent).removeClass('btn-default').prop('disabled', false);
            }, 5000);
        });
    });

    $('#view-flow-transition').on('click', function() {
        $('#preloader').removeClass('hidden');
        let modal = $('#modal-lg');
        $('#modal-lg-label').html('Lead status logs');
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load('$flowTransitionUrl', function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            modal.modal('show');
        });
    });
    
    $(function () {
        $.scrollUp({
            scrollName: 'scrollUp', // Element ID
            topDistance: '300', // Distance from top before showing element (px)
            topSpeed: 300, // Speed back to top (ms)
            animation: 'fade', // Fade, slide, none
            animationInSpeed: 200, // Animation in speed (ms)
            animationOutSpeed: 200, // Animation out speed (ms)
            scrollText: 'Scroll to top', // Text for element
            activeOverlay: true, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
        });
    
        //$("[data-toggle='tooltip']").tooltip();
        //$("[data-toggle='popover']").popover({sanitize: false});
    
    });
     $("#pj-itinerary").on("pjax:end", function () {
         if ($('#modeFlightSegments').data('value') == 'view') {
            $.pjax.reload({container: '#quotes_list', timeout: 10000, async: false});
            $.pjax.reload({container: '#pjax-lead-call-expert', timeout: 10000, async: false});
         }
     });
    
JS;

    $this->registerJs($js);
}

$jsCode = <<<JS

    $(document).on('click', '.showModalButton', function(){
        let id = $(this).data('modal_id');
        let url = $(this).data('content-url');

        $('#modal-' + id + '-label').html($(this).attr('title'));
        $('#modal-' + id).modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');

        $.post(url, function(data) {
            $('#modal-' + id).find('.modal-body').html(data);
        });
       //return false;
    });

$(document).on('click','#client-unsubscribe-button', function (e) {
        e.preventDefault();
        let url = $(this).data('unsubscribe-url');
        $.ajax({
            url: url,               
            success: function(response){
                $.pjax.reload({container: '#pjax-client-info', timeout: 10000, async: false});
                if (Boolean(Number(response.data.action))){
                    createNotifyByObject({title: "Communication", type: "info", text: 'Client communication restricted', hide: true});
                } else {
                    createNotifyByObject({title: "Communication", type: "info", text: 'Client communication allowed', hide: true});
                }
                updateCommunication();
            }
        });
    });

    $(document).on('click','#client-subscribe-button', function (e) {
        e.preventDefault();
        let url = $(this).data('subscribe-url');
        $.ajax({
            url: url,               
            success: function(response){
                $.pjax.reload({container: '#pjax-client-info', timeout: 10000, async: false});
                if (Boolean(Number(response.data.action))){
                    createNotifyByObject({title: "Communication", type: "info", text: 'Client communication restricted', hide: true});
                } else {
                    createNotifyByObject({title: "Communication", type: "info", text: 'Client communication allowed', hide: true});
                }
                updateCommunication();
            }
        });
    });
    
JS;

$this->registerJs($jsCode);

Modal::begin([
    'title' => 'Client Chat Room',
    'id' => 'chat-room-popup',
    'size' => Modal::SIZE_LARGE
]);

Modal::end();

$jsCommBlockChatView = <<<JS

$('body').on('click', '.comm-chat-room-view', function(e) {
    e.preventDefault();
    $.get(        
        '/client-chat-qa/room',       
        {
            id: $(this).data('id')
        },
        function (data) {
            $('#chat-room-popup .modal-body').html(data);
            $('#chat-room-popup').modal('show');
        }  
    );
});

JS;
$this->registerJs($jsCommBlockChatView);

$css = <<<CSS
    .datepicker {
        z-index: 1040!important;
    }
CSS;
$this->registerCss($css);