<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog */

$this->title = $model->ussl_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Schedule Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-shift-schedule-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ussl_id' => $model->ussl_id, 'ussl_month_start' => $model->ussl_month_start, 'ussl_year_start' => $model->ussl_year_start], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ussl_id' => $model->ussl_id, 'ussl_month_start' => $model->ussl_month_start, 'ussl_year_start' => $model->ussl_year_start], [
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
            'ussl_id',
            'ussl_uss_id',
            'ussl_old_attr',
            'ussl_new_attr',
            'ussl_formatted_attr',
            'ussl_created_user_id',
            'ussl_created_dt',
            'ussl_month_start',
            'ussl_year_start',
        ],
    ]) ?>

</div>
