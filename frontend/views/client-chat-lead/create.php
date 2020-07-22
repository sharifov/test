<?php

use sales\model\clientChatLead\entity\ClientChatLead;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ClientChatLead */

$this->title = 'Client Chat Lead';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-lead-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
