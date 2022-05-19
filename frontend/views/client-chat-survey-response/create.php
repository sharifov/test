<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\ClientChatSurveyResponse */

$this->title = 'Create Client Chat Survey Response';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Survey', 'url' => ['client-chat-survey/view', 'ccs_id' => $model->ccsr_client_chat_survey_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-survey-response-create">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
