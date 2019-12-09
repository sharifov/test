<?php
/**
 * @var $leadForm LeadForm
 * @var $comForm \frontend\models\CommunicationForm
 * @var $previewEmailForm \frontend\models\LeadPreviewEmailForm
 * @var $previewSmsForm \frontend\models\LeadPreviewSmsForm
 * @var $quotesProvider \yii\data\ActiveDataProvider
 * @var $dataProviderCommunication \yii\data\ActiveDataProvider
 * @var $dataProviderCallExpert \yii\data\ActiveDataProvider
 * @var $dataProviderNotes \yii\data\ActiveDataProvider
 * @var $enableCommunication boolean
 * @var $modelLeadCallExpert \common\models\LeadCallExpert
 * @var  $modelNote \common\models\Note
 * @var $modelLeadChecklist \common\models\LeadChecklist
 * @var $dataProviderChecklist \yii\data\ActiveDataProvider
 * @var $itineraryForm \sales\forms\lead\ItineraryEditForm
 */

use frontend\models\LeadForm;

\frontend\themes\gentelella\assets\AssetLeadCommunication::register($this);

// $this->params['breadcrumbs'][] = $this->title;

$userId = Yii::$app->user->id;
$user = Yii::$app->user->identity;

$is_admin = $user->isAdmin();
$is_qa = $user->isQa();
$is_supervision = $user->canRole('supervision');

if($is_admin || $is_supervision) {
    $is_manager = true;
} else {
    $is_manager = false;
}

$lead = $leadForm->getLead();
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

            <?= $this->render('products/_products', [
                'lead' => $lead,
                'itineraryForm' => $itineraryForm,
                'quotesProvider' => $quotesProvider,
                'leadForm' => $leadForm,
                'is_manager' => $is_manager,
            ]) ?>


            <?= $this->render('offers/lead_offers', [
                'lead' => $lead,
                'leadForm' => $leadForm,
                'is_manager' => $is_manager,
            ]) ?>


        </div>
        <div class="col-md-6">
            <?php if($leadForm->mode === $leadForm::VIEW_MODE && (!$is_admin && !$is_qa && !$is_supervision) && !$lead->isOwner(Yii::$app->user->id)):?>
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Client information is not available in VIEW MODE, please take lead!</p>
                </div>

            <?php elseif(!$is_manager && !$is_qa && ( $lead->isFollowUp() || ($lead->isPending() && !$lead->isNewRecord) ) && !$lead->isOwner(Yii::$app->user->id)):?>

                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Client information is not available for this status (<?=strtoupper($lead->getStatusName())?>)!</p>
                </div>

            <?php else: ?>

                <?= $this->render('client-info/client_info', [
                    'lead' => $lead,
                    'leadForm' => $leadForm,
                    'is_manager' => $is_manager,
                ]) ?>

            <?php endif;?>



            <?php if($leadForm->mode === $leadForm::VIEW_MODE && (!$is_admin && !$is_qa && !$is_supervision) && $lead->employee_id !== Yii::$app->user->id):?>
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Lead Preferences is not available in VIEW MODE, please take lead!</p>
                </div>
            <?php elseif(!$is_manager && !$is_qa && ( $lead->isFollowUp() || ($lead->isPending() && !$lead->isNewRecord) ) && $lead->employee_id !== Yii::$app->user->id):?>

                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>Client information is not available for this status (<?=strtoupper($lead->getStatusName())?>)!</p>
                </div>
            <?php else: ?>
                <div id="lead-preferences">
                    <?= $this->render('partial/_lead_preferences', [
                        'lead' => $lead,
                        'leadForm' => $leadForm
                    ]) ?>
                </div>
            <?php endif; ?>


            <?= $this->render('checklist/lead_checklist', [
                'lead' => $lead,
                'comForm'       => $comForm,
                'leadId'        => $lead->id,
                'dataProvider'  => $dataProviderChecklist,
                'isAdmin'       => $is_admin,
                'modelLeadChecklist'       => $modelLeadChecklist,
            ]) ?>

            <?= $this->render('partial/_task_list', [
                'lead' => $lead
            ]); ?>

            <?php if ($enableCommunication) : ?>
                <?= $this->render('communication/lead_communication', [
                    'leadForm'      => $leadForm,
                    'previewEmailForm' => $previewEmailForm,
                    'previewSmsForm' => $previewSmsForm,
                    'comForm'       => $comForm,
                    'leadId'        => $lead->id,
                    'dataProvider'  => $dataProviderCommunication,
                    'isAdmin'       => $is_admin
                ]);
                ?>
            <?php else: ?>
                <div class="alert alert-warning" role="alert">You do not have access to view Communication block messages.</div>
            <?php endif;?>


            <?//php \yii\helpers\VarDumper::dump(Yii::$app->user->identity->callExpertCountByShiftTime) ?>



            <?php if(Yii::$app->user->identity->isAllowCallExpert): ?>
                <?= $this->render('call-expert/lead_call_expert', [
                    'lead' => $lead,
                    'comForm'       => $comForm,
                    'leadId'        => $lead->id,
                    'dataProvider'  => $dataProviderCallExpert,
                    'isAdmin'       => $is_admin,
                    'modelLeadCallExpert'       => $modelLeadCallExpert,
                ]) ?>
            <?php endif;?>



        </div>

        <div class="col-md-6">

<!--            --><?php //if (!$lead->isNewRecord) : ?>
<!--                <div class="row">-->
<!--                    <div class="col-md-12">-->
<!--                        --><?php //if(!$lead->l_answered): ?>
<!---->
<!--                            --><?php //if($lead->isProcessing()):?>
<!--                                --><?//= Html::a(($lead->l_answered ? '<i class="fa fa-commenting-o"></i>Make UnAnswered' : '<i class="fa fa-commenting"></i> Make Answered'), ['lead/update2', 'id' => $lead->id, 'act' => 'answer'], [
//                                    'class' => 'btn '.($lead->l_answered ? 'btn-success' : 'btn-info'),
//                                    'data-pjax' => false,
//                                    'data' => [
//                                        'confirm' => 'Are you sure?',
//                                        'method' => 'post',
//                                        'pjax' => 0
//                                    ],
//                                ]) ?>
<!--                            --><?php //else: ?>
<!--                                <span class="badge badge-warning"><i class="fa fa-commenting-o"></i> ANSWERED: false</span>-->
<!--                            --><?php //endif;?>
<!---->
<!--                        --><?php //else: ?>
<!--                            <span class="badge badge-success"><i class="fa fa-commenting-o"></i> ANSWERED: true</span>-->
<!--                        --><?php //endif; ?>
<!---->
<!--                    </div>-->
<!---->
<!--                </div>-->
<!--            --><?php //endif; ?>





<!--                --><?//= $this->render('quotes/quote_list', [
//                    'dataProvider' => $quotesProvider,
//                    'lead' => $lead,
//                    'leadForm' => $leadForm,
//                    'is_manager' => $is_manager,
//                ]); ?>





                <?= $this->render('notes/agent_notes', [
                    'lead' => $lead,
                    'dataProviderNotes'  => $dataProviderNotes,
                    'modelNote'  => $modelNote,
                ]) ?>


        </div>




        <div class="clearfix"></div>
        <br/>
        <br/>

    </div>


</div>

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
    
JS;

$this->registerJs($jsCode);
