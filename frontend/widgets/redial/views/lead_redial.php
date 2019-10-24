<?php

use common\models\Lead;
use frontend\widgets\redial\LeadRedialViewWidget;
use frontend\widgets\redial\RedialUrl;
use frontend\widgets\redial\ViewUrl;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JqueryAsset;
use yii\widgets\Pjax;

/** @var View $this */
/** @var Lead $lead */
/** @var RedialUrl $viewUrl */
/** @var RedialUrl $takeUrl */
/** @var string $pjaxListContainerId */

/** @var string $phoneFrom */
/** @var string $phoneTo */
/** @var int $projectId */
/** @var int $redialAutoTakeSeconds */

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => $this::POS_HEAD,
    'depends' => [JqueryAsset::class]
]);

?>
    <div id="redial-call-box">

        <div class="row">

            <div class="col-md-12">
                <div id="redial-lead-call-status-block" style="display: none">

                    <div class="text-center badge badge-warning" style="font-size: 35px;">
                        <span id="redial-lead-call-status-block-text"></span>
                    </div>

                </div>
                <p></p>
                <hr>
            </div>

            <div class="col-md-6">
                <?php Pjax::begin(['id' => 'redial-lead-view-block-pjax', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                      <?= LeadRedialViewWidget::widget(['lead' => $lead]) ?>
                <?php Pjax::end(); ?>
            </div>
            <div class="col-md-6"></div>

            <div class="col-md-12">
                <button id="redial-lead-actions-block-call" class="btn btn-success" style="font-size: 25px; margin-top: 5px">
                    Call
                </button>

                <div id="redial-lead-actions-block">

                    <div id="redial-lead-actions-block-timer-countdown" style="display: none ">
                        <div id="countdown-clock text-center badge badge-warning" style="font-size: 35px">
                            <i class="fa fa-clock-o"></i> <span id="clock">00:00</span>
                            <button id="redial-lead-actions-take" class="btn btn-success" style="font-size: 25px; ">
                                Take
                            </button>
                            <button id="redial-lead-actions-take-cancel" class="btn btn-primary" style="font-size: 25px; ">
                                Cancel
                            </button>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

<?php

$leadViewUrl = Url::to(['lead/view', 'gid' => $lead->gid]);

$js = <<<JS

$("#redial-lead-actions-block-call").on('click', function (e) {
    $(this).hide();
    leadRedialCall();
});

$("#redial-lead-actions-take").on('click', function (e) {
    leadRedialTake();
});

$("#redial-lead-actions-take-cancel").on('click', function (e) {
    $('#clock').countdown('remove');
    hideActionBlock();
});

function showActionBlock() {
    $('#redial-lead-actions-block *').show();
}

function hideActionBlock() {
    $('#redial-lead-actions-block *').hide();
}

function leadRedialCall() {
    $("#redial-lead-call-status-block-text").html('Processing ...');
    $('#redial-lead-call-status-block').show();
    webCallLeadRedial('{$phoneFrom}', '{$phoneTo}', {$projectId}, {$lead->id}, 'web-call');  
}

function callInProgress() {
    $('#clock').html('00:00');
    $("#redial-lead-call-status-block-text").html('In progress ...');
    showActionBlock();
    startTimer({$redialAutoTakeSeconds});
}

function leadRedialTake() {
    $('#clock').countdown('stop');
    $('#redial-lead-actions-take').hide();
    $('#redial-lead-actions-take-cancel').hide();        
    new PNotify({title: "Take Lead", type: "info", text: 'Wait', hide: true});
    $.ajax({
        type: '{$takeUrl->method}',
        url: '{$takeUrl->url}',
        data: {$takeUrl->getData()}
    })
    .done(function(data) {
        hideActionBlock();
        if (data.success) {
             openInNewTab();
             let text = 'Lead taken!';
             if (data.message) {
                text = data.message;
             }
             new PNotify({title: "Take Lead", type: "success", text: text, hide: true});
             reloadLeadViewContainer();
             reloadListContainer();
        } else {
           let text = 'Error. Try again later';
           if (data.message) {
               text = data.message;
           }
           new PNotify({title: "Take Lead", type: "error", text: text, hide: true});
        }
    })
    .fail(function() {
        hideActionBlock();
        new PNotify({title: "Take lead", type: "error", text: 'Try again later.', hide: true});
    })
}

function webCallLeadRedialUpdate(obj) {
    
        console.log(obj.status);
        
        if(obj.status !== undefined) {
            if (obj.leadId != '{$lead->id}') {
                return;
            }
            reloadListContainer();
            if (obj.status === 'Ringing') {
                $("#redial-lead-call-status-block-text").html('Ringing ...');
            } else if (obj.status === 'In progress') {
                callInProgress();                
            } else if (obj.status === 'Completed') {
                $("#redial-lead-call-status-block-text").html('Completed');
            } else if (obj.status === 'Busy') {
                $("#redial-lead-call-status-block-text").html('Busy');
            } else if (obj.status === 'No answer') {
                $("#redial-lead-call-status-block-text").html('No answer');
            } else if (obj.status === 'Failed') {
                $("#redial-lead-call-status-block-text").html('Failed');
            } else if (obj.status === 'Canceled') {
                $("#redial-lead-call-status-block-text").html('Canceled');
            } else { 
                $("#redial-lead-call-status-block-text").html('Undefined status: ' + obj.status);
            }
        }
}

function reloadListContainer() {
  $.pjax.reload({container: '#{$pjaxListContainerId}', async: false});
}

function reloadLeadViewContainer() {
  $.pjax.reload({
        container: '#redial-lead-view-block-pjax', 
        async: false, 
        push: false, 
        replace: false, 
        url: '{$viewUrl->url}', 
        type: '{$viewUrl->method}',
        data: {$viewUrl->getData()}
    });
}

function startTimer(sec) {
    let seconds = new Date().getTime() + (1000 * sec);
    $('#clock').countdown(seconds)
        .on('update.countdown', function(event) {
            let format = '%M:%S';
            $(this).html(event.strftime(format));
        })
        .on('finish.countdown', function(event) {
             $('#clock').html('00:00');
             leadRedialTake();
        });
}

function openInNewTab() {
        let url = '{$leadViewUrl}';
        //var strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no";
        let windowObjectReference = window.open(url, 'window' + {$lead->id}); //, strWindowFeatures);
        windowObjectReference.focus();
}
    
JS;

$this->registerJs($js);
