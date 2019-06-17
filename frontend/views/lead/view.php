<?php
/**
 * @var $leadForm LeadForm
 * @var $comForm \frontend\models\CommunicationForm
 * @var $previewEmailForm \frontend\models\LeadPreviewEmailForm
 * @var $previewSmsForm \frontend\models\LeadPreviewSmsForm
 * @var $quotesProvider \yii\data\ActiveDataProvider
 * @var $dataProviderCommunication \yii\data\ActiveDataProvider
 * @var $dataProviderCallExpert \yii\data\ActiveDataProvider
 * @var $enableCommunication boolean
 * @var $modelLeadCallExpert \common\models\LeadCallExpert
 * @var $itineraryForm \sales\forms\lead\ItineraryEditForm
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;

$bundle = \frontend\themes\gentelella\assets\AssetLeadCommunication::register($this);

//$this->registerCssFile('/css/style-req.css');
$userId = Yii::$app->user->id;
$user = Yii::$app->user->identity;

$is_manager = false;
$is_admin = $user->canRole('admin');
$is_qa = $user->canRole('qa');
$is_supervision = $user->canRole('supervision');

if($is_admin || $is_supervision) {
    $is_manager = true;
}

$lead = $leadForm->getLead();
?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title">
            <?= Html::encode($this->title) ?>
            <?php
                if($lead->clone_id) {
                    $cloneLead = \common\models\Lead::findOne($lead->clone_id);
                    if($cloneLead) {
                        echo \yii\helpers\Html::a('(Cloned from ' . $lead->clone_id . ' )', ['lead/view', 'gid' => $cloneLead->gid], ['title' => 'Clone reason: ' . $lead->description]);
                    }
                }
            ?>
            <?php if ($leadForm->getLead()->isNewRecord) : ?>
            	<span class="label status-label label-info">New</span>
            <?php else:?>
            	<?= $leadForm->getLead()->getStatusLabel() ?>
            <?php endif;?>
            </h2>
            <div class="page-header__general">
                <?php if (!$leadForm->getLead()->isNewRecord) : ?>
                    <?php if (!empty($leadForm->getLead()->employee_id)) : ?>
                        <div class="page-header__general-item">
                            <strong>Assigned to:</strong>
                            <i class="fa fa-user"></i> <?= $leadForm->getLead()->employee->username ?>
                        </div>
                    <?php endif; ?>
                    <div class="page-header__general-item">
                        <strong>Client:</strong>
                        <?= $leadForm->getLead()->getClientTime2(); ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>UID:</strong>
                        <span><?= Html::a($leadForm->getLead()->uid, '#', ['id' => 'view-flow-transition']) ?></span>
                    </div>

                    <div class="page-header__general-item">
                        <strong>Market:</strong>
                        <span><?= (($leadForm->getLead()->project)?$leadForm->getLead()->project->name:'').
                        (($leadForm->getLead()->source)?' - '.$leadForm->getLead()->source->name:'')?></span>
                    </div>
                    <div class="page-header__general-item">
                        <?= $this->render('partial/_rating', [
                            'lead' => $leadForm->getLead()
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="main-sidebars">
    <div class="panel panel-main">
        <?= $this->render('partial/_actions', [
            'leadForm' => $leadForm
        ]);
        ?>

        <div class="col-md-12">
            <?= \common\widgets\Alert::widget() ?>
            <br>
        </div>

        <div class="col-md-7">

            <?php \yii\widgets\Pjax::begin(['id' => 'pj-itinerary', 'enablePushState' => false, 'timeout' => 10000])?>
                <?= $this->render('partial/_flightDetails', [
                    'itineraryForm' => $itineraryForm
                ]) ?>
            <?php \yii\widgets\Pjax::end()?>

            <?php if (!$leadForm->getLead()->isNewRecord) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!$leadForm->getLead()->l_answered): ?>

                            <?php if($leadForm->getLead()->status == \common\models\Lead::STATUS_PROCESSING):?>
                                <?= Html::a(($leadForm->getLead()->l_answered ? '<i class="fa fa-commenting-o"></i>Make UnAnswered' : '<i class="fa fa-commenting"></i> Make Answered'), ['lead/update2', 'id' => $leadForm->getLead()->id, 'act' => 'answer'], [
                                    'class' => 'btn '.($leadForm->getLead()->l_answered ? 'btn-success' : 'btn-info'),
                                    'data-pjax' => false,
                                    'data' => [
                                        'confirm' => 'Are you sure?',
                                        'method' => 'post',
                                        'pjax' => 0
                                    ],
                                ]) ?>
                            <? else: ?>
                                <span class="badge badge-warning"><i class="fa fa-commenting-o"></i> ANSWERED: false</span>
                            <? endif;?>

                        <? else: ?>
                            <span class="badge badge-success"><i class="fa fa-commenting-o"></i> ANSWERED: true</span>
                        <? endif; ?>

                        <?php if($is_manager): ?>
                            <span class="badge badge-info" title="Grade"><i class="fa fa-retweet"></i> GRADE: <?=$leadForm->getLead()->l_grade?></span>
                        <? endif; ?>
                    </div>

                </div>
                <br>
            <?php endif; ?>



			<?php if (!$leadForm->getLead()->isNewRecord):?>

                <?= $this->render('quotes/quote_list', [
                        'dataProvider' => $quotesProvider,
                        'lead' => $lead,
                        'leadForm' => $leadForm,
                        'is_manager' => $is_manager,
                ]); ?>

            <?php endif;?>

            <?php if (!$leadForm->getLead()->isNewRecord) : ?>

                <?/*= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
                ]);*/ ?>

                <?= $this->render('partial/_notes', [
                    'notes' => $leadForm->getLead()->notes
                ]); ?>

                <?/*= $this->render('partial/_leadLog', [
                    'logs' => $leadForm->getLead()->leadLogs
                ]);*/ ?>

            <?php endif; ?>

        </div>


        <div class="col-md-5">
            <?php if (!$leadForm->getLead()->isNewRecord) : ?>

                <?= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
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

                <?= $this->render('call-expert/lead_call_expert', [
                    'lead' => $leadForm->getLead(),
                    'comForm'       => $comForm,
                    'leadId'        => $lead->id,
                    'dataProvider'  => $dataProviderCallExpert,
                    'isAdmin'       => $is_admin,
                    'modelLeadCallExpert'       => $modelLeadCallExpert,
                ]) ?>


            <?php endif;?>
        </div>
    </div>

	<aside class="sidebar right-sidebar sl-right-sidebar">
    	 <?php if($leadForm->mode === $leadForm::VIEW_MODE && (!$is_admin && !$is_qa && !$is_supervision)):?>
			<div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>Client information is not available in VIEW MODE, please take lead!</p>
            </div>

    	 <?php elseif(!$is_manager && !$is_qa && ( $leadForm->getLead()->status == \common\models\Lead::STATUS_FOLLOW_UP || ($leadForm->getLead()->status == \common\models\Lead::STATUS_PENDING && !$leadForm->getLead()->isNewRecord) ) && $leadForm->getLead()->employee_id != Yii::$app->user->id):?>

            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>Client information is not available for this status (<?=strtoupper($leadForm->getLead()->getStatusName())?>)!</p>
            </div>

        <? else: ?>
            <?= $this->render('partial/_client', [
                'leadForm' => $leadForm
            ]);
            ?>
        <? endif; ?>
        <?= $this->render('partial/_preferences', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>
</div>

<?php

if (!$leadForm->getLead()->isNewRecord) {
    $flowTransitionUrl = \yii\helpers\Url::to([
        'lead/flow-transition',
        'leadId' => $leadForm->getLead()->id
    ]);

    $checkUpdatesUrl = \yii\helpers\Url::to([
        'lead/check-updates',
        'leadId' => $leadForm->getLead()->id,
        'lastUpdate' => date('Y-m-d H:i:s')
    ]);

    $js = <<<JS
    function checkRequestUpdates(checkUrl) {
        $.get(checkUrl)
            .done(function (data) {
                if (data.logs.length != 0) {
                    $('#agents-activity-logs').html(data.logs);
                }
                if (data.needRefresh) {
                    var modal = $('#modal-error');
                    modal.find('.modal-body').html(data.content);
                    modal.modal({
                        backdrop: 'static',
                        show: true
                    });
                } else {
                    setTimeout(function() {
                        checkRequestUpdates(data.checkUpdatesUrl);
                    }, 120000);
                }
            })
            .fail(function () {
                setTimeout(function() {
                    checkRequestUpdates('$checkUpdatesUrl');
                }, 120000);
            });
    }
    setTimeout(function() {
        checkRequestUpdates('$checkUpdatesUrl');
    }, 120000);

    $('#view-flow-transition').click(function() {
        $('#preloader').removeClass('hidden');
        var editBlock = $('#get-request-flow-transition');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load('$flowTransitionUrl', function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            editBlock.modal('show');
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
            $.pjax.reload({container: '#quotes_list', timeout: 10000});     
         }
     });
    
JS;

    $this->registerJs($js);
}