<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\user\entity\paymentCategory\UserPaymentCategory */

$this->title = 'Create User Payment Category';
$this->params['breadcrumbs'][] = ['label' => 'User Payment Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
