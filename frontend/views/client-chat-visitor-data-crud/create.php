<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitorData\entity\ClientChatVisitorData */

$this->title = 'Create Client Chat Visitor Data';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Visitor Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-visitor-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
