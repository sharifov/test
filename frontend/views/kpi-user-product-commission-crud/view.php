<?php

use sales\helpers\DateHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission */

$this->title = $model->upcProductType->pt_name . ', User: ' . $model->upcUser->username . ', Year: ' . $model->upc_year . ', Month: ' . DateHelper::getMonthName($model->upc_month);
$this->params['breadcrumbs'][] = ['label' => 'Kpi User Product Commissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="kpi-user-product-commission-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'upc_product_type_id' => $model->upc_product_type_id, 'upc_user_id' => $model->upc_user_id, 'upc_year' => $model->upc_year, 'upc_month' => $model->upc_month], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'upc_product_type_id' => $model->upc_product_type_id, 'upc_user_id' => $model->upc_user_id, 'upc_year' => $model->upc_year, 'upc_month' => $model->upc_month], [
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
            'upc_product_type_id:productType',
            'upc_user_id:userName',
            'upc_year',
            'upc_month:MonthNameByMonthNumber',
            'upc_performance',
            'upc_commission_percent:percentInteger',
            'upc_created_user_id:UserName',
            'upc_updated_user_id:UserName',
            'upc_created_dt:byUserDateTime',
            'upc_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
