<?php

use common\models\UserGroupSet;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'ug_id',
            'ug_key',
            'ug_name',
            'ug_description',
            'ug_processing_fee',
            'ug_disable:boolean',
            //'ug_on_leaderboard:boolean',
            [
                'attribute' => 'ug_on_leaderboard',
                'format' => 'raw',
                'value' => function(\common\models\UserGroup $model) {
                    return $model->ug_on_leaderboard ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                },
                'contentOptions' => ['class' => 'text-left'],
            ],
            [
                'attribute' => 'ug_updated_dt',
                'value' => function(\common\models\UserGroup $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ug_updated_dt));
                },
                'format' => 'html',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'ug_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            [
                'attribute' => 'ug_user_group_id',
                'format' => 'raw',
                'value' => function(\common\models\UserGroup $model) {
                    if ($model->ug_user_group_id) {
                        return $model->userGroupSet->ugs_name;
                    }
                    return '';
                },
                'filter' => UserGroupSet::getList()
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
