<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BillingInfo */

$this->title = 'Update Billing Info: ' . $model->bi_id;
$this->params['breadcrumbs'][] = ['label' => 'Billing Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bi_id, 'url' => ['view', 'id' => $model->bi_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="billing-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
