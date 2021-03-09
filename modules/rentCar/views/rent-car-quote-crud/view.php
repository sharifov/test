<?php

use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCarQuote\RentCarQuote */

$this->title = $model->rcq_id;
$this->params['breadcrumbs'][] = ['label' => 'Rent Car Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rent-car-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->rcq_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->rcq_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'rcq_id',
                    'rcq_booking_id',
                    'rcq_rent_car_id',
                    'rcq_product_quote_id',
                    'rcq_hash_key',
                    'rcq_request_hash_key',
                    'rcq_offer_token',
                    //'rcq_json_response',
                    'rcq_model_name',
                    'rcq_category',
                    'rcq_image_url:url',
                    'rcq_vendor_name',
                    'rcq_vendor_logo_url:url',
                    'rcq_transmission',
                    'rcq_seats',
                    'rcq_doors',
                    //'rcq_options',
                    'rcq_days',
                    'rcq_price_per_day',
                    'rcq_currency',
                    //'rcq_advantages',
                    'rcq_pick_up_location',
                    'rcq_drop_of_location',
                    'rcq_created_dt',
                    'rcq_updated_dt',
                    'rcq_created_user_id',
                    'rcq_updated_user_id',
                ],
            ]) ?>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'rcq_options',
                        'value' => function (RentCarQuote $rentCarQuote) {
                            if (!$rentCarQuote->rcq_options) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return VarDumper::dumpAsString($rentCarQuote->rcq_options, 20, true);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'rcq_booking_json',
                        'value' => function (RentCarQuote $rentCarQuote) {
                            if (!$rentCarQuote->rcq_booking_json) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return VarDumper::dumpAsString($rentCarQuote->rcq_booking_json, 20, true);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'rcq_json_response',
                        'value' => function (RentCarQuote $rentCarQuote) {
                            if (!$rentCarQuote->rcq_json_response) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return VarDumper::dumpAsString($rentCarQuote->rcq_json_response, 20, true);
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'rcq_contract_request_json',
                        'value' => function (RentCarQuote $rentCarQuote) {
                            if (!$rentCarQuote->rcq_contract_request_json) {
                                return Yii::$app->formatter->nullDisplay;
                            }
                            return VarDumper::dumpAsString($rentCarQuote->rcq_contract_request_json, 20, true);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
