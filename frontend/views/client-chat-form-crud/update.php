<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatForm\entity\ClientChatForm */

$this->title = 'Update Client Chat Form: ' . $model->ccf_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Forms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ccf_id, 'url' => ['view', 'id' => $model->ccf_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-form-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
