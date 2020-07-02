<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatData\entity\ClientChatData */

$this->title = 'Create Client Chat Data';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
