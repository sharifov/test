<?php

use common\components\grid\BooleanColumn;
use common\components\grid\department\DepartmentColumn;
use common\models\Employee;
use common\components\grid\UserSelect2Column;
use common\models\UserProjectParams;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserProjectParamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Project Params';
$this->params['breadcrumbs'][] = $this->title;

$userList = [];

/** @var Employee $user */
$user = Yii::$app->user->identity;

if ($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}

?>
<div class="user-project-params-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
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
                'value' => function (\common\models\UserProjectParams $model) {
                    return $model->uppUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->uppUser->username) . '' : '-';
                },
                'format' => 'raw',
                'filter' => $userList
                //'contentOptions' => ['class' => 'text-right']
            ],

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'upp_project_id',
                'relation' => 'uppProject',
            ],

            ['class' => DepartmentColumn::class, 'attribute' => 'upp_dep_id', 'relation' => 'uppDep'],

            'upp_email:email',
            [
                'class' => \common\components\grid\EmailSelect2Column::class,
                'attribute' => 'upp_email_list_id',
                'relation' => 'emailList',
            ],
            'upp_tw_phone_number',
            [
                'class' => \common\components\grid\PhoneSelect2Column::class,
                'attribute' => 'upp_phone_list_id',
                'relation' => 'phoneList',
            ],
            ['class' => BooleanColumn::class, 'attribute' => 'upp_allow_general_line'],
            ['class' => BooleanColumn::class, 'attribute' => 'upp_allow_transfer'],
            ['class' => BooleanColumn::class, 'attribute' => 'upp_vm_enabled'],
            [
                'attribute' => 'upp_vm_user_status_id',
                'value' => static function (UserProjectParams $model) {
                    return UserProjectParams::VM_USER_STATUS_LIST[$model->upp_vm_user_status_id] ?? null;
                },
                'filter' => UserProjectParams::VM_USER_STATUS_LIST,
            ],
            [
                'attribute' => 'upp_vm_id',
                'value' => static function (UserProjectParams $model) {
                    return $model->upp_vm_id ? $model->voiceMail->uvm_name : null;
                },
            ],
            //'upp_tw_sip_id',
            //'upp_created_dt',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'upp_updated_dt'
            ],

            /*[
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
            ],*/
            ['class' => UserSelect2Column::class, 'attribute' => 'upp_updated_user_id', 'relation' => 'uppUpdatedUser'],
            //'upp_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
