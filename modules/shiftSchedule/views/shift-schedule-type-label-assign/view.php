<?php

use modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign */

$this->title = $model->tla_stl_key . ' - ' . $model->tla_sst_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Type Label Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-schedule-type-label-assign-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update',
            'tla_stl_key' => $model->tla_stl_key, 'tla_sst_id' => $model->tla_sst_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete',
            'tla_stl_key' => $model->tla_stl_key, 'tla_sst_id' => $model->tla_sst_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'tla_stl_key',
                'value' => static function (ShiftScheduleTypeLabelAssign $model) {
                    return $model->tla_stl_key . ' - ' . $model->getShiftTypeLabel();
                },
            ],
            //'tla_sst_id',
            [
                'attribute' => 'tla_sst_id',
                'value' => static function (ShiftScheduleTypeLabelAssign $model) {
                    return $model->getShiftTypeName();
                }
            ],
            'tla_created_dt:datetime',
        ],
    ]) ?>
    </div>

</div>
