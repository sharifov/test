<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatStatusLog\entity\ClientChatStatusLog */

$this->title = 'Update Client Chat Status Log: ' . $model->csl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->csl_id, 'url' => ['view', 'id' => $model->csl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
