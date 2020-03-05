<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiProductCommission\KpiProductCommission */

$this->title = 'Kpi Product Commission - ' . $model->pcProductType->pt_name;
$this->params['breadcrumbs'][] = ['label' => 'Kpi Product Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="kpi-product-commission-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'pc_product_type_id' => $model->pc_product_type_id, 'pc_performance' => $model->pc_performance, 'pc_commission_percent' => $model->pc_commission_percent], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'pc_product_type_id' => $model->pc_product_type_id, 'pc_performance' => $model->pc_performance, 'pc_commission_percent' => $model->pc_commission_percent], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'pc_product_type_id:productType',
            'pc_performance:percentInteger',
            'pc_commission_percent:percentInteger',
            'pc_created_user_id:UserName',
            'pc_updated_user_id:UserName',
            'pc_created_dt:byUserDateTime',
            'pc_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
