<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitorData\entity\ClientChatVisitorData */

$this->title = 'Update Client Chat Visitor Data: ' . $model->cvd_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Visitor Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cvd_id, 'url' => ['view', 'id' => $model->cvd_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-visitor-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
