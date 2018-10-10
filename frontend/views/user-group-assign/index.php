<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserGroupAssignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Group Assigns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-assign-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Group Assign', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'ugs_user_id',
            [
                'attribute' => 'ugs_user_id',
                'value' => function(\common\models\UserGroupAssign $model) {
                    return $model->ugsUser ? $model->ugsUser->username : '-' ;
                },
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 'ugs_group_id',
                'value' => function(\common\models\UserGroupAssign $model) {
                    return $model->ugsGroup ? $model->ugsGroup->ug_name : '-' ;
                },
                'filter' => \common\models\UserGroup::getList()
            ],

            //'ugs_group_id',
            //'ugs_updated_dt',
            [
                'attribute' => 'ugs_updated_dt',
                'value' => function(\common\models\UserGroupAssign $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ugs_updated_dt));
                },
                'format' => 'html',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
