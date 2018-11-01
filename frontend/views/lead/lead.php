<?php
/**
 * @var $leadForm LeadForm
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;
use yii\bootstrap\Modal;

//$bundle = \frontend\themes\gentelella\assets\LeadAsset::register($this);
//$this->registerCssFile('/css/style-req.css');
$userId = Yii::$app->user->id;

$is_manager = false;
if(Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) {
    $is_manager = true;
}

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
JS;

    $this->registerJs($js);
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
                printf(" <a title=\"%s\" href=\"%s\">(Cloned from %s)</a> ",
                    "Clone reason: ".$lead->description,
                    \yii\helpers\Url::to([
                    'lead/quote',
                    'type' => 'processing',
                    'id' => $lead->clone_id
                ]),$lead->clone_id);
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
                        <strong>Client <i class="fa fa-clock-o"></i>:</strong>
                        <?= $leadForm->getLead()->getClientTime(); ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>UID:</strong>
                        <span><?= Html::a(sprintf('%08d', $leadForm->getLead()->uid), '#', ['id' => 'view-flow-transition']) ?></span>
                    </div>

                    <div class="page-header__general-item">
                        <strong>Market:</strong>
                        <span><?= $leadForm->getLead()->project->name.' - '.$leadForm->getLead()->source->name?></span>
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

        <div class="sl-request-content">
            <?= \common\widgets\Alert::widget() ?>

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


                <?= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
                ]); ?>

            <?php endif; ?>

            <?= $this->render('partial/_flightDetails', [
                'leadForm' => $leadForm
            ]);
            ?>

            <?//php \yii\widgets\Pjax::begin(); ?>




            <?//php \yii\widgets\Pjax::end() ?>

			<div id="search-result">
				<div id="search-preloader" class="hidden">
                    <div class="preloader">
                        <span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span>
                        <div class="preloader__text">Search Loading...</div>
                    </div>
                </div>
                <div class="content"></div>
			</div>

            <?php if (!$leadForm->getLead()->isNewRecord && count($leadForm->getLead()->getQuotes())) {
                echo $this->render('partial/_quotes', [
                    'quotes' => array_reverse($leadForm->getLead()->getQuotes()),
                    'lead' => $leadForm->getLead(),
                    'leadForm' => $leadForm
                ]);
            } ?>


            <?php if (!$leadForm->getLead()->isNewRecord) : ?>

                <?/*= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
                ]);*/ ?>

                <?= $this->render('partial/_notes', [
                    'notes' => $leadForm->getLead()->getNotes()
                ]); ?>
                <div class="panel panel-neutral panel-wrapper history-block">
                    <div class="panel-heading collapsing-heading">
                        <a data-toggle="collapse" href="#agents-activity-logs" aria-expanded="false"
                           class="collapsing-heading__collapse-link collapsed">
                            Activity Logs
                            <i class="collapsing-heading__arrow"></i>
                        </a>
                    </div>
                    <div class="collapse" id="agents-activity-logs" aria-expanded="false" style="">
                        <?= $this->render('partial/_leadLog', [
                            'logs' => $leadForm->getLead()->getLogs()
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <aside class="sidebar right-sidebar sl-right-sidebar">
    	 <?php if($leadForm->getLead()->status == \common\models\Lead::STATUS_FOLLOW_UP && $leadForm->getLead()->employee_id != Yii::$app->user->id && $is_manager):?>

            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>Client information is not available for this status (FOLLOW UP)!</p>
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