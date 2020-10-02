<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason */

$this->title = 'Update Client Chat Status Log Reason: ' . $model->cslr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Log Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cslr_id, 'url' => ['view', 'id' => $model->cslr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-status-log-reason-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
