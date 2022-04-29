<?php

/**
 * @var View $this
 * @var ShiftScheduleRequest $model
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

$this->title = $model->srh_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shift Schedule Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="shift-schedule-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'srh_id' => $model->srh_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'srh_id' => $model->srh_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'srh_id',
            'srh_uss_id',
            'srh_sst_id',
            'srh_status_id',
            'srh_description',
            'srh_created_dt',
            'srh_update_dt',
            'srh_created_user_id',
            'srh_updated_user_id',
        ],
    ]) ?>

</div>
