<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatData\entity\ClientChatData */

$this->title = 'Update Client Chat Data: ' . $model->ccd_cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccd_cch_id, 'url' => ['view', 'id' => $model->ccd_cch_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
