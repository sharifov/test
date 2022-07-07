<?php

use src\entities\cases\CasesStatus;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use src\helpers\email\MaskEmailHelper;
use src\helpers\phone\MaskPhoneHelper;

/**
 * @var $dataProvider ActiveDataProvider
 * @var $clientId int
 */
Pjax::begin(['id' => 'pjax-client-same-phone-or-email', 'timeout' => 2000, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'data' => [
    'clientId' => $clientId
]]]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],
        'id',
        'first_name',
        [
            'header' => 'Phones',
            'attribute' => 'client_phone',
            'value' => static function (\common\models\Client $model) {

                $phones = $model->clientPhones;
                $data = [];
                if ($phones) {
                    foreach ($phones as $k => $phone) {
                        $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode(MaskPhoneHelper::masking($phone->phone)) . '</code>';
                    }
                }

                $str = implode('<br>', $data);
                return $str;
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
        ],

        [
            'header' => 'Emails',
            'attribute' => 'client_email',
            'value' => static function (\common\models\Client $model) {

                $emails = $model->clientEmailsLimited;
                $data = [];
                if ($emails) {
                    foreach ($emails as $k => $email) {
                        $data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode(MaskEmailHelper::masking($email->email)) . '</code>';
                    }
                }

                $str = '';
                if ($data) {
                    $str = implode('<br>', $data);
                    if (count($emails) >= 30) {
                        $str .= '<br>' . Html::a('See all emails in new window', '/client/view?id=' .
                                $model->id, ['target' => '_blank', 'data-pjax' => 0]);
                    }
                }

                return $str;
            },
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-left'],
        ],

        [
            'header' => 'Leads',
            'value' => static function (\common\models\Client $model) {

                $leads = $model->leadsLimited;
                $data = [];
                if (!empty($leads)) {
                    foreach ($leads as $lead) {
                        $data[] = '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $lead->id, ['lead/view', 'gid' => $lead->gid], ['target' => '_blank', 'data-pjax' => 0]) . ($lead->request_ip ? ' (IP: ' . $lead->request_ip . ')' : '') . ' ' . $lead->getStatusLabel();
                    }
                }

                $str = '';
                if ($data) {
                    $str = implode('<br>', $data);
                    if (count($leads) >= 30) {
                        $str .= '<br>' . Html::a('See all leads in new window', '/client/view?id=' .
                                $model->id, ['target' => '_blank', 'data-pjax' => 0]);
                    }
                }

                return $str;
            },
            'format' => 'raw',
            //'options' => ['style' => 'width:100px']
        ],

        [
            'header' => 'Cases',
            'value' => static function (\common\models\Client $model) {
                $cases = $model->casesLimited;
                $data = [];
                if ($cases) {
                    foreach ($cases as $case) {
                        $data[] = '<i class="fa fa-link"></i> ' . Html::a('case: ' . $case->cs_id, ['cases/view', 'gid' => $case->cs_gid], ['target' => '_blank', 'data-pjax' => 0]) . ' ' . CasesStatus::getLabel($case->cs_status, '');
                    }
                }

                $str = '';
                if ($data) {
                    $str = implode('<br>', $data);
                    if (count($cases) >= 30) {
                        $str .= '<br>' . Html::a('See all cases in new window', '/client/view?id=' .
                                $model->id, ['target' => '_blank', 'data-pjax' => 0]);
                    }
                }

                return $str;
            },
            'format' => 'raw',
        ],

        [
            'header' => 'Projects',
            'value' => static function (\common\models\Client $model) {
                $data = [];
                if ($projects = $model->clientProjects) {
                    foreach ($projects as $item) {
                        $data[] = Html::tag('div', Html::encode($item->cpProject->name), ['class' => 'label label-info']) ;
                    }
                }

                return $data ? implode('<br>', $data)  : '-';
            },
            'format' => 'raw',
        ],

        [
            'attribute' => 'created',
            'value' => static function (\common\models\Client $model) {
                return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
            },
            'format' => 'html',
        ],

        ['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'client'],
    ],
]);

Pjax::end();
