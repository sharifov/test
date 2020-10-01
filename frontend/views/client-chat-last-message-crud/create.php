<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatLastMessage\entity\ClientChatLastMessage */

$this->title = 'Create Client Chat Last Message';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Last Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-last-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
