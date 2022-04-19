<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ClientChatSurveyResponse;

/**
 * @var $this yii\web\View
 * @var $model ClientChatSurveyResponse
 **/

$this->title = "Response #{$model->ccsr_id}";
$this->params['breadcrumbs'][] = ['label' => "Client Chat Survey #{$model->ccsr_client_chat_survey_id}", 'url' => ['client-chat-survey/view', 'ccs_id' => $model->ccsr_client_chat_survey_id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-survey-view">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <p>
        <?= Html::a('Update', ['update', 'ccsr_id' => $model->ccsr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ccsr_id' => $model->ccsr_id], [
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
                'ccsr_id',
                'ccsr_created_dt:datetime',
                [
                    'attribute' => 'ccsr_client_chat_survey_id',
                    'value' => function (ClientChatSurveyResponse $model) {
                        return Html::a('<i class="fa fa-link"></i> ' . $model->clientChatSurvey->ccs_id, ['client-chat-survey/view', 'ccs_id' => $model->ccsr_client_chat_survey_id]);
                    },
                    'format' => 'raw'
                ],
                'ccsr_question',
                'ccsr_response'
            ]
        ]);
    } catch (\Exception $e) {
        echo Html::tag('pre', $e->getMessage());
    }
    ?>
</div>
