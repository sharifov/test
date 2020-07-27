<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitor\entity\ClientChatVisitor */

$this->title = 'Update Client Chat Visitor: ' . $model->ccv_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccv_id, 'url' => ['view', 'id' => $model->ccv_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-visitor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
