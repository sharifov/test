<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate */

$this->title = 'Update Client Chat Channel Translate: ' . $model->ct_channel_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channel Translates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ct_channel_id, 'url' => ['view', 'ct_channel_id' => $model->ct_channel_id, 'ct_language_id' => $model->ct_language_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-channel-translate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
