<?php

use common\components\grid\BooleanColumn;
use common\models\Client;
use common\models\search\ClientSearch;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $clientSearchModel ClientSearch */
/* @var $clientDataProvider yii\data\ActiveDataProvider */


Pjax::begin([
    'id' => 'pjax-call-recording-disabled-client',
    'timeout' => 5000,
]);

echo GridView::widget([
    'dataProvider' => $clientDataProvider,
    'filterModel' => $clientSearchModel,
    'columns' => [
        [
            'attribute' => 'first_name',
            'value' => static function (Client $model) {
                return \common\helpers\LogHelper::replaceSource($model->first_name, 2);
            }
        ],
        [
            'attribute' => 'middle_name',
            'value' => static function (Client $model) {
                return \common\helpers\LogHelper::replaceSource($model->middle_name, 2);
            }
        ],
        [
            'attribute' => 'last_name',
            'value' => static function (Client $model) {
                return \common\helpers\LogHelper::replaceSource($model->last_name, 2);
            }
        ],
        'company_name',
        [
            'attribute' => 'cl_type_id',
            'value' => static function (Client $client) {
                return Client::TYPE_LIST[$client->cl_type_id] ?? null;
            },
            'filter' => Client::TYPE_LIST,
        ],
        ['class' => BooleanColumn::class, 'attribute' => 'is_company'],
        ['class' => BooleanColumn::class, 'attribute' => 'is_public'],
        ['class' => BooleanColumn::class, 'attribute' => 'disabled'],
        ['class' => BooleanColumn::class, 'attribute' => 'cl_excluded'],
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'contentOptions' => ['style' => 'width: 90px;'],
            'buttons' => [
                'view' => static function ($url, Client $model) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/client/view', 'id' => $model->id], ['data-pjax' => 0,]);
                }
            ],
        ],
    ],
]);

Pjax::end();
