<?php

use modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType */

$this->title = $model->sst_key;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-schedule-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'sst_id' => $model->sst_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'sst_id' => $model->sst_id], [
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
            'sst_id',
            'sst_key',
            'sst_name',
            'sst_title',
            'sst_enabled:boolean',
            'sst_readonly:boolean',
            [
                'attribute' => 'sst_subtype_id',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->getSubtypeName();
                }
            ],
            'sst_color',
            'sst_icon_class',
            [
                'label' => 'icon',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_icon_class ? Html::tag(
                        'i',
                        '',
                        ['class' => $model->sst_icon_class]
                    ) : '-';
                },
                'format' => 'raw',
            ],
            [
                'label' => 'color',
                'value' => static function (ShiftScheduleType $model) {
                    return $model->sst_color ? Html::tag(
                        'span',
                        '&nbsp;&nbsp;&nbsp;',
                        ['class' => 'label', 'style' => 'background-color: ' . $model->sst_color]
                    ) : '-';
                },
                'format' => 'raw',
            ],
            [
                'label' => 'Labels',
                'value' => static function (ShiftScheduleType $model) {
                    $labelList = $model->getLabelList();
                    return $labelList ? implode(', ', $labelList) : '-';
                },
            ],
            'sst_css_class',
            'sst_params_json',
            'sst_sort_order',
            'sst_updated_dt:datetime',
            'sst_updated_user_id:username',
        ],
    ]) ?>
    </div>

</div>
