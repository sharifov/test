<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SaleCreditCard */

$this->title = 'Create Sale Credit Card';
$this->params['breadcrumbs'][] = ['label' => 'Sale Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-credit-card-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
