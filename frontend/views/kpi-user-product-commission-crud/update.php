<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission */

$this->title = 'Update Kpi User Product Commission: ' . $model->upc_product_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Kpi User Product Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->upc_product_type_id, 'url' => ['view', 'upc_product_type_id' => $model->upc_product_type_id, 'upc_user_id' => $model->upc_user_id, 'upc_year' => $model->upc_year, 'upc_month' => $model->upc_month]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="kpi-user-product-commission-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
