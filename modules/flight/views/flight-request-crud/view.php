<?php

use modules\flight\models\FlightRequest;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequest */

$this->title = $model->fr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month], [
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
                'fr_id',
                'fr_hash',
                'fr_type_id',
                'fr_created_api_user_id',
                'fr_status_id',
                'fr_job_id',
                'fr_created_dt',
                'fr_updated_dt',
                'fr_year',
                'fr_month',
                [
                    'attribute' => 'fr_data_json',
                    'value' => static function (FlightRequest $model) {
                        if ($model->fr_data_json) {
                            return VarDumper::dumpAsString($model->fr_data_json, 20, true);
                        }
                        return Yii::$app->formatter->nullDisplay;
                    },
                    'format' => 'raw'
                ],
            ],
        ]) ?>

    </div>

</div>
