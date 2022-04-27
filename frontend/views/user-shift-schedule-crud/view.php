<?php

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule */

$this->title = 'Time Line ' . $model->uss_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-shift-schedule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'id' => $model->uss_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->uss_id], [
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
//                'uss_shift_id',
//                'uss_ssr_id',

                [
                    'attribute' => 'uss_shift_id',
                    'value' => static function (
                        UserShiftSchedule $model
                    ) {
                        return $model->getShiftTitle();
                    },
                ],

                [
                    'attribute' => 'uss_ssr_id',
                    'value' => static function (
                        UserShiftSchedule $model
                    ) {
                        return $model->getRuleTitle();
                    },
                ],

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
                'uss_customized:boolean',
                'uss_created_dt:byUSerDateTime',
                'uss_updated_dt:byUSerDateTime',
                'uss_created_user_id:username',
                'uss_updated_user_id:username',
                'uss_year_month',
                'uss_year_start'
            ],
        ]) ?>

    </div>

</div>
