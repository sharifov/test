<?php

use sales\model\leadProduct\entity\LeadProduct;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LeadProduct */

$this->title = $model->lp_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-product-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'lp_lead_id' => $model->lp_lead_id, 'lp_product_id' => $model->lp_product_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'lp_lead_id' => $model->lp_lead_id, 'lp_product_id' => $model->lp_product_id], [
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
                'lead:lead',
                'lp_product_id',
                'lp_quote_id',
            ],
        ]) ?>

    </div>

</div>
