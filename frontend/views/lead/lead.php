<?php
/**
 * @var $leadForm LeadForm
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;

if (!$leadForm->getLead()->isNewRecord) {
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
                    }, 10000);
                }
            })
            .fail(function () {
                setTimeout(function() {
                    checkRequestUpdates('$checkUpdatesUrl');
                }, 10000);
            });
    }
    setTimeout(function() {
        checkRequestUpdates('$checkUpdatesUrl');
    }, 10000);
JS;

    $this->registerJs($js);
}

?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title"><?= Html::encode($this->title) ?></h2>
            <div class="page-header__general">
                <?php if ($leadForm->getLead()->isNewRecord) : ?>
                    <div class="page-header__general-item">
                        <strong>Lead Status:</strong>
                        <span id="status-label"><span class="label status-label label-info">New</span></span>
                    </div>
                <?php else : ?>
                    <?php if (!empty($leadForm->getLead()->employee_id)) : ?>
                        <div class="page-header__general-item">
                            <strong>Assigned to Agent: </strong>
                            <?= $leadForm->getLead()->employee->username ?>
                        </div>
                    <?php endif; ?>
                    <div class="page-header__general-item">
                        <strong>Rating:</strong>
                        <?= $this->render('partial/_rating', [
                            'lead' => $leadForm->getLead()
                        ]) ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>Client Time:</strong>
                        <?= $leadForm->getLead()->getClientTime(); ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>Lead Status:</strong>
                        <?= $leadForm->getLead()->getStatusLabel() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="main-sidebars">
    <aside class="sidebar left-sidebar sl-client-sidebar">
        <?= $this->render('partial/_client', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>

    <div class="panel panel-main">
        <?= $this->render('partial/_actions', [
            'leadForm' => $leadForm
        ]);
        ?>

        <div class="sl-request-content">
            <?= \common\widgets\Alert::widget() ?>

            <?= $this->render('partial/_flightDetails', [
                'leadForm' => $leadForm
            ]);
            ?>

            <?php if (!$leadForm->getLead()->isNewRecord && count($leadForm->getLead()->getQuotes())) {
                echo $this->render('partial/_quotes', [
                    'quotes' => array_reverse($leadForm->getLead()->getQuotes()),
                    'lead' => $leadForm->getLead()
                ]);
            } ?>

            <?php if (!$leadForm->getLead()->isNewRecord) : ?>
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
        <?= $this->render('partial/_preferences', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>
</div>
