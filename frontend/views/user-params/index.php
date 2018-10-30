<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Params';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-params-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Params', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'up_user_id',

            [
                'attribute' => 'upUser.username',
                /*'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
                'contentOptions' => ['class' => 'text-right'],*/
            ],

            //'up_commission_percent',
            //'up_base_amount',

            [
                'attribute' => 'up_commission_percent',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],

            [
                'attribute' => 'up_base_amount',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_commission_percent ? $model->up_commission_percent. '%' : '-';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],

            [
                'attribute' => 'up_bonus_active',
                'value' => function(\common\models\UserParams $model) {
                    return $model->up_bonus_active ? 'Yes' : 'No';
                },
                'contentOptions' => ['class' => 'text-right'],
            ],
            'up_timezone',
            'up_work_start_tm',
            'up_work_minutes',
            [
                'attribute' => 'up_updated_dt',
                'value' => function(\common\models\UserParams $model) {
                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->up_updated_dt));
                },
                'format' => 'raw',
            ],
            'up_updated_user_id',
            [
                    'label' => 'Updated User',
                'attribute' => 'upUpdatedUser.username',
                /*'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
                'contentOptions' => ['class' => 'text-right'],*/
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
