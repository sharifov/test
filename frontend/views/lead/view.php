<?php

/**
 * @var $leadForm LeadForm
 * @var $comForm CommunicationForm
 * @var $previewEmailForm LeadPreviewEmailForm
 * @var $previewSmsForm LeadPreviewSmsForm
 * @var $quotesProvider ActiveDataProvider
 * @var $dataProviderCommunication ActiveDataProvider
 * @var $dataProviderCommunicationLog ActiveDataProvider
 * @var $dataProviderCallExpert ActiveDataProvider
 * @var $dataProviderNotes ActiveDataProvider
 * @var $enableCommunication boolean
 * @var $modelLeadCallExpert LeadCallExpert
 * @var $modelNote Note
 * @var $modelLeadChecklist LeadChecklist
 * @var $dataProviderChecklist ActiveDataProvider
 * @var $itineraryForm \sales\forms\lead\ItineraryEditForm
 * @var $dataProviderOffers ActiveDataProvider
 * @var $dataProviderOrders ActiveDataProvider
 * @var $fromPhoneNumbers array
 * @var bool $smsEnabled
 */

use common\models\Employee;
use common\models\LeadCallExpert;
use common\models\LeadChecklist;
use common\models\Note;
use frontend\models\CommunicationForm;
use frontend\models\LeadForm;
use frontend\models\LeadPreviewEmailForm;
use frontend\models\LeadPreviewSmsForm;
use sales\auth\Auth;
use yii\bootstrap4\Modal;
use yii\data\ActiveDataProvider;

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
?>

<?= $this->render('partial/_view_header', [
    'lead' => $lead,
    'title' => $this->title
]) ?>


<div class="main-sidebars">
    <div class="panel panel-main">
        <?= $this->render('partial/_actions', [
            'leadForm' => $leadForm
        ]);
?>

        <div class="col-md-12">
            <?= \common\widgets\Alert::widget() ?>
        </div>

        <div class="col-md-6">
