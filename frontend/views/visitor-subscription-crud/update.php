<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\visitorSubscription\entity\VisitorSubscription */

$this->title = 'Update Visitor Subscription: ' . $model->vs_id;
$this->params['breadcrumbs'][] = ['label' => 'Visitor Subscriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->vs_id, 'url' => ['view', 'id' => $model->vs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="visitor-subscription-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
