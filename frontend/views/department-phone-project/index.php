<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DepartmentPhoneProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Department Phone Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-phone-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Department Phone Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'rowOptions' => function (\common\models\DepartmentPhoneProject $model) {
            if (!$model->dpp_enable) {
                return ['class' => 'danger'];
            }
            if (!$model->dpp_ivr_enable) {
                return ['class' => 'warning'];
            }
            return [];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'dpp_id',

            //'dpp_project_id',
            [
                'attribute' => 'dpp_project_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppProject ? '<span class="badge">' . Html::encode($model->dppProject->name) . '</span>' : '-';
                },
                'filter' => \common\models\Project::getList(true),
                'format' => 'raw',
            ],
            'dpp_phone_number',
            [
                'attribute' => 'dpp_dep_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppDep ? $model->dppDep->dep_name : '-';
                },
                'filter' => \common\models\Department::getList()
            ],
            //'dpp_source_id',

            [
                'label' => 'User Groups',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    $userGroupList = [];
                    if ($model->dugUgs) {
                        foreach ($model->dugUgs as $userGroup) {
                            $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                        }
                    }
                    return $userGroupList ? implode(' ', $userGroupList) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'dpp_source_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppSource ? $model->dppSource->name : '-';
                },
                'filter' => \common\models\Sources::getList(true)
            ],

            //'dpp_params',
            'dpp_ivr_enable:boolean',
            'dpp_enable:boolean',

            [
                'attribute' => 'dpp_updated_user_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dpp_updated_user_id ? '<i class="fa fa-user"></i> ' .Html::encode($model->dppUpdatedUser->username) : $model->dpp_updated_user_id;
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 'dep_updated_dt',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dpp_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dpp_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],

            //'dpp_updated_user_id',
            //'dpp_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
