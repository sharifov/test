<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserParams */

$this->title = $model->up_user_id;
$this->params['breadcrumbs'][] = ['label' => 'User Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-params-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->up_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->up_user_id], [
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
            'up_user_id',
            'upUser.username',
            [
                'attribute' => 'up_base_amount',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },

            ],

            [
                'attribute' => 'up_commission_percent',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent. '%' : '-';
                },

            ],
            'up_bonus_active:boolean',
            'up_timezone',
            'up_work_start_tm',
            'up_work_minutes',
            'up_inbox_show_limit_leads',
            'up_default_take_limit_leads',
            'up_min_percent_for_take_leads',
            'up_call_expert_limit',
            [
                'attribute' => 'up_updated_dt',
                'value' => function(\common\models\UserParams $model) {
                 return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->up_updated_dt));
                },
                'format' => 'raw',
             ],
             'upUpdatedUser.username'
        ],
    ]) ?>

</div>
