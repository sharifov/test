<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserChannel\entity\ClientChatUserChannel */

$this->title = 'Create Client Chat User Channel';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat User Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-user-channel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
