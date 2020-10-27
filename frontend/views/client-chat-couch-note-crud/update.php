<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\ClientChatCouchNote\entity\ClientChatCouchNote */

$this->title = 'Update Client Chat Couch Note: ' . $model->cccn_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Couch Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cccn_id, 'url' => ['view', 'id' => $model->cccn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-couch-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
