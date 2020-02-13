<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\payment\UserPayment */

$this->title = 'Create User Payment';
$this->params['breadcrumbs'][] = ['label' => 'User Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
