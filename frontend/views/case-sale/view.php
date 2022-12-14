<?php

use frontend\helpers\JsonHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CaseSale */

$this->title = $model->css_cs_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Sales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="case-sale-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-4">
            <p>
                <?= Html::a('Update', ['update', 'css_cs_id' => $model->css_cs_id, 'css_sale_id' => $model->css_sale_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'css_cs_id' => $model->css_cs_id, 'css_sale_id' => $model->css_sale_id], [
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
                    'css_cs_id',
                    'css_sale_id',
                    'css_sale_book_id',
                    'css_sale_pnr',
                    'css_sale_pax',
                    'css_sale_created_dt',
                    'css_created_user_id',
                    'css_updated_user_id',
                    'css_created_dt',
                    'css_updated_dt',
                    'css_in_departure_airport',
                    'css_in_arrival_airport',
                    'css_in_date',
                    'css_out_departure_airport',
                    'css_out_arrival_airport',
                    'css_out_date',
                ],
            ]) ?>
        </div>
        <div class="col-md-8">
            <h6>SaleData:</h6>
            <pre>
                <?php
                    VarDumper::dump(JsonHelper::decode($model->css_sale_data), 20, true);
                ?>
            </pre>
        </div>
    </div>
</div>
