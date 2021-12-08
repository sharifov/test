<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\emailReviewQueue\entity\EmailReviewQueue */

$this->title = 'Update Email Review Queue: ' . $model->erq_id;
$this->params['breadcrumbs'][] = ['label' => 'Email Review Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->erq_id, 'url' => ['view', 'id' => $model->erq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="email-review-queue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
