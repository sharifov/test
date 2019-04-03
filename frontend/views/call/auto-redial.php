<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
//use yii\grid\GridView;
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

    function openInNewTab(url, name) {
        //var strWindowFeatures = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=no";
        var windowObjectReference = window.open(url, 'window' + name); //, strWindowFeatures);
        windowObjectReference.focus();
    }

    function autoredialInit() {
        $.pjax.reload({container:'#pjax-auto-redial', 'scrollTo': false});
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

                $.pjax.reload({container:'#pjax-auto-redial', data: 'act=find', type: 'POST', 'scrollTo': false});


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

        <div class="row">
            <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
                <h5>My Project / Phone List:</h5>

                <?php
                    if($user->userProjectParams):
                ?>

                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Nr</th>
                        <th>Project</th>
                        <th>Phone</th>
                    </tr>
                    <?php
                    $nr = 1;
                    foreach ($user->userProjectParams as $upp):?>
                        <tr>
                            <td width="100px"><?=($nr++)?></td>
                            <td><?=Html::encode($upp->uppProject->name)?></td>
                            <td><?=Html::encode($upp->upp_tw_phone_number)?></td>
                        </tr>
                    <? endforeach; ?>

                </table>
                <?php endif; ?>


            </div>
        </div>

        <div class="clearfix"></div>


        <?php Pjax::begin(['id' => 'pjax-auto-redial', 'timeout' => 5000, 'enablePushState' => false/*, 'clientOptions' => ['method' => 'GET']*/]); ?>
        <?//=date('Y-m-d H:i:s')?>

        <?php
            //\yii\helpers\VarDumper::dump(Yii::$app->request->get(), 10, true);
            //\yii\helpers\VarDumper::dump(Yii::$app->request->post(), 10, true);
        ?>

        <p>
            <?php if($user->userProfile && !$user->userProfile->up_auto_redial):?>
                <?= Html::a('<i class="fa fa-play"></i> Start Call', ['auto-redial', 'act' => 'start'], ['class' => 'btn btn-success']) ?>
            <?php else: ?>
                <?= Html::a('<i class="fa fa-stop"></i> Stop Call ('.Yii::$app->formatter->asTime(strtotime($user->userProfile->up_updated_dt)).')', ['auto-redial', 'act' => 'stop'], [
                    'class' => 'btn btn-danger',
                    /*'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],*/
                ]) ?>

            <?= Html::a('<i class="fa fa-refresh"></i> Auto Redial INIT', ['auto-redial', 'act' => 'init'], ['class' => 'btn btn-info click_after_call_update', 'id' => 'btn-auto-redial-init']) ?>

            <?php if($isActionFind && $leadModel):?>
                <div class="text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-spinner fa-spin"></i> Processing ...
                </div>
            <?php elseif($callModel):?>
                <div class="text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-spinner fa-spin"></i> Called to <?=$callModel->c_to?> ...
                </div>
            <?php else: ?>
                <div class="countdown text-center badge badge-warning" style="font-size: 35px">
                    <i class="fa fa-clock-o"></i>
                    <span id="clock"></span>
                </div>
            <?php endif; ?>

            <?//=$this->registerJs('startTimer(10);');?>

            <?php endif; ?>
        </p>



        <?php if($user->userProfile && $user->userProfile->up_auto_redial):?>
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
                                <?=$this->registerJs("openInNewTab('".\yii\helpers\Url::to(['/lead/take', 'gid' => $leadModel->gid])."', '".$leadModel->id."')");?>
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

                                        [
                                            'attribute' => 'employee_id',
                                            'format' => 'raw',
                                            'value' => function(\common\models\Lead $model) {
                                                return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                                            },
                                        ],

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


                        <div class="row">
                            <div class="col-md-12">
                                <h4>Flight Segments:</h4>
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => $dataProviderSegments,
                                    //'filterModel' => $searchModelSegments,
                                    'columns' => [
                                        //'id',
                                        /*[
                                            'attribute' => 'lead_id',
                                            'format' => 'raw',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return '<i class="fa fa-arrow-right"></i> '.Html::a('lead: '.$model->lead_id, ['leads/view', 'id' => $model->lead_id], ['target' => '_blank', 'data-pjax' => 0]);
                                            },
                                        ],*/
                                        'origin',
                                        'destination',
                                        [
                                            'attribute' => 'departure',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return '<i class="fa fa-calendar"></i> '.date("Y-m-d", strtotime($model->departure));
                                            },
                                            'format' => 'html',
                                        ],
                                        /*[
                                            'attribute' => 'flexibility',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return $model->flexibility;
                                            },
                                            'filter' => array_combine(range(0, 5), range(0, 5)),
                                        ],
                                        [
                                            'attribute' => 'flexibility_type',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return $model->flexibility_type;
                                            },
                                            'filter' => \common\models\LeadFlightSegment::FLEX_TYPE_LIST
                                        ],
                                        [
                                            'attribute' => 'created',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                                            },
                                            'format' => 'html',
                                        ],

                                        [
                                            'attribute' => 'updated',
                                            'value' => function(\common\models\LeadFlightSegment $model) {
                                                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                                            },
                                            'format' => 'html',
                                        ],*/

                                        'origin_label',
                                        'destination_label',

                                    ],
                                ]); ?>


                            </div>
                        </div>
                    <?php else: ?>
                        <?php
                            if(!Yii::$app->request->isPjax || Yii::$app->request->get('act') === 'start') {
                                $this->registerJs('startTimer(10);');
                            } else {
                                $this->registerJs('startTimer(30);');
                            }
                        ?>
                    <?php endif; ?>

                </div>
                <div class="col-md-6">
                    <?php if($callModel): ?>
                        <h1>Call</h1>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <hr>


        <div class="row">
            <div class="col-md-12">
                <h3>Last leads:</h3>
            </div>

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


            <?php if(!empty($accessLeadByFrequency) && $accessLeadByFrequency['access'] == false): ?>
                <div class="col-md-4">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> New leads will be available in <span id="left-time-countdown" data-elapsed="<?= $accessLeadByFrequency['takeDtUTC']->format('U') - time()?>" data-countdown="<?= $accessLeadByFrequency['takeDtUTC']->format('Y-m-d H:i')?>"><?=Yii::$app->formatter->asTime($accessLeadByFrequency['takeDt'])?></span>
                    </div>
                </div>
            <?php endif; ?>
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
                'attribute' => 'l_call_rating',
                'value' => function (\common\models\Lead $model) {
                    return $model->l_call_rating;
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

        ?>
        <?php
        echo \yii\grid\GridView::widget([

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

        <?php if($user->userProfile && $user->userProfile->up_auto_redial):?>
            <?//=$this->registerJs('startTimer(10);');?>
        <?php endif; ?>
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




