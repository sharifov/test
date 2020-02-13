<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\payment\UserPayment */

$this->title = 'Update User Payment: ' . $model->upt_id;
$this->params['breadcrumbs'][] = ['label' => 'User Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->upt_id, 'url' => ['view', 'id' => $model->upt_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-payment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
