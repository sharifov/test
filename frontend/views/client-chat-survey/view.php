<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use common\models\ClientChatSurvey;
use common\models\ClientChatSurveyResponse;
use common\models\search\ClientChatSurveyResponseSearch;
use yii\data\ActiveDataProvider;

/**
 * @var $this yii\web\View
 * @var $model \common\models\ClientChatSurvey
 * @var $clientChatSurveyResponseSearchModel ClientChatSurveyResponseSearch
 * @var $clientChatSurveyResponseDataProvider ActiveDataProvider
 **/

$this->title = $model->ccs_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Survey', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-survey-view">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <p>
        <?= Html::a('Update', ['update', 'ccs_id' => $model->ccs_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ccs_id' => $model->ccs_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    try {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ccs_id',
                'ccs_uid',
                'ccs_chat_id',
                [
                    'attribute' => 'ccs_type',
                    'value' => function (ClientChatSurvey $model) {
                        return ClientChatSurvey::typeName($model->ccs_type);
                    }
                ],
                'ccs_template',
                [
                    'attribute' => 'ccs_trigger_source',
                    'value' => function (ClientChatSurvey $model) {
                        return ClientChatSurvey::triggerSourceName($model->ccs_trigger_source);
                    }
                ],
                'ccs_requested_by:username',
                'ccs_requested_for:username',
                [
                    'attribute' => 'ccs_status',
                    'value' => function (ClientChatSurvey $model) {
                        return ClientChatSurvey::statusName($model->ccs_status);
                    }
                ],
                'ccs_created_dt:byUserDateTime'
            ]
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', $e->getMessage());
    }
    ?>

    <?= Html::tag('h2', 'Responses'); ?>

    <?= Html::tag('p', Html::a('Create Client Chat Survey Response', ['client-chat-survey-response/create', 'ccs_id' => $model->ccs_id], ['class' => 'btn btn-success'])) ?>

    <?php
    try {
        echo GridView::widget([
            'dataProvider' => $clientChatSurveyResponseDataProvider,
            'filterModel' => $clientChatSurveyResponseSearchModel,
            'columns' => [
                'ccsr_id',
                'ccsr_question',
                'ccsr_response',
                [
                    'class' => ActionColumn::class,
                    'urlCreator' => static function ($action, ClientChatSurveyResponse $model, $key, $index, $column): string {
                        return Url::toRoute(["client-chat-survey-response/{$action}", 'ccsr_id' => $model->ccsr_id]);
                    }
                ],
            ],
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', $e->getMessage());
    }
    ?>

</div>
