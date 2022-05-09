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

$this->title = $model->ssr_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="shift-schedule-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'ssr_id' => $model->ssr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'ssr_id' => $model->ssr_id], [
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
            'ssr_id',
            'ssr_uss_id',
            'ssr_sst_id',
            'ssr_status_id',
            'ssr_description',
            'ssr_created_dt',
            'ssr_update_dt',
            'ssr_created_user_id',
            'ssr_updated_user_id',
        ],
    ]) ?>

</div>
