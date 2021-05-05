<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteBooking */

$this->title = $model->fqb_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Bookings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-booking-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fqb_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fqb_id], [
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
                'fqb_id',
                'fqb_fqf_id',
                'fqb_booking_id',
                'fqb_pnr',
                'fqb_gds',
                'fqb_gds_pcc',
                'fqb_validating_carrier',
                'fqb_created_dt:byUserDateTime',
                'fqb_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
