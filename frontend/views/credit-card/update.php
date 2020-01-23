<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CreditCard */

$this->title = 'Update Credit Card: ' . $model->cc_id;
$this->params['breadcrumbs'][] = ['label' => 'Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cc_id, 'url' => ['view', 'id' => $model->cc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="credit-card-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
