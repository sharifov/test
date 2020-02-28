<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiProductCommission\KpiProductCommission */

$this->title = 'Update Kpi Product Commission: ' . $model->pc_product_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Kpi Product Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->pc_product_type_id, 'url' => ['view', 'pc_product_type_id' => $model->pc_product_type_id, 'pc_performance' => $model->pc_performance, 'pc_commission_percent' => $model->pc_commission_percent]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="kpi-product-commission-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
