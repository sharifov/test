<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogQueue\CallLogQueue */

$this->title = 'Create Call Log Queue';
$this->params['breadcrumbs'][] = ['label' => 'Call Log Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-log-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
