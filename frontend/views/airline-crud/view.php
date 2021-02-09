<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Airline */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Airlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="airline-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
                'id',
                'name',
                'iata',
                'code',
                'iaco',
                'countryCode',
                'country',
                'cl_economy',
                'cl_premium_economy',
                'cl_business',
                'cl_premium_business',
                'cl_first',
                'cl_premium_first',
                'updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
