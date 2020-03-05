<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTips\OrderTips */

$this->title = 'Create Order Tips';
$this->params['breadcrumbs'][] = ['label' => 'Order Tips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-tips-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
