<?php

use modules\flight\models\FlightRequest;
use modules\flight\models\FlightRequestLog;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\components\grid\DateTimeColumn;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequest */
/* @var $searchModel modules\flight\models\search\FlightRequestLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Request ID: ' .  $model->fr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <p>
                <?= Html::a('<i class="fa fa-pencil"></i> Update', ['update', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month], [
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
                    'fr_booking_id',
                    'fr_hash',
                    [
                        'attribute' => 'fr_type_id',
                        'value' => static function (FlightRequest $model) {
                            return $model->getTypeName();
                        }
                    ],
                    [
                        'attribute' => 'fr_created_api_user_id',
                        'class' => common\components\grid\ApiUserColumn::class,
                    ],
                    [
                        'attribute' => 'fr_status_id',
                        'value' => static function (FlightRequest $model) {
                            return $model->getStatusName();
                        }
                    ],
                    'fr_project_id:projectName',
                    'fr_job_id',
                    'fr_created_dt',
                    'fr_updated_dt',
                    'fr_year',
                    'fr_month',
                ],
            ]) ?>
        </div>
    </div>

    <h5>Flight Request Logs:</h5>
    <div class="row">
        <div class="col-md-12">
            <?php Pjax::begin(['id' => 'pjax-flight-request']); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'flr_id',
                    [
                        'attribute' => 'flr_status_id_old',
                        'value' => static function (FlightRequestLog $searchModel) {
                            return $searchModel->getOldStatusName() ?? Yii::$app->formatter->nullDisplay;
                        },
                        'filter' => FlightRequest::STATUS_LIST,
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'flr_status_id_new',
                        'value' => static function (FlightRequestLog $searchModel) {
                            return $searchModel->getNewStatusName() ?? Yii::$app->formatter->nullDisplay;
                        },
                        'filter' => FlightRequest::STATUS_LIST,
                        'format' => 'raw',
                    ],
                    'flr_description',
                    ['class' => DateTimeColumn::class, 'attribute' => 'flr_created_dt'],
                    ['class' => DateTimeColumn::class, 'attribute' => 'flr_updated_dt'],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
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
