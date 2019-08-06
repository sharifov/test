<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserProjectParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Project Params';
$this->params['breadcrumbs'][] = $this->title;

$userList = [];
$projectList = [];

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

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

            //'upp_user_id',
            //'upp_project_id',
            'upp_email:email',
            'upp_phone_number',
            'upp_tw_phone_number',
            [
                'attribute' => 'upp_allow_general_line',
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'boolean'

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
