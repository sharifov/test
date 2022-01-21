<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\emailReviewQueue\entity\EmailReviewQueue */

$this->title = 'Create Email Review Queue';
$this->params['breadcrumbs'][] = ['label' => 'Email Review Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-review-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