<?php if (Auth::can('lead-view/flight-default/view', ['lead' => $lead])) : ?>
        <?php yii\widgets\Pjax::begin(['id' => 'pjax-lead-products-wrap', 'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 5000]) ?>

            <?= $this->render('products/_products', [
                'lead' => $lead,
                'itineraryForm' => $itineraryForm,
                'quotesProvider' => $quotesProvider,
                'leadForm' => $leadForm,
                'is_manager' => $is_manager,
            ]) ?>N

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
<?php endif; ?>
        </div>
        <div class="col-md-6">
            <?php /*if($leadForm->mode === $leadForm::VIEW_MODE && (!$is_admin && !$is_qa && !$is_supervision) && !$lead->isOwner($user->id)):*/?><!--
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Client information is not available in VIEW MODE, please take lead!</p>
                </div>

            <?php /*elseif(!$is_manager && !$is_qa && ( $lead->isFollowUp() || ($lead->isPending() && !$lead->isNewRecord) ) && !$lead->isOwner($user->id)):*/?>

                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Client information is not available for this status (<?/*=strtoupper($lead->getStatusName())*/?>)!</p>
                </div>

            <?php /*else: */?>
                <?php /*yii\widgets\Pjax::begin(['id' => 'pjax-client-info', 'enablePushState' => false, 'enableReplaceState' => false]) */?>
                <?/*= $this->render('client-info/client_info', [
                    'lead' => $lead,
                    'leadForm' => $leadForm,
                    'is_manager' => $is_manager,
                    'unsubscribe' => $unsubscribe
                ]) */?>
                <?php /*\yii\widgets\Pjax::end(); */?>
            --><?php /*endif;*/?>

            <?php if (Auth::can('lead-view/client-info/view', ['lead' => $lead])) : ?>
                <?= $this->render('client-info/client_info', [
                    'lead' => $lead,
                    'leadForm' => $leadForm,
                    'is_manager' => $is_manager,
                    'unsubscribe' => $unsubscribe
                ]) ?>
            <?php endif; ?>

            <?php /*if($leadForm->mode === $leadForm::VIEW_MODE && (!$is_admin && !$is_qa && !$is_supervision) && !$lead->isOwner($user->id)):*/?><!--
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Lead Preferences is not available in VIEW MODE, please take lead!</p>
                </div>
            <?php /*elseif(!$is_manager && !$is_qa && ( $lead->isFollowUp() || ($lead->isPending() && !$lead->isNewRecord) ) && !$lead->isOwner($user->id)):*/?>

                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Client information is not available for this status (<?/*=strtoupper($lead->getStatusName())*/?>)!</p>
                </div>
            <?php /*else: */?>
                <div id="lead-preferences">
                    <?/*= $this->render('partial/_lead_preferences', [
                        'lead' => $lead
                    ]) */?>
                </div>
            --><?php /*endif; */?>

            <?php if (Auth::can('lead-view/lead-preferences/view', ['lead' => $lead])) : ?>
                <div id="lead-preferences">
                    <?= $this->render('partial/_lead_preferences', [
                        'lead' => $lead
                    ]) ?>
                </div>
            <?php endif; ?>

            <?php if (Auth::can('lead-view/check-list/view', ['lead' => $lead])) : ?>
                <?= $this->render('checklist/lead_checklist', [
                    'lead' => $lead,
                    'comForm'       => $comForm,
                    'leadId'        => $lead->id,
                    'dataProvider'  => $dataProviderChecklist,
                    'isAdmin'       => $is_admin,
                    'modelLeadChecklist'       => $modelLeadChecklist,
                ]) ?>
            <?php endif; ?>

            <?php if (Auth::can('lead-view/task-list/view', ['lead' => $lead])) : ?>
                <?= $this->render('partial/_task_list', [
                    'lead' => $lead
                ]) ?>
            <?php endif; ?>

            <?php if (Yii::$app->user->can('lead-view/communication-block/view', ['lead' => $lead])) : ?>
                <?= $this->render('communication/lead_communication', [
                    'leadForm'      => $leadForm,
                    'previewEmailForm' => $previewEmailForm,
                    'previewSmsForm' => $previewSmsForm,
                    'comForm'       => $comForm,
                    'leadId'        => $lead->id,
                    'dataProvider'  => (bool)Yii::$app->params['settings']['new_communication_block_lead'] ? $dataProviderCommunicationLog : $dataProviderCommunication,
                    'isAdmin'       => $is_admin,
                    'isCommunicationLogEnabled' => Yii::$app->params['settings']['new_communication_block_lead'],
                    'lead' => $lead,
                    'fromPhoneNumbers' => $fromPhoneNumbers,
                    'unsubscribe' => $unsubscribe,
                    'smsEnabled' => $smsEnabled,
                ]); ?>
                <?php /*else: */ ?><!--
                <div class="alert alert-warning" role="alert">You do not have access to view Communication block messages.</div>-->
            <?php endif;?>

            <?php //php \yii\helpers\VarDumper::dump(Yii::$app->user->identity->callExpertCountByShiftTime)?>


            <?php if (!$lead->client->isExcluded()) : ?>
                <?php if (Auth::can('lead-view/call-expert/view', ['lead' => $lead])) : ?>
                    <?php  if (Yii::$app->user->identity->isAllowCallExpert) : ?>
                        <?= $this->render('call-expert/lead_call_expert', [
                            'lead' => $lead,
                            'comForm'       => $comForm,
                            'leadId'        => $lead->id,
                            'dataProvider'  => $dataProviderCallExpert,
                            'isAdmin'       => $is_admin,
                            'modelLeadCallExpert'       => $modelLeadCallExpert,
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

            <?php if (Auth::can('lead-view/notes/view', ['lead' => $lead])) : ?>
                <?= $this->render('notes/agent_notes', [
                    'lead' => $lead,
                    'dataProviderNotes'  => $dataProviderNotes,
                    'modelNote'  => $modelNote,
                ]) ?>
            <?php endif;?>
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

    $js = <<<JS

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
       return false;
    });

$(document).on('click','#client-unsubscribe-button', function (e) {
        e.preventDefault();
        let url = $(this).data('unsubscribe-url');        
        $.ajax({
            url: url,               
            success: function(response){
                $.pjax.reload({container: '#pjax-client-info', timeout: 10000, async: false});
                if (Boolean(Number(response.data.action))){
                    new PNotify({title: "Communication", type: "info", text: 'Client communication restricted', hide: true});
                } else {
                    new PNotify({title: "Communication", type: "info", text: 'Client communication allowed', hide: true});
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
