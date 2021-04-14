<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteTicket */

$this->title = $model->fqt_pax_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fqt_pax_id' => $model->fqt_pax_id, 'fqt_fqb_id' => $model->fqt_fqb_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fqt_pax_id' => $model->fqt_pax_id, 'fqt_fqb_id' => $model->fqt_fqb_id], [
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
                'fqt_pax_id',
                'fqt_fqb_id',
                'fqt_ticket_number',
                'fqt_created_dt:byUserDateTime',
                'fqt_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
