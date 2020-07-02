<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatStatusLog\entity\ClientChatStatusLog */

$this->title = 'Create Client Chat Status Log';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-status-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
