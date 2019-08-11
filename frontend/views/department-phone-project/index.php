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
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'dpp_dep_id',
            [
                'attribute' => 'dpp_dep_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppDep ? $model->dppDep->dep_name : '-';
                },
                'filter' => \common\models\Project::getList(true)
            ],
            //'dpp_project_id',
            [
                'attribute' => 'dpp_project_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppProject ? $model->dppProject->name : '-';
                },
                'filter' => \common\models\Project::getList(true)
            ],
            'dpp_phone_number',
            //'dpp_source_id',
            [
                'attribute' => 'dpp_source_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppSource ? $model->dppSource->name : '-';
                },
                'filter' => \common\models\Sources::getList(true)
            ],
            //'dpp_params',
            'dpp_avr_enable:boolean',
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
