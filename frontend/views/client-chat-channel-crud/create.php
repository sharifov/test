<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatChannel\entity\ClientChatChannel */

$this->title = 'Create Client Chat Channel';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
