<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUnread\entity\ClientChatUnread */

$this->title = 'Update Client Chat Unread: ' . $model->ccu_cc_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Unreads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccu_cc_id, 'url' => ['view', 'id' => $model->ccu_cc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-unread-update">

    <h1><?= Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
