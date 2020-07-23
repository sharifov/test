<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Airports */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Airports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="airports-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->iata], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->iata], [
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
            'name',
            'city',
            'country',
            'iata',
            'latitude',
            'longitude',
            'timezone',
            'dst',
            'a_created_user_id',
            'a_updated_user_id',
            'a_icao',
            'a_country_code',
            'a_city_code',
            'a_state',
            'a_rank',
            'a_multicity',
            'a_close',
            'a_disabled',
            'a_created_dt',
            'a_updated_dt',
        ],
    ]) ?>

</div>
