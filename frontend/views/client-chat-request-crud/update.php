<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatRequest\entity\ClientChatRequest */

$this->title = 'Update Client Chat Request: ' . $model->ccr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccr_id, 'url' => ['view', 'id' => $model->ccr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
