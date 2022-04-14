<?php

use modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule */

$this->title = $model->ssr_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-schedule-rule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ssr_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ssr_id], [
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
                [
                    'attribute' => 'ssr_shift_id',
                    'value' => static function (ShiftScheduleRule $model) {
                        return \yii\helpers\Html::a(
                            'shift: ' . $model->shift->sh_name . '(' . $model->shift->sh_id . ')',
                            Url::to(['/shift-crud/view', 'id' => $model->shift->sh_id]),
                            ['target' => '_blank', 'data-pjax' => 0]
                        );
                    },
                    'format' => 'raw'
                ],
                'ssr_title',
                'ssr_timezone',
                'ssr_start_time_loc',
                'ssr_end_time_loc',
                'ssr_duration_time',
                'ssr_cron_expression',
                'ssr_cron_expression_exclude',
                'ssr_enabled:booleanByLabel',
                'ssr_start_time_utc',
                'ssr_end_time_utc',
                'ssr_created_dt:byUserDateTime',
                'ssr_updated_dt:byUserDateTime',
                'ssr_created_user_id:username',
                'ssr_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
