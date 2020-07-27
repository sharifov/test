<?php

use sales\model\clientChatCase\entity\ClientChatCase;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ClientChatCase */

$this->title = 'Client Chat Case';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-case-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
