<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\data\ActiveDataProvider;
use common\components\grid\DateTimeColumn;
use common\models\ClientChatSurvey;
use common\models\search\ClientChatSurveySearch;

/* @var $this yii\web\View */
/* @var $searchModel ClientChatSurveySearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Client Chat Survey';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-survey-index">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= Html::tag('p', Html::a('Create Client Chat Survey', ['create'], ['class' => 'btn btn-success'])) ?>

    <?php Pjax::begin(); ?>

    <?php
    try {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'ccs_id',
                'ccs_uid',
                'ccs_client_chat_id',
                [
                    'attribute' => 'ccs_type',
                    'value' => function (ClientChatSurvey $model) {
                        return ClientChatSurvey::typeName($model->ccs_type);
                    },
                    'filter' => ClientChatSurvey::TYPE_LIST
                ],
                'ccs_template',
                [
                    'attribute' => 'ccs_trigger_source',
                    'value' => function (ClientChatSurvey $model) {
                        return ClientChatSurvey::triggerSourceName($model->ccs_trigger_source);
                    },
                    'filter' => ClientChatSurvey::TRIGGER_SOURCE_LIST
                ],
                [
                    'attribute' => 'ccs_requested_by',
                    'value' => function (ClientChatSurvey $model) {
                        return (is_null($model->requestedBy))
                            ? 'BOT'
                            : Html::a('<i class="fa fa-link"></i> ' . $model->requestedBy->username, ['/user/info', 'id' => $model->ccs_requested_by], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'ccs_requested_for',
                    'value' => function (ClientChatSurvey $model) {
                        return Html::a('<i class="fa fa-link"></i> ' . $model->requestedFor->username, ['/user/info', 'id' => $model->ccs_requested_for], ['target' => '_blank', 'data-pjax' => 0]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'ccs_status',
                    'value' => function (ClientChatSurvey $model) {
                        return ClientChatSurvey::statusName($model->ccs_status);
                    },
                    'filter' => ClientChatSurvey::STATUS_LIST
                ],
                [
                    'class' => DateTimeColumn::class,
                    'attribute' => 'ccs_created_dt',
                    'options' => [
                        'width' => '200px'
                    ],
                ],
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => static function ($action, ClientChatSurvey $model, $key, $index, $column): string {
                        return Url::toRoute([$action, 'ccs_id' => $model->ccs_id]);
                    }
                ],
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', $e->getMessage());
    }
    ?>

    <?php Pjax::end(); ?>

</div>
