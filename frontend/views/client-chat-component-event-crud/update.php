<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent */

$this->title = 'Update Client Chat Component Event: ' . $model->ccce_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Component Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccce_id, 'url' => ['view', 'id' => $model->ccce_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-component-event-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
