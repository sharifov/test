<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatDataRequest\entity\ClientChatDataRequest */

$this->title = 'Create Client Chat Data Request';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Data Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-data-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
