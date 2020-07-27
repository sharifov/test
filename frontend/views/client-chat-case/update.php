<?php

use sales\model\clientChatCase\entity\ClientChatCase;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ClientChatCase */

$this->title = 'Update Client Chat Case';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-case-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
