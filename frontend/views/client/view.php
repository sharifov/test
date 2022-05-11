<?php

use common\models\Client;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $leadsDataProvider yii\data\ActiveDataProvider */
/* @var $casesDataProvider yii\data\ActiveDataProvider */

$this->title = 'Client: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="row">
    <div class="col-md-5">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uuid',
            [
                'attribute' => 'first_name',
                'value' => static function (Client $client) {
                    return \src\model\client\helpers\ClientFormatter::formatName($client);
                },
                'format' => 'raw',
            ],
            'middle_name',
            'last_name',
            'company_name',
            'description',
            'is_company:boolean',
            'is_public:boolean',
            'project:projectName',
            [
                'attribute' => 'cl_type_create',
                'value' => static function (Client $model) {
                    return Client::TYPE_CREATE_LIST[$model->cl_type_create] ?? null;
                },
            ],
            'disabled:boolean',
            'rating',
            [
                'attribute' => 'parent_id',
                'value' => function (\common\models\Client $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if ($model->parent_id && $parent = Client::findOne(['id' => $model->parent_id])) {
                        return $parent->id . '  <i class="fa fa-user"></i> ' . $parent->getNameByType();
                    }
                    return $out;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cl_type_id',
                'value' => function (\common\models\Client $model) {
                    return $model::TYPE_LIST[$model->cl_type_id];
                },
                'format' => 'raw',
            ],
            'cl_ca_id',
            'cl_excluded:boolean',
            'cl_ppn',
            'cl_ip',
            'cl_locale',
            'cl_marketing_country',
            'cl_call_recording_disabled:booleanByLabel',
        ],
    ]) ?>
    </div>
        <div class="col-md-7">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Phones',
                        'value' => function (\common\models\Client $model) {

                            $phones = $model->clientPhones;
                            $data = [];
                            if ($phones) {
                                foreach ($phones as $k => $phone) {
                                    $data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code>'; //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                }
                            }

                            $str = implode('<br>', $data);
                            return '' . $str . '';
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                    ],

                    [
                        'label' => 'Emails',
                        'value' => function (\common\models\Client $model) {

                            $emails = $model->clientEmails;
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

                    //'created',
                    //'updated',

                    [
                        'attribute' => 'created',
                        'value' => function (\common\models\Client $model) {
                            return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'updated',
                        'value' => function (\common\models\Client $model) {
                            return $model->updated ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->updated)) : null;
                        },
                        'format' => 'html',
                    ],
                ],
            ]) ?>

            <div class="col-md-6">
            <h5>Leads:</h5>
            <?php
            Pjax::begin(['id' => 'pjax-client-leads', 'timeout' => 2000, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'data' => [
                'clientId' => $model->id
            ]]]);

            echo GridView::widget([
                'dataProvider' => $leadsDataProvider,
                'showHeader'=> false,
                'columns' => [
                    [
                    'value' => static function (array $model) {
                        return '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $model['id'], ['lead/view', 'gid' => $model['gid']], ['target' => '_blank', 'data-pjax' => 0]) . ($model['request_ip'] ? ' (IP: ' . $model['request_ip'] . ')' : '');
                    },
                    'format' => 'html'
                    ]
                ]
            ]);
            Pjax::end();
            ?>
            </div>
            <div class="col-md-6">
                <h5>Cases:</h5>
                <?php
                Pjax::begin(['id' => 'pjax-client-cases', 'timeout' => 2000, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'data' => [
                    'clientId' => $model->id
                ]]]);

                echo GridView::widget([
                    'dataProvider' => $casesDataProvider,
                    'showHeader'=> false,
                    'columns' => [
                        [
                            'value' => static function (array $model) {
                                return '<i class="fa fa-link"></i> ' . Html::a('case: ' . $model['cs_id'], ['cases/view', 'gid' => $model['cs_gid'] ], ['target' => '_blank', 'data-pjax' => 0]);
                            },
                            'format' => 'html'
                        ]
                    ]
                ]);
                Pjax::end();
                ?>
            </div>
        </div>
    </div>

</div>
