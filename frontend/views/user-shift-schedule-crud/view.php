<?php

use sales\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\shiftSchedule\entity\userShiftSchedule\UserShiftSchedule */

$this->title = $model->uss_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-shift-schedule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->uss_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->uss_id], [
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
                'uss_id',
                'uss_user_id:username',
                'uss_shift_id',
                'uss_ssr_id',
                'uss_description',
                'uss_start_utc_dt:byUSerDateTime',
                'uss_end_utc_dt:byUSerDateTime',
                'uss_duration',
                [
                    'attribute' => 'uss_status_id',
                    'value' => static function (UserShiftSchedule $model) {
                        return $model->getStatusName();
                    }
                ],
                [
                    'attribute' => 'uss_type_id',
                    'value' => static function (UserShiftSchedule $model) {
                        return $model->getTypeName();
                    }
                ],
                'uss_customized',
                'uss_created_dt:byUSerDateTime',
                'uss_updated_dt:byUSerDateTime',
                'uss_created_user_id:username',
                'uss_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
