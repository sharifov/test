<?php

use modules\featureFlag\FFlag;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Params';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-params-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Params', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            /*'up_user_id',
            [
                'attribute' => 'upUser.username',
                //'value' => function(\common\models\UserParams $model) {
                //    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                //},
                //'contentOptions' => ['class' => 'text-right'],
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'up_user_id',
                'relation' => 'upUser',
                'placeholder' => 'Select User',
            ],

            //'up_commission_percent',
            //'up_base_amount',

            [
                'attribute' => 'up_commission_percent',
                'value' => function (\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$' . number_format($model->up_base_amount, 2) : '-';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],

            [
                'attribute' => 'up_base_amount',
                'value' => function (\common\models\UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent . '%' : '-';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],

            [
                'attribute' => 'up_bonus_active',
                'value' => function (\common\models\UserParams $model) {
                    return $model->up_bonus_active ? 'Yes' : 'No';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],
            'up_timezone',
            'up_work_start_tm',
            'up_work_minutes',
            'up_inbox_show_limit_leads',
            [
                'attribute' => 'up_business_inbox_show_limit_leads',
                /** @fflag FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT, Business Queue Limit Enable */
                'visible' => \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BUSINESS_QUEUE_LIMIT)
            ],
            'up_default_take_limit_leads',
            'up_min_percent_for_take_leads',
            'up_call_expert_limit',
            [
                'attribute' => 'up_leaderboard_enabled',
                'format' => 'raw',
                'value' => function (\common\models\UserParams $model) {
                    return $model->up_leaderboard_enabled ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],
            'up_call_user_level',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'up_updated_dt'
            ],

            /*[
                'attribute' => 'up_updated_dt',
                'value' => function(\common\models\UserParams $model) {
                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->up_updated_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'up_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/
           /* 'up_updated_user_id',
            [
                    'label' => 'Updated User',
                'attribute' => 'upUpdatedUser.username',
                //'value' => function(\common\models\UserParams $model) {
                //    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                //},
                //'contentOptions' => ['class' => 'text-right'],
            ],*/

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'up_updated_user_id',
                'relation' => 'upUpdatedUser',
                'placeholder' => 'Select User',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
