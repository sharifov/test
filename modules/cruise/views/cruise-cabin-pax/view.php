<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax */

$this->title = $model->crp_id;
$this->params['breadcrumbs'][] = ['label' => 'Cruise Cabin Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cruise-cabin-pax-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->crp_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->crp_id], [
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
                'crp_id',
                'crp_cruise_cabin_id',
                'crp_type_id',
                'crp_age',
                'crp_first_name',
                'crp_last_name',
                'crp_dob',
            ],
        ]) ?>

    </div>

</div>
