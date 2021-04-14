<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBookingAirline */

$this->title = $model->fqba_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Booking Airlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-booking-airline-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fqba_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fqba_id], [
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
                'fqba_id',
                'fqba_fqb_id',
                'fqba_record_locator',
                'fqba_airline_code',
                'fqba_created_dt:byUserDateTime',
                'fqba_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
