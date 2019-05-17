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
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;

$bundle = \frontend\themes\gentelella\assets\AssetLeadCommunication::register($this);

//$this->registerCssFile('/css/style-req.css');
$userId = Yii::$app->user->id;

$is_manager = false;
$is_admin = Yii::$app->authManager->getAssignment('admin', $userId);
$is_qa = Yii::$app->authManager->getAssignment('qa', $userId);
$is_supervision = Yii::$app->authManager->getAssignment('supervision', $userId);

if($is_admin || $is_supervision) {
    $is_manager = true;
}
?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title">
            <?= Html::encode($this->title) ?>
            <?php
            $lead = $leadForm->getLead();
            if(!empty($lead->clone_id)){

                $cloneLead = \common\models\Lead::findOne($lead->clone_id);

                /*printf(" <a title=\"%s\" href=\"%s\">(Cloned from %s)</a> ",
                    "Clone reason: ".$lead->description,
                    \yii\helpers\Url::to([
                    'lead/view',
                    'uid' => $cloneLead->uid
                ]),$lead->clone_id);*/

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

        <div class="col-md-12">
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

            <?php endif;?>
        </div>
    </div>

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
    
JS;

    $this->registerJs($js);
}