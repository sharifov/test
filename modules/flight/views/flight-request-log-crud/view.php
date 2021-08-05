<?php

use modules\flight\models\FlightRequest;
use modules\flight\models\FlightRequestLog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequestLog */

$this->title = $model->flr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Request Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-request-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->flr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->flr_id], [
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
            'flr_id',
            'flr_fr_id',
            [
                'attribute' => 'flr_status_id_old',
                'value' => static function (FlightRequestLog $model) {
                    return $model->getOldStatusName() ?? Yii::$app->formatter->nullDisplay;
                },
                'filter' => FlightRequest::STATUS_LIST
            ],
            [
                'attribute' => 'flr_status_id_new',
                'value' => static function (FlightRequestLog $model) {
                    return $model->getNewStatusName() ?? Yii::$app->formatter->nullDisplay;
                },
                'filter' => FlightRequest::STATUS_LIST
            ],
            'flr_description',
            'flr_created_dt',
            'flr_updated_dt',
        ],
    ]) ?>

</div>
