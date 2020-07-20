<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatNote\entity\ClientChatNote */

$this->title = 'Update Client Chat Note: ' . $model->ccn_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccn_id, 'url' => ['view', 'id' => $model->ccn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
