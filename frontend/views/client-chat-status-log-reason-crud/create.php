<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason */

$this->title = 'Create Client Chat Status Log Reason';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Log Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-status-log-reason-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
