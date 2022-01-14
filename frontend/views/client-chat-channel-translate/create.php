<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate */

$this->title = 'Create Client Chat Channel Translate';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channel Translates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-translate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
