<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
//use kartik\grid\GridView;
//use yii\grid\GridView;
use \yiister\gentelella\widgets\grid\GridView;
use common\models\Lead;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $checkShiftTime bool */
/* @var $isAgent bool */
/* @var $isAccessNewLead bool */
/* @var $accessLeadByFrequency array */
/* @var $user \common\models\Employee */
/* @var $newLeadsCount integer */

/* @var $leadModel Lead */
/* @var $callModel \common\models\Call */


/* @var $searchModelSegments common\models\search\LeadFlightSegmentSearch */
/* @var $dataProviderSegments yii\data\ActiveDataProvider */
/* @var $isActionFind bool */
/* @var $callData [] */

/* @var $myPendingLeadsCount int */
/* @var $allPendingLeadsCount int */


/* @var $searchModelCall common\models\search\CallSearch */
/* @var $dataProviderCall yii\data\ActiveDataProvider */
/* @var $projectList [] */


$this->title = 'Auto redial';

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js', [
    //'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js', [
    'position' => \yii\web\View::POS_HEAD,
    'depends' => [
        \yii\web\JqueryAsset::class
    ]
]);


$this->params['breadcrumbs'][] = ['label' => 'Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<script>

    var takeTimerId = 0;

    function openInNewTab(url, name) {
        //var strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no";
        var windowObjectReference = window.open(url, 'window' + name); //, strWindowFeatures);
        windowObjectReference.focus();
    }

    function autoredialInit() {
        console.info('autoredialInit()');
        $.pjax.reload({container:'#pjax-auto-redial', 'scrollTo': false});
    }

    function startAutoTake(url, name) {
        takeTimerId = setTimeout(function() { openInNewTab(url, name) }, 20000);
        console.log('Create takeTimerId: ' + takeTimerId);
    }

    function endAutoTake() {
        console.log('endAutoTake, current takeTimerId: ' + takeTimerId);
        console.log('endAutoTake response: ' + clearTimeout(takeTimerId));
    }

    function webCallUpdate(obj) {
        //console.log('--- webCallUpdate ---');
        console.info('webCallUpdate - 3');
        //status: "completed", duration: "1", snr: "3"


        if(obj.status !== undefined) {

            //$('#call_autoredial_status').html(obj.status);

            if (obj.status === 'completed') {
                endAutoTake();
                //stopCall(obj.duration); //updateCommunication();
                autoredialInit();
            } else if (obj.status === 'in-progress') {
                autoredialInit();
                //startCallTimer();
                //$('#div-call-timer').timer('resume');
            } else if (obj.status === 'initiated') {
                endAutoTake();
                //startCall();
            } else if (obj.status === 'busy') {
                endAutoTake();
                //stopCall(0);
                //updateCommunication();
                autoredialInit();
            } else if (obj.status === 'no-answer') {
                //stopCall(0);
                //updateCommunication();
                endAutoTake();
                autoredialInit();
            }
        }

        //console.info('webCallUpdate - 4');

        //$('.click_after_call_update').trigger('click');
    }




    function startTimer(sec) {
        var seconds = new Date().getTime() + (1000 * sec);

        $('#clock').countdown(seconds)
            .on('update.countdown', function(event) {
                var format = '%M:%S';
                /*if(event.offset.totalDays > 0) {
                  format = '%-d day%!d ' + format;
                }
                if(event.offset.weeks > 0) {
                  format = '%-w week%!w ' + format;
                }*/
                $(this).html(event.strftime(format));
            })
            .on('finish.countdown', function(event) {
                //$(this).html('This call has expired!').parent().addClass('disabled');

                /*var phone_to = $('#call-to-number').val();
                var phone_from = $('#call-from-number').val();

                var project_id = $('#call-project-id').val();
                var lead_id = $('#call-lead-id').val();*/

                //$.pjax.reload({container:'#pjax-auto-redial', data: 'act=show', type: 'POST', 'scrollTo': false});


                //$('#web-phone-dial-modal').modal('hide');
                //alert(phone_from + ' - ' + phone_to);


                //$('#web-phone-widget').slideDown();
                //$('.fabs2').hide();

                //webCall(phone_from, phone_to, project_id, lead_id);

            });
    }
</script>
<?php
//$dt = Yii::$app->formatter->asDatetime(strtotime('+1 hour'), 'php:Y/m/d H:i:s'); //date('Y/m/d H:i:s', strtotime('+1 hour')); echo $dt;
//$js = 'startTimer(20);';
//$this->registerJs($js);

?>

    <h1>
        <i class="fa fa-phone-square"></i> <?=\yii\helpers\Html::encode($this->title)?>
    </h1>

    <div class="call-auto-redial">
        <?php Pjax::begin(['id' => 'pjax-auto-redial', 'timeout' => 5000, 'enablePushState' => false/*, 'clientOptions' => ['method' => 'GET']*/]); ?>

        <div class="row top_tiles">

            <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=Lead::find()->where(['status' => Lead::STATUS_PENDING])->count()?>
                    </div>
                    <h3>Total Pending Leads</h3>
                    <p>Total Leads - status Pending</p>
                </div>
            </div>

            <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count">
                        <?=$allPendingLeadsCount?>
                    </div>
                    <h3>Accessed Pending Leads</h3>
                    <p>Accessed all pending Leads (delay, client time, 09:00 - 21:00)</p>
                </div>
            </div>

            <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-bars"></i></div>
                    <div class="count">
                        <?=$myPendingLeadsCount?>
                    </div>
                    <h3>My Pending Leads</h3>
                    <p>Accessed for me by (project, phone, client time, 09:00 - 21:00)</p>
                </div>
            </div>

            <div class="animated flipInY col-md-2 col-sm-6 col-xs-12" title="My Project / Phone List">
                <?php
                if($user->userProjectParams):
                    ?>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Project</th>
                            <th>Phone</th>
                        </tr>
                        <?php
                        $nr = 1;
                        foreach ($user->userProjectParams as $upp):?>
                            <tr>
                                <td><?=Html::encode($upp->uppProject->name)?></td>
                                <td><?=Html::encode($upp->upp_tw_phone_number)?></td>
                            </tr>
                        <? endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>


        <?//=date('Y-m-d H:i:s')?>

        <?php
            //\yii\helpers\VarDumper::dump(Yii::$app->request->get(), 10, true);
            //\yii\helpers\VarDumper::dump(Yii::$app->request->post(), 10, true);
        ?>

        <p>



            <?/*php if($user->userProfile && !$user->userProfile->up_auto_redial):?>
                <?= Html::a('<i class="fa fa-play"></i> Start Call', ['auto-redial', 'act' => 'start'], ['class' => 'btn btn-success']) ?>
            <?php else:*/ ?>
                <?/*= Html::a('<i class="fa fa-stop"></i> Stop Call ('.Yii::$app->formatter->asTime(strtotime($user->userProfile->up_updated_dt)).')', ['auto-redial', 'act' => 'stop'], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ])*/ ?>

            <?//= Html::a('<i class="fa fa-refresh"></i> Auto Redial INIT', ['auto-redial', 'act' => 'init'], ['class' => 'btn btn-info click_after_call_update', 'id' => 'btn-auto-redial-init']) ?>

            <?php if($isActionFind && $leadModel):?>
                <div class="text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-spinner fa-spin"></i> Processing ...
                </div>
            <?php elseif($callModel):?>
                <div class="text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-spinner fa-spin"></i> <?=$callModel->getStatusName()?> <?=$callModel->c_to?> ...
                </div>
            <?php else: ?>

                <?php if($checkShiftTime && $isAccessNewLead):?>
                    <div class="text-center">
                        <?= Html::a('<i class="fa fa-search"></i> Find new Lead and make Call', ['auto-redial', 'act' => 'find'], ['class' => 'btn btn-lg btn-success']) ?>
                    </div>
                <?php endif; ?>

                <?php if($isActionFind):?>
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Sorry! No lead was found.</strong> Last request time: <?=Yii::$app->formatter->asTime(time())?>
                    </div>
                    <?/*<p>Last request time: <?=Yii::$app->formatter->asTime(time())?></p>*/?>
                <?php endif; ?>

                <?/*<div class="countdown text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-clock-o"></i>
                    <span id="clock">00:00</span>
                </div>*/?>
            <?php endif; ?>

            <?//=$this->registerJs('startTimer(10);');?>

            <?/*php endif;*/ ?>
        </p>



        <?php if($user->userProfile):?>
            <hr>
            <div class="row">
                <div class="col-md-6">

                    <?php if($leadModel && !$callModel): ?>
                        <h1>Find new Lead <?=$leadModel->id?></h1>

                        <?php if(isset($callData['error']) && $callData['error']):?>
                            <div class="alert alert-danger" role="alert"><strong>Error:</strong> <?=Html::encode($callData['error'])?></div>
                            <?=$this->registerJs("createNotify('Call Error', '". Html::encode($callData['error'])."', 'error');");?>
                        <?php else: ?>

                            <?php if($callData): ?>

                                <?=$this->registerJs("webCall('". $callData['phone_from']."', '". $callData['phone_to']."', ". $callData['project_id'].", ". $callData['lead_id'].", 'auto-redial');");?>
                                <?=$this->registerJs("startAutoTake('".\yii\helpers\Url::to(['/lead/auto-take', 'gid' => $leadModel->gid])."', '".$leadModel->id."');");?>
                                <?=$this->registerJs('startTimer(20);');?>
                            <?php endif; ?>

                        <?php endif; ?>

                        <div class="hidden">
                            <?php \yii\helpers\VarDumper::dump($callData, 10, true) ?>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <?= \yii\widgets\DetailView::widget([
                                    'model' => $leadModel,
                                    'attributes' => [
                                        //'id',
                                        'uid',
                                        //'client_id',
                                        [
                                            'attribute' => 'client.name',
                                            'header' => 'Client name',
                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) {
                                                if($model->client) {
                                                    $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                                                    if ($clientName === 'Client Name') {
                                                        $clientName = '- - - ';
                                                    } else {
                                                        $clientName = '<i class="fa fa-user"></i> '. Html::encode($clientName);
                                                    }
                                                } else {
                                                    $clientName = '-';
                                                }

                                                return $clientName;
                                            },
                                            'options' => ['style' => 'width:160px'],
                                            //'filter' => \common\models\Employee::getList()
                                        ],

                                        [
                                            'attribute' => 'client.phone',
                                            'header' => 'Client Phones',
                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) use ($isAgent) {
                                                if($model->client && $model->client->clientPhones) {
                                                    if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                                        $str = '- // - // - // -';
                                                    } else {
                                                        $str = '<i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone'));
                                                    }
                                                } else {
                                                    $str = '-';
                                                }

                                                return $str ?? '-';
                                            },
                                            'options' => ['style' => 'width:180px'],
                                        ],


                                       /* [
                                            'attribute' => 'client.email',

                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) use ($isAgent) {

                                                if($model->client && $model->client->clientEmails) {
                                                    if ($isAgent && Yii::$app->user->id !== $model->employee_id) {
                                                        $str = '- // - // - // -';
                                                    } else {
                                                        $str = '<i class="fa fa-envelope"></i> '.implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email'));
                                                    }
                                                } else {
                                                    $str = '-';
                                                }

                                                return $str ?? '-';
                                            },
                                            'options' => ['style' => 'width:180px'],
                                        ],*/

                                        /*[
                                            'attribute' => 'employee_id',
                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                                            },
                                        ],*/

                                        //'employee_id',



                                        [
                                            'attribute' => 'status',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->getStatusName(true);
                                            },
                                            'format' => 'html',

                                        ],
                                        [
                                            'attribute' => 'project_id',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->project ? $model->project->name : '-';
                                            },

                                        ],

                                        [
                                            'attribute' => 'source_id',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->source ? $model->source->name : '-';
                                            },
                                        ],

                                        [
                                            'label' => 'Segments',
                                            'value' => function (\common\models\Lead $model) {

                                                $segments = $model->leadFlightSegments;
                                                $segmentData = [];
                                                if ($segments) {
                                                    foreach ($segments as $sk => $segment) {
                                                        $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin_label . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination_label . ' ('.$segment->departure.')</small>';
                                                    }
                                                }

                                                $segmentStr = implode('<br>', $segmentData);
                                                return '' . $segmentStr . '';
                                                // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                                            },
                                            'format' => 'raw',
                                            'visible' => ! $isAgent,
                                            'contentOptions' => [
                                                'class' => 'text-left'
                                            ],
                                            'options' => [
                                                'style' => 'width:140px'
                                            ]
                                        ],

                                        /*[
                                            'header' => 'Segments',
                                            'value' => function (\common\models\Lead $model) {

                                                $segments = $model->leadFlightSegments;
                                                $segmentData = [];
                                                if ($segments) {
                                                    foreach ($segments as $sk => $segment) {
                                                       $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                                                    }
                                                }

                                                $segmentStr = implode('<br>', $segmentData);
                                                return '' . $segmentStr . '';
                                                // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                                            },
                                            'format' => 'raw',
                                            'contentOptions' => [
                                                'class' => 'text-left'
                                            ],
                                            'options' => [
                                                'style' => 'width:140px'
                                            ]
                                        ],*/



                                        /*[
                                            'header' => 'Client time',
                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->getClientTime2();
                                            },
                                            //'options' => ['style' => 'width:80px'],
                                            //'filter' => \common\models\Employee::getList()
                                        ],*/





                                    ],
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= \yii\widgets\DetailView::widget([
                                    'model' => $leadModel,
                                    'attributes' => [
                                        [
                                            'attribute' => 'trip_type',
                                            'value' => function(\common\models\Lead $model) {
                                                return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                                            },

                                        ],

                                        [
                                            'attribute' => 'cabin',
                                            'value' => function(\common\models\Lead $model) {
                                                return \common\models\Lead::getCabin($model->cabin) ?? '-';
                                            },

                                        ],

                                        /*'project_id',
                                        'source_id',
                                        'trip_type',
                                        'cabin',*/
                                        /*'adults',
                                        'children',
                                        'infants',*/
                                        'offset_gmt',
                                        [
                                            'label' => 'Client time',
                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->getClientTime2();
                                            },
                                            //'options' => ['style' => 'width:80px'],
                                            //'filter' => \common\models\Employee::getList()
                                        ],

                                        //'discount_id',

                                        [
                                            'label' => 'Pax',
                                            'value' => function (\common\models\Lead $model) {
                                                return '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';
                                            },
                                            'format' => 'raw',
                                            //'visible' => ! $isAgent,
                                            /*'contentOptions' => [
                                                'class' => 'text-center'
                                            ],*/

                                        ],


                                        [
                                            'attribute' => 'created',
                                            'value' => function(\common\models\Lead $model) {
                                                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                                            },
                                            'format' => 'raw',
                                        ],
                                        [
                                            //'attribute' => 'pending',
                                            'label' => 'Pending Time',
                                            'value' => function (\common\models\Lead $model) {
                                                $createdTS = strtotime($model->created);

                                                $diffTime = time() - $createdTS;
                                                $diffHours = (int) ($diffTime / (60 * 60));

                                                return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                                            },
                                            'options' => [
                                                'style' => 'width:180px'
                                            ],
                                            'format' => 'raw',
                                        ],


                                        /*[
                                            'attribute' => 'updated',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->updated ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated)) : '-';
                                            },
                                            'format' => 'raw',
                                        ],*/
                                    ],
                                ]) ?>
                            </div>



                        </div>

                        <?/*
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Flight Segments:</h4>
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => $dataProviderSegments,
                                    //'filterModel' => $searchModelSegments,
                                    'columns' => [
                                        //'id',

                                        'origin',
                                        'destination',
                                        [
                                            'attribute' => 'departure',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return '<i class="fa fa-calendar"></i> '.date("Y-m-d", strtotime($model->departure));
                                            },
                                            'format' => 'html',
                                        ],


                                        'origin_label',
                                        'destination_label',

                                    ],
                                ]); ?>


                            </div>
                        </div>*/
                        ?>
                    <?php else: ?>
                        <?/*php
                            if(!Yii::$app->request->isPjax || Yii::$app->request->get('act') === 'start') {
                                $this->registerJs('startTimer(10);');
                            } else {
                                $this->registerJs('startTimer(30);');
                            }*/
                        ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-6">
                    <?/*<h3>Call status: <span class="badge badge-info" id="call_autoredial_status"><?=$callModel ? $callModel->getStatusName() : '-'?></span></h3>*/?>
                    <?php if($callModel): ?>
                        <h1>Call info <?=$callModel->c_id?></h1>

                        <div class="countdown text-center badge badge-warning" style="font-size: 15px">
                            <i class="fa fa-clock-o"></i>
                            <span id="clock">00:<?=(time() - strtotime($callModel->c_created_dt))?></span>
                        </div>

                        <div class="col-md-6">

                            <?= \yii\widgets\DetailView::widget([
                                'model' => $callModel,
                                'attributes' => [
                                    'c_id',
                                    'c_call_sid',
                                    //'c_account_sid',
                                    //'c_call_type_id',
                                    [
                                        'attribute' => 'c_call_type_id',
                                        'value' => function (\common\models\Call $model) {
                                            return $model->getCallTypeName();
                                        },
                                    ],
                                    'c_from',
                                    'c_to',
                                    //'c_sip',
                                    'c_call_status',
                                    //'c_api_version',
                                    //'c_direction',
                                    //'c_forwarded_from',

                                    //'c_parent_call_sid',

                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= \yii\widgets\DetailView::widget([
                                'model' => $callModel,
                                'attributes' => [
                                    'c_caller_name',
                                    'c_call_duration',
                                    //'c_sip_response_code',
                                    //'c_recording_url:url',
                                    //'c_recording_sid',
                                    //'c_recording_duration',
                                    //'c_timestamp',
                                    //'c_uri',
                                    //'c_sequence_number',
                                    //'c_lead_id',
                                    [
                                        'attribute' => 'c_lead_id',
                                        'value' => function (\common\models\Call $model) {
                                            return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['data-pjax' => 0, 'target' => '_blank']) : '';
                                        },
                                        'format' => 'raw'
                                    ],
                                    //'c_created_user_id',
                                    [
                                        'attribute' => 'c_created_user_id',
                                        'value' => function (\common\models\Call $model) {
                                            return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                                        },
                                        'format' => 'raw'
                                    ],
                                    //'c_created_dt',
                                    [
                                        'attribute' => 'c_created_dt',
                                        'value' => function (\common\models\Call $model) {
                                            return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                                        },
                                        'format' => 'raw'
                                    ],
                                    [
                                        'attribute' => 'c_updated_dt',
                                        'value' => function (\common\models\Call $model) {
                                            return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                                        },
                                        'format' => 'raw'
                                    ],
                                    //'c_com_call_id',
                                    //'c_updated_dt',
                                    //'c_project_id',
                                    [
                                        'attribute' => 'c_project_id',
                                        'value' => function (\common\models\Call $model) {
                                            return $model->cProject ? $model->cProject->name : '-';
                                        },
                                        'filter' => \common\models\Project::getList()
                                    ],
                                    //'c_error_message',
                                    //'c_is_new:boolean',
                                    //'c_is_deleted',
                                ],
                            ]) ?>
                        </div>


                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> Not found user profile for User (<?=$user->id?>)
            </div>
        <?php endif; ?>
        <hr>

        <div class="row">
            <?php if(!$checkShiftTime): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads are only available on your shift. (Current You time: <?=Yii::$app->formatter->asTime(time())?>)
                    </div>

                    <?/*php \yii\helpers\VarDumper::dump(Yii::$app->user->identity->getShiftTime(), 10, true)?>
                <?php echo date('Y-m-d H:i:s')*/?>
                </div>
            <?php endif; ?>

            <?php if(!$isAccessNewLead): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> Access is denied - action "take new lead"
                    </div>
                </div>
            <?php endif; ?>


            <?php if(isset($accessLeadByFrequency['access']) && $accessLeadByFrequency['access'] == false): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads will be available in <span id="left-time-countdown" data-elapsed="<?= $accessLeadByFrequency['takeDtUTC']->format('U') - time()?>" data-countdown="<?= $accessLeadByFrequency['takeDtUTC']->format('Y-m-d H:i')?>"><?=Yii::$app->formatter->asTime($accessLeadByFrequency['takeDt'])?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?/*<div class="col-md-12">
                <h3>Last leads:</h3>
            </div>*/?>

        </div>

        <div class="col-md-12">
            <h3>My Last calls:</h3>
        </div>


        <?= \yiister\gentelella\widgets\grid\GridView::widget([
            'dataProvider' => $dataProviderCall,
            //'filterModel' => false, //$searchModelCall,
            //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
            'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
                if ($model->c_call_status === \common\models\Call::CALL_STATUS_BUSY) {
                    return ['class' => 'danger'];
                } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_RINGING || $model->c_call_status === \common\models\Call::CALL_STATUS_QUEUE) {
                    return ['class' => 'warning'];
                } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_COMPLETED) {
                    return ['class' => 'success'];
                }
            },
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'s_is_deleted',

                [
                    'attribute' => 'c_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_id;
                    },
                    'options' => ['style' => 'width: 100px']
                ],

                'c_is_new:boolean',
                //'c_com_call_id',
                //'c_call_sid',
                //'c_account_sid',
                //'c_call_type_id',

                [
                    'attribute' => 'c_call_type_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->getCallTypeName();
                    },
                    'filter' => \common\models\Call::CALL_TYPE_LIST
                ],

                //'c_project_id',

                [
                    'attribute' => 'c_project_id',
                    'value' => function (\common\models\Call $model) {
                        return $model->cProject ? $model->cProject->name : '-';
                    },
                    'filter' => $projectList
                ],


                'c_from',
                'c_to',

                //'c_sip',
                //'c_call_status',
                [
                    'attribute' => 'c_call_status',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_call_status;
                    },
                    'filter' => \common\models\Call::CALL_STATUS_LIST
                ],
                //'c_lead_id',
                [
                    'attribute' => 'c_lead_id',
                    'value' => function (\common\models\Call $model) {
                        return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                    },
                    'format' => 'raw'
                ],
                //'c_api_version',
                //'c_direction',
                //'c_forwarded_from',
                'c_caller_name',
                //'c_parent_call_sid',
                'c_call_duration',
                //'c_sip_response_code',
                //'c_recording_url:url',
                [
                    'attribute' => 'c_recording_url',
                    'value' => function (\common\models\Call $model) {
                        return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                    },
                    'format' => 'raw'
                ],
                //'c_recording_sid',
                'c_recording_duration',
                //'c_timestamp',
                //'c_uri',
                //'c_sequence_number',

                //'c_created_user_id',

                /*[
                    'attribute' => 'c_created_user_id',
                    'value' => function (\common\models\Call $model) {
                        return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                    },
                    'format' => 'raw'
                ],*/

                //'c_created_dt',

                /*[
                    'attribute' => 'c_updated_dt',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                    },
                    'format' => 'raw'
                ],*/

                [
                    'attribute' => 'c_created_dt',
                    'value' => function (\common\models\Call $model) {
                        return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
                    },
                    'format' => 'raw'
                ],


                //'c_updated_dt',

                //'c_error_message',

                //'c_is_deleted:boolean',
                [   'class' => 'yii\grid\ActionColumn',
                    'template' => '{view2}',
                    'buttons' => [
                        'view2' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                                'title' => 'View',
                            ]);
                        },
                        /*'soft-delete' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                                'title' => 'Delete',
                                'data' => [
                                    'confirm' => 'Are you sure you want to delete this SMS?',
                                    //'method' => 'post',
                                ],
                            ]);
                        }*/
                    ],
                ],


            ],
        ]); ?>


        <div class="col-md-12">
            <h3>Pending leads:</h3>
        </div>

        <?php

        $gridColumns = [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'label' => 'Lead ID',
                'value' => function (\common\models\Lead $model) {
                    return $model->id;
                },
                'visible' => ! $isAgent,
                'options' => [
                    'style' => 'width:80px'
                ]
            ],
            [
                //'attribute' => 'pending',
                'label' => 'Pending Time',
                'value' => function (\common\models\Lead $model) {
                    $createdTS = strtotime($model->created);

                    $diffTime = time() - $createdTS;
                    $diffHours = (int) ($diffTime / (60 * 60));

                    return ($diffHours > 3 && $diffHours < 73 ) ? $diffHours.' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                },
                'options' => [
                    'style' => 'width:180px'
                ],
                'format' => 'raw',
                'visible' => ! $isAgent,
            ],

            [
                'attribute' => 'created',
                'value' => function (\common\models\Lead $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },

                'format' => 'raw',
                'options' => [
                    'style' => 'width:180px'
                ],
                'filter' => false,
                'enableSorting' => ! $isAgent
            ],

            [
                // 'attribute' => 'client_id',
                'header' => 'Client',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {

                    if ($model->client) {
                        $clientName = $model->client->first_name . ' ' . $model->client->last_name;
                        if ($clientName === 'Client Name') {
                            $clientName = '- - - ';
                        } else {
                            $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                        }

                        $str = $model->client && $model->client->clientEmails ? '<i class="fa fa-envelope"></i> ' . implode(' <br><i class="fa fa-envelope"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientEmails, 'email', 'email')) . '' : '';
                        $str .= $model->client && $model->client->clientPhones ? '<br><i class="fa fa-phone"></i> ' . implode(' <br><i class="fa fa-phone"></i> ', \yii\helpers\ArrayHelper::map($model->client->clientPhones, 'phone', 'phone')) . '' : '';

                        $clientName .= '<br>' . $str;
                    } else {
                        $clientName = '-';
                    }

                    return $clientName;
                },
                'visible' => ! $isAgent,
                'options' => [
                    'style' => 'width:160px'
                ]
            ],/*
        [
            'attribute' => 'clientTime',
            'label' => 'Client Time',
            'value' => function ($model) {
                return Lead::getClientTime($model['id']);
            },
            'format' => 'raw'
        ], */



            /*[
                'attribute' => 'Request Details',
                'content' => function (\common\models\Lead $model) {
                    $content = '';
                    $content .= $model->getFlightDetails();
                    $content .= ' (<i class="fa fa-male"></i> x' . ($model->adults . '/' . $model->children . '/' . $model->infants) . ')<br/>';

                    $content .= sprintf('<strong>Cabin:</strong> %s', Lead::getCabin($model['cabin']));

                    return $content;
                },
                'format' => 'raw'
            ],*/

            [
                'header' => 'Depart',
                'value' => function (\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;

                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            return date('d-M-Y', strtotime($segment->departure));
                        }
                    }
                    return '-';

                },
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'options' => [
                    'style' => 'width:100px'
                ]
            ],

            [
                'header' => 'Segments',
                'value' => function (\common\models\Lead $model) {

                    $segments = $model->leadFlightSegments;
                    $segmentData = [];
                    if ($segments) {
                        foreach ($segments as $sk => $segment) {
                            $segmentData[] = ($sk + 1) . '. <small>' . $segment->origin . ' <i class="fa fa-long-arrow-right"></i> ' . $segment->destination . '</small>';
                        }
                    }

                    $segmentStr = implode('<br>', $segmentData);
                    return '' . $segmentStr . '';
                    // return $model->leadFlightSegmentsCount ? Html::a($model->leadFlightSegmentsCount, ['lead-flight-segment/index', "LeadFlightSegmentSearch[lead_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'visible' => ! $isAgent,
                'contentOptions' => [
                    'class' => 'text-left'
                ],
                'options' => [
                    'style' => 'width:140px'
                ]
            ],

            [
                'label' => 'Pax',
                'value' => function (\common\models\Lead $model) {
                    return '<span title="adult"><i class="fa fa-male"></i> '. $model->adults .'</span> / <span title="child"><i class="fa fa-child"></i> ' . $model->children . '</span> / <span title="infant"><i class="fa fa-info"></i> ' . $model->infants.'</span>';
                },
                'format' => 'raw',
                'visible' => ! $isAgent,
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'options' => [
                    'style' => 'width:100px'
                ]
            ],


            [
                'attribute' => 'cabin',
                'value' => function (\common\models\Lead $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => false //\common\models\Lead::CABIN_LIST
            ],



            /*[
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return $model->getClientTime();
                },
                'visible' => ! $isAgent,
                //'options' => ['style' => 'width:110px'],

            ],*/


            [
                'header' => 'Client time',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->getClientTime2();
                },
                //'options' => ['style' => 'width:80px'],
                //'filter' => \common\models\Employee::getList()
            ],

            [
                'label' => 'Client time2',
                'format' => 'raw',
                'value' => function(\common\models\Lead $model) {
                    return $model->l_client_time;
                },
                //'options' => ['style' => 'width:80px'],
                //'filter' => \common\models\Employee::getList()
            ],


            [
                'header' => 'Project',
                'attribute' => 'project_id',
                'filter' => false,
                'value' => function (\common\models\Lead $model) {
                    return $model->project ? $model->project->name : '-';
                },
            ],

            [
                'attribute' => 'status',
                'value' => function (\common\models\Lead $model) {
                    return $model->getStatusLabel(); //Lead::STATUS_LIST[$model->status] ?? '-';
                },
                'filter' => Lead::STATUS_LIST,
                'format' => 'raw',
            ],

            [
                'attribute' => 'l_call_status_id',
                'value' => function (\common\models\Lead $model) {
                    return Lead::CALL_STATUS_LIST[$model->l_call_status_id] ?? '-';
                },
                'filter' => Lead::CALL_STATUS_LIST
            ],

            [
                'attribute' => 'request_ip',
                'value' => function (\common\models\Lead $model) {
                    return $model->request_ip;
                },
            ],

            [
                'attribute' => 'l_pending_delay_dt',
                'value' => function (\common\models\Lead $model) {
                    return $model->l_pending_delay_dt ? Yii::$app->formatter->asDatetime(strtotime($model->l_pending_delay_dt)) : '-';
                },
            ],


            /*[
                'header' => 'Client time2',
                'format' => 'raw',
                'value' => function (\common\models\Lead $model) {
                    return $model->getClientTime2();
                },
                'options' => [
                    'style' => 'width:110px'
                ]
            ],*/


        ];

        echo GridView::widget([

            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,

            'rowOptions' => function (Lead $model) {
                if ($model->status === Lead::STATUS_PROCESSING && Yii::$app->user->id == $model->employee_id) {
                    return [
                        'class' => 'highlighted'
                    ];
                }


            }

        ]);
        ?>

        <?php Pjax::end(); ?>

        <?/*php if($user->userProfile && $user->userProfile->up_auto_redial):?>
            <?//=$this->registerJs('startTimer(10);');?>
        <?php endif;*/ ?>
    </div>


<?php //$this->registerJs('$(".dial").knob();', \yii\web\View::POS_READY); ?>

<?php
$js = <<<JS

    /*function startTimers() {
    
        $(".timer").each(function( index ) {
            var sec = $( this ).data('sec');
            var control = $( this ).data('control');
            var format = $( this ).data('format');
            //var id = $( this ).data('id');
            //$( this ).addClass( "foo" );
            $(this).timer({format: format, seconds: sec}).timer(control);
            //console.log( index + ": " + $( this ).text() );
        });
    
        //$('.timer').timer('remove');
        //$('.timer').timer({format: '%M:%S', seconds: 0}).timer('start');
    }

    

    $('#btn-user-call-map-refresh').on('click', function () {
        // $('#modal-dialog').find('.modal-content').html('');
        $.pjax.reload({container:'#pjax-call-list'});
    });*/

    $(document).on('pjax:start', function() {
        //$("#modalUpdate .close").click();
    });

    $(document).on('pjax:end', function() {
        //startTimers();
    });
    
    /*startTimers();
        
    setTimeout(function runTimerRefresh() {
       $('#btn-user-call-map-refresh').click();
      setTimeout(runTimerRefresh, 30000);
    }, 30000);*/


JS;
$this->registerJs($js);




