<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatHold\entity\ClientChatHold */

$this->title = 'Update Client Chat Hold: ' . $model->cchd_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Holds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cchd_id, 'url' => ['view', 'id' => $model->cchd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-hold-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
