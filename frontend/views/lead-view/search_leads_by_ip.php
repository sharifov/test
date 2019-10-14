<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use common\models\Lead;
use yii\widgets\Pjax;

/** @var ActiveDataProvider $dataProvider */

$ipData = @json_decode($lead->request_ip_detail, true);

if ($ipData) {

    $str = '<pre>';
    $str .= '<table class="table table-bordered">';
    $content = '';
    foreach ($ipData as $key => $val) {
        if (is_array($val)) {
            continue;
        }
        $content .= '<tr><th>' . $key . '</th><td>' . $val . '</td></tr>';
    }
    if ($content) {
        echo $str . $content . '</table></pre>';
    }

}

Pjax::begin(['id' => 'pjax-leads-ip-info', 'timeout' => 2000, 'enablePushState' => false]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => false,
    'columns' => [
        'id',
        [
            'header' => 'IP',
            'attribute' => 'request_ip',
            'value' => static function (Lead $lead) {
                return '' . Html::a($lead->request_ip, ['leads/index', 'LeadSearch[request_ip]' => $lead->request_ip], ['data-pjax' => 0, 'target' => '_blank']) . '';
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
        ],
        [
            'attribute' => 'status',
            'value' => function (Lead $model) {
                return $model->getStatusName(true);
            },
            'format' => 'raw',
            'options' => [
                'style' => 'width:100px'
            ],
            'contentOptions' => [
                'class' => 'text-center'
            ]
        ],
        [
            'attribute' => 'uid',
            'options' => ['style' => 'width:100px'],
            'contentOptions' => ['class' => 'text-center'],
        ],
        ['attribute' => 'client_id',
            'options' => ['style' => 'width:80px'],
            'contentOptions' => ['class' => 'text-center'],
        ],
        [
            'header' => 'Client name',
            'format' => 'raw',
            'value' => static function (Lead $lead) {
                $clientName = '-';
                if ($lead->client) {
                    $clientName = $lead->client->first_name . ' ' . $lead->client->last_name;
                    if ($clientName === 'Client Name') {
                        $clientName = '- - - ';
                    } else {
                        $clientName = '<i class="fa fa-user"></i> ' . Html::encode($clientName);
                    }
                }
                return $clientName;
            },
            'options' => ['style' => 'width:160px'],
        ],
        [
            'header' => 'Phones',
            'attribute' => 'client_phone',
            'value' => static function (Lead $lead) {
                $phones = $lead->client->clientPhones;
                $data = [];
                if ($phones) {
                    foreach ($phones as $k => $phone) {
                        $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code>';
                    }
                }

                $str = implode('<br>', $data);
                return '' . $str . '';
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
        ],
        [
            'header' => 'Emails',
            'attribute' => 'client_email',
            'value' => static function (Lead $lead) {
                $emails = $lead->client->clientEmails;
                $data = [];
                if ($emails) {
                    foreach ($emails as $k => $email) {
                        $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode($email->email) . '</code>';
                    }
                }
                $str = implode('<br>', $data);
                return '' . $str . '';
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
        ],
        [
            'attribute' => 'trip_type',
            'value' => static function (Lead $lead) {
                return $lead->getFlightTypeName();
            },
//            'filter' => Lead::getFlightTypeList()
        ],
        [
            'attribute' => 'cabin',
            'value' => static function (Lead $lead) {
                return $lead->getCabinClassName();
            },
//            'filter' => Lead::getCabinList()
        ],
        [
            'header' => 'Segments',
            'value' => static function (Lead $lead) {

                $segments = $lead->leadFlightSegments;
                $segmentData = [];
                if ($segments) {
                    foreach ($segments as $sk => $segment) {
                        $segmentData[] = ($sk + 1) . '. <code>' . Html::a($segment->origin . '->' . $segment->destination, ['lead-flight-segment/view', 'id' => $segment->id], ['target' => '_blank', 'data-pjax' => 0]) . '</code>';
                    }
                }
                $segmentStr = implode('<br>', $segmentData);
                return '' . $segmentStr . '';
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'options' => ['style' => 'width:140px'],
        ],
        [
            'attribute' => 'created',
            'value' => static function (Lead $lead) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($lead->created));
            },
            'format' => 'html',
        ],
        [
            'attribute' => 'l_last_action_dt',
            'value' => static function (Lead $lead) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($lead->l_last_action_dt));
            },
            'format' => 'html',
        ],
    ],
]);

Pjax::end();
