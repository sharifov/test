<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use common\models\QuoteCommunication;
use src\widgets\UserSelect2Widget;
use frontend\models\CommunicationForm;

/* @var $this yii\web\View */
/* @var $searchModel QuoteCommunication */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Communication';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-communication-index">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= Html::tag('p', Html::a('Create Quote Communication', ['create'], ['class' => 'btn btn-success'])) ?>

    <?php Pjax::begin(); ?>

    <?php
    try {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'qc_id',
                    'options' => [
                        'width' => '100px'
                    ]
                ],
                [
                    'attribute' => 'qc_uid',
                    'options' => ['width' => '100px']
                ],
                [
                    'attribute' => 'qc_communication_type',
                    'filter' => CommunicationForm::TYPE_LIST,
                    'value' => static function (QuoteCommunication $model): string {
                        return (isset(CommunicationForm::TYPE_LIST[$model->qc_communication_type]))
                            ? CommunicationForm::TYPE_LIST[$model->qc_communication_type]
                            : 'Unknown communication type';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qc_communication_id',
                    'value' => static function (QuoteCommunication $model): string {
                        switch ($model->qc_communication_type) {
                            case CommunicationForm::TYPE_EMAIL:
                                return Html::a('<i class="fa fa-link"></i> ' . $model->qc_communication_id, ['/email/view', 'id' => $model->qc_communication_id], ['target' => '_blank', 'data-pjax' => 0]);
                            case CommunicationForm::TYPE_SMS:
                                return Html::a('<i class="fa fa-link"></i> ' . $model->qc_communication_id, ['/sms/view', 'id' => $model->qc_communication_id], ['target' => '_blank', 'data-pjax' => 0]);
                            case CommunicationForm::TYPE_CHAT:
                                return Html::a('<i class="fa fa-link"></i> ' . $model->qc_communication_id, ['/client-chat-crud/view', 'id' => $model->qc_communication_id], ['target' => '_blank', 'data-pjax' => 0]);
                            default:
                                return 'Unknown communication type';
                        }
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'qc_quote_id',
                    'value' => function (QuoteCommunication $model) {
                        return Html::a('<i class="fa fa-link"></i> ' . $model->qc_quote_id, ['/quotes/view', 'id' => $model->qc_quote_id], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'qc_created_dt',
                    'format' => 'byUserDateTime',
                    'options' => [
                        'width' => '200px'
                    ],
                ],
                [
                    'attribute' => 'qc_created_by',
                    'filter' => UserSelect2Widget::widget([
                        'model' => $searchModel,
                        'attribute' => 'qc_created_by'
                    ]),
                    'format' => 'username',
                    'options' => [
                        'width' => '200px'
                    ],
                ],
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => static function ($action, QuoteCommunication $model, $key, $index, $column): string {
                        return Url::toRoute([$action, 'qc_id' => $model->qc_id]);
                    }
                ],
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', Html::encode($e->getMessage()));
    }
    ?>

    <?php Pjax::end(); ?>

</div>
