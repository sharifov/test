<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\ShiftScheduleRequestHistory */

$this->title = $model->ssrh_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Request Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-schedule-request-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ssrh_id' => $model->ssrh_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ssrh_id' => $model->ssrh_id], [
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
            'ssrh_id',
            'ssrh_ssr_id',
            'ssrh_old_attr',
            'ssrh_new_attr',
            'ssrh_formatted_attr',
            'ssrh_created_dt',
            'ssrh_updated_dt',
            'ssrh_created_user_id',
            'ssrh_updated_user_id',
        ],
    ]) ?>

</div>
