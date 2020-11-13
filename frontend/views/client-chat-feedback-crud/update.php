<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatFeedback\entity\ClientChatFeedback */

$this->title = 'Update Client Chat Feedback: ' . $model->ccf_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Feedback', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccf_id, 'url' => ['view', 'id' => $model->ccf_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
