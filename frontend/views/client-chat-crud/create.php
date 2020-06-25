<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\ClientChat */

$this->title = 'Create Client Chat';
$this->params['breadcrumbs'][] = ['label' => 'Client Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
