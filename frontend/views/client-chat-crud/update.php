<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\ClientChat */

$this->title = 'Update Client Chat: ' . $model->cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cch_id, 'url' => ['view', 'id' => $model->cch_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
