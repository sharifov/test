<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuotePaxPrice */

$this->title = $model->qpp_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Pax Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-pax-price-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->qpp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->qpp_id], [
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
            'qpp_id',
            'qpp_flight_quote_id',
            'qpp_flight_pax_code_id',
            'qpp_fare',
            'qpp_tax',
            'qpp_system_mark_up',
            'qpp_agent_mark_up',
            'qpp_origin_fare',
            'qpp_origin_currency',
            'qpp_origin_tax',
            'qpp_client_currency',
            'qpp_client_fare',
            'qpp_client_tax',
            'qpp_created_dt',
            'qpp_updated_dt',
        ],
    ]) ?>

</div>
