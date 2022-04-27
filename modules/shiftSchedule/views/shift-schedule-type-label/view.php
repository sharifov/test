<?php

use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel */

$this->title = 'Type label: ' . $model->stl_key;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Type Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-schedule-type-label-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'stl_key' => $model->stl_key], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'stl_key' => $model->stl_key], [
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
            'stl_key',
            'stl_name',
            'stl_enabled:boolean',
            'stl_color',
            'stl_icon_class',
            [
                'label' => 'Color',
                'value' => static function (ShiftScheduleTypeLabel $model) {
                    return $model->stl_color ? Html::tag(
                        'span',
                        '&nbsp;&nbsp;&nbsp;',
                        ['class' => 'label', 'style' => 'background-color: ' . $model->stl_color]
                    ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:60px']
            ],
            'stl_color',
            [
                'label' => 'Icon',
                'value' => static function (ShiftScheduleTypeLabel $model) {
                    return $model->stl_icon_class ? Html::tag(
                        'i',
                        '',
                        ['class' => $model->stl_icon_class] // , 'style' => 'color: ' . $model->sst_color
                    ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:50px']
            ],
            'stl_params_json',
            'stl_sort_order',
            'stl_updated_dt:datetime',
            'stl_updated_user_id:username',
        ],
    ]) ?>
    </div>

</div>
