<?php

use sales\model\clientChatLead\entity\ClientChatLead;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ClientChatLead */

$this->title = 'Update Client Chat Lead';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="client-chat-lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
