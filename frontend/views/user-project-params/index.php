<?php

use common\models\Employee;
use sales\access\EmployeeProjectAccess;
use sales\yii\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserProjectParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Project Params';
$this->params['breadcrumbs'][] = $this->title;

$userList = [];
$projectList = [];

/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}

$projectList = EmployeeProjectAccess::getProjects($user->id);


?>
<div class="user-project-params-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Project Params', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'upp_user_id',
                'value' => function(\common\models\UserProjectParams $model) {
                    return $model->uppUser ? '<i class="fa fa-user"></i> '.Html::encode($model->uppUser->username).'' : '-';
                },
                'format' => 'raw',
                'filter' => $userList
                //'contentOptions' => ['class' => 'text-right']
            ],

            [
                'attribute' => 'upp_project_id',
                'value' => function(\common\models\UserProjectParams $model) {
                    return $model->uppProject ? ''.$model->uppProject->name.'' : '-';
                },
                'filter' => $projectList
                //'format' => 'raw'
                //'contentOptions' => ['class' => 'text-right']
            ],

            [
                'attribute' => 'upp_dep_id',
                'value' => function(\common\models\UserProjectParams $model) {
                    return $model->uppDep ? ''.$model->uppDep->dep_name.'' : '-';
                },
                'filter' => \common\models\Department::getList()
                //'format' => 'raw'
                //'contentOptions' => ['class' => 'text-right']
            ],

            //'upp_user_id',
            //'upp_project_id',
            'upp_email:email',
            [
                'class' => \sales\yii\grid\EmailSelect2Column::class,
                'attribute' => 'upp_email_list_id',
                'relation' => 'emailList',
            ],
            'upp_tw_phone_number',
            [
                'class' => \sales\yii\grid\PhoneSelect2Column::class,
                'attribute' => 'upp_phone_list_id',
                'relation' => 'phoneList',
            ],
            [
                'attribute' => 'upp_allow_general_line',
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No'],
                'value' => function(\common\models\UserProjectParams $model) {
                    if ($model->upp_allow_general_line) {
                        return '<span class="label label-success">Yes</span>';
                    }
                    return '<span class="label label-danger">No</span>';
                }
            ],
            //'upp_tw_sip_id',
            //'upp_created_dt',
            //'upp_updated_dt',
            [
                'attribute' => 'upp_updated_dt',
                'value' => function(\common\models\UserProjectParams $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->upp_updated_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'upp_updated_dt',
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
                'label' => 'Updated User',
                'attribute' => 'uppUpdatedUser.username',
                /*'value' => function(\common\models\UserParams $model) {
                    return $model->up_base_amount ? '$'.number_format($model->up_base_amount , 2) : '-';
                },
                'contentOptions' => ['class' => 'text-right'],*/
            ],
            //'upp_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
