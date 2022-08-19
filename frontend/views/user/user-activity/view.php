<?php

use common\components\grid\UserSelect2Column;
use modules\user\userActivity\entity\UserActivity;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\user\userActivity\entity\UserActivity */

$this->title = 'User Activity: DateTime ' . $model->ua_start_dt . ' - User ' . $model->ua_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-activity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            '<i class="fa fa-edit"></i> Update',
            ['update', 'ua_start_dt' => $model->ua_start_dt,
            'ua_user_id' => $model->ua_user_id, 'ua_object_event' => $model->ua_object_event,
                'ua_object_id' => $model->ua_object_id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'ua_start_dt' => $model->ua_start_dt,
            'ua_user_id' => $model->ua_user_id, 'ua_object_event' => $model->ua_object_event,
            'ua_object_id' => $model->ua_object_id], [
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
                    'class' => UserSelect2Column::class,
                    'attribute' => 'ua_user_id',
                    'relation' => 'user'
                ],
                [
                    'attribute' => 'ua_object_event',
                    'value' => static function (UserActivity $model) {
                        return $model->getEventName();
                    },
                ],
                'ua_object_id',
                'ua_start_dt',
                'ua_end_dt',
                [
                    'attribute' => 'ua_type_id',
                    'value' => static function (UserActivity $model) {
                        return $model->getTypeName();
                    },
                ],
                'ua_description',
                'ua_shift_event_id'
            ],
        ]) ?>
    </div>

</div>
