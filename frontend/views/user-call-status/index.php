<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserCallStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Call Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-call-status-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create User Call Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'us_id',
            //'us_type_id',
            [
                'attribute' => 'us_type_id',
                'value' => function (\common\models\UserCallStatus $model) {
                    return $model->getStatusTypeName();
                },
                'format' => 'raw',
                'filter' => \common\models\UserCallStatus::STATUS_TYPE_LIST
            ],
            //'us_user_id',
            [
                'attribute' => 'us_user_id',
                'value' => function (\common\models\UserCallStatus $model) {
                    return ($model->usUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->usUser->username) : $model->us_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 'us_created_dt',
                'value' => function (\common\models\UserCallStatus $model) {
                    return $model->us_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->us_created_dt)) : $model->us_created_dt;
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
