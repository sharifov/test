<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUnread\entity\ClientChatUnread */

$this->title = 'Create Client Chat Unread';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Unreads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-unread-create">

    <h1><?= Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>

</div>
