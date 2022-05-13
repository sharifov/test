<?php

use yii\helpers\Html;
use common\models\ClientChatSurveyResponse;

/* @var $this yii\web\View */
/* @var $model ClientChatSurveyResponse */

$this->title = 'Client Chat Survey Response: ' . $model->ccsr_id;
$this->params['breadcrumbs'][] = ['label' => "Client Chat Survey #{$model->ccsr_client_chat_survey_id}", 'url' => ['client-chat-survey/view', 'ccs_id' => $model->ccsr_client_chat_survey_id]];
$this->params['breadcrumbs'][] = ['label' => "Response: #{$model->ccsr_id}", 'url' => ['view', 'ccsr_id' => $model->ccsr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-survey-response-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
