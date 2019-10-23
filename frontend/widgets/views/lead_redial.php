<?php

use common\models\Lead;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JqueryAsset;

/** @var Lead $lead */
/** @var View $this */
/** @var string $phoneFrom */
/** @var string $phoneTo */
/** @var int $projectId */
/** @var int $redialAutoTakeSeconds */

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => $this::POS_HEAD,
    'depends' => [JqueryAsset::class]
]);

?>
<div class="row">

    <div class="text-center badge badge-warning call-status" style="font-size: 35px; display: none">
        <span id="text-status-call"></span>
    </div>

    <p></p>

    <hr>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-list"></i> Lead <?= $lead->id ?></h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $lead,
                    'attributes' => [
                        'uid',
                        [
                            'attribute' => 'client.name',
                            'header' => 'Client name',
                            'format' => 'raw',
                            'value' => static function (Lead $model) {
                                if ($model->client) {
                                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                                    if ($clientName === 'Client Name') {
                                        $clientName = '- - - ';
                                    } else {
                                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                                    }
                                } else {
                                    $clientName = '-';
                                }

                                return $clientName;
                            },
                            'options' => ['style' => 'width:160px'],
                        ],

                        [
                            'attribute' => 'client.phone',
                            'header' => 'Client Phones',
                            'format' => 'raw',
                            'value' => static function (Lead $model)  {
                                if ($model->client && $model->client->clientPhones) {
                                    $str = '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                                }
                                return $str ?? '-';
                            },
                            'options' => ['style' => 'width:180px'],
                        ],
                        [
                            'attribute' => 'status',
                            'value' => static function (Lead $model) {
                                return $model->getStatusName(true);
                            },
                            'format' => 'html',
                        ],
                        [
                            'attribute' => 'project_id',
                            'value' => static function (Lead $model) {
                                return $model->project ? $model->project->name : '-';
                            },
                        ],
                        [
                            'attribute' => 'source_id',
                            'value' => static function (Lead $model) {
                                return $model->source ? $model->source->name : '-';
                            },
                        ],
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $lead,
                    'attributes' => [
                        [
                            'attribute' => 'trip_type',
                            'value' => static function (Lead $model) {
                                return $model->getFlightTypeName();
                            },
                        ],
                        [
                            'attribute' => 'cabin',
                            'value' => static function (Lead $model) {
                                return $model->getCabinClassName();
                            },
                        ],
                        'offset_gmt',
                        [
                            'label' => 'Client time',
                            'format' => 'raw',
                            'value' => static function (Lead $model) {
                                return $model->getClientTime2();
                            },
                        ],
                        [
                            'attribute' => 'created',
                            'value' => static function (Lead $model) {
                                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                            },
                            'format' => 'raw',
                        ],
                        [
                            'label' => 'Pending Time',
                            'value' => static function (Lead $model) {
                                $createdTS = strtotime($model->created);

                                $diffTime = time() - $createdTS;
                                $diffHours = (int)($diffTime / (60 * 60));

                                return ($diffHours > 3 && $diffHours < 73) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                            },
                            'options' => [
                                'style' => 'width:180px'
                            ],
                            'format' => 'raw',
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="timer-countdown" style="display: none ">
        <div class="countdown text-center badge badge-warning" style="font-size: 35px">
            <i class="fa fa-clock-o"></i> <span id="clock">00:00</span>
        </div>
        <button id="take-btn" class="btn btn-success" style="font-size: 25px; margin-top: 5px">Take</button>
        <button id="cancel-take-btn" class="btn btn-primary" style="font-size: 25px; margin-top: 5px">Cancel</button>
    </div>
</div>

<?php

$urlLeadView = Url::to(['lead/view', 'gid' => $lead->gid]);
$urlLeadTake = Url::to(['lead-redial/take', 'gid' => $lead->gid]);

$js =<<<JS

leadRedialCall();

function leadRedialCall() {
    $("#text-status-call").html('Processing ...');
    $('.call-status').show();
    webCallLeadRedial('{$phoneFrom}', '{$phoneTo}', {$projectId}, {$lead->id}, 'web-call');  
}

$("#take-btn").on('click', function (e) {
    leadRedialTake();
});

function hideTimerCountdown() {
    $('#clock').hide();
    $('#cancel-take-btn').hide();
    $('#take-btn').hide();
    $('.countdown').hide();
    $('.timer-countdown').hide();
}

function showTimerCountdown() {
    $('#clock').html('00:00').show();
    $('#cancel-take-btn').show();
    $('#take-btn').show();
    $('.countdown').show();
    $('.timer-countdown').show();
}

$("#cancel-take-btn").on('click', function (e) {
    $('#clock').countdown('remove');
    hideTimerCountdown();
});

function leadRedialTake() {
    $('#cancel-take-btn').hide();
    $('#take-btn').hide();
    $('#clock').countdown('stop');
    new PNotify({title: "Take Lead", type: "info", text: 'Wait', hide: true});
    $.ajax({
        type: 'get',
        url: '{$urlLeadTake}'
    })
    .done(function(data) {
        hideTimerCountdown();
        if (data.success) {
             openInNewTab();
             let text = 'Lead taken!';
             if (data.message) {
                text = data.message;
            }
             new PNotify({title: "Take Lead", type: "success", text: text, hide: true});
        } else {
           let text = 'Error. Try again later';
           if (data.message) {
               text = data.message;
           }
            new PNotify({title: "Take Lead", type: "error", text: text, hide: true});
        }
    })
    .fail(function() {
        hideTimerCountdown();
        new PNotify({title: "Take lead", type: "error", text: 'Try again later.', hide: true});
    })
}

function webCallLeadRedialUpdate(obj) {
    
        console.log(obj.status);
        
        if(obj.status !== undefined) {
            if (obj.leadId != '{$lead->id}') {
                return;
            }
            if (obj.status === 'Ringing') {
                $("#text-status-call").html('Ringing ...');
            } else if (obj.status === 'In progress') {
                showTimerCountdown();
                $("#text-status-call").html('In progress ...');
                startTimer({$redialAutoTakeSeconds});
            } else if (obj.status === 'Completed') {
                $("#text-status-call").html('Completed');
            } else if (obj.status === 'Busy') {
                $("#text-status-call").html('Busy');
            } else if (obj.status === 'No answer') {
                $("#text-status-call").html('No answer');
            } else if (obj.status === 'Failed') {
                $("#text-status-call").html('Failed');
            } else if (obj.status === 'Canceled') {
                $("#text-status-call").html('Canceled');
            } else { 
                $("#text-status-call").html('Undefined status: ' + obj.status);
            }
        }
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
        let url = '{$urlLeadView}';
        //var strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no";
        let windowObjectReference = window.open(url, 'window' + {$lead->id}); //, strWindowFeatures);
        windowObjectReference.focus();
}
    
JS;

$this->registerJs($js);
