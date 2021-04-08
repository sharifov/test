<?php

use modules\flight\models\FlightQuoteFlight;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var modules\flight\models\FlightQuoteFlight $model */

$this->title = $model->fqf_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Flights', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="flight-quote-flight-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fqf_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fqf_id], [
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
                'fqf_id',
                'fqf_fq_id',
                'fqf_record_locator',
                'fqf_gds',
                'fqf_gds_pcc',
                'fqf_type_id',
                'fqf_cabin_class',
                'fqf_trip_type_id',
                'fqf_main_airline',
                'fqf_fare_type_id',
                'fqf_status_id',
                'fqf_booking_id',
                'fqf_pnr',
                'fqf_validating_carrier',
                [
                    'attribute' => 'fqf_original_data_json',
                    'value' => static function (FlightQuoteFlight $model) {
                        if ($model->fqf_original_data_json) {
                            return VarDumper::dumpAsString($model->fqf_original_data_json, 20, true);
                        }
                        return Yii::$app->formatter->nullDisplay;
                    },
                    'format' => 'raw'
                ],
                'fqf_created_dt:byUserDateTime',
                'fqf_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
