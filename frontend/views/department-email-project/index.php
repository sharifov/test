<?php

use common\components\grid\BooleanColumn;
use common\components\grid\department\DepartmentColumn;
use common\components\grid\EmailSelect2Column;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DepartmentEmailProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Department Email Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-email-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Department Email Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'tableOptions' => ['class' => 'table table-bordered table-hover'],
		'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'dep_id',
			[
				'attribute' => 'dep_project_id',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
					return $model->depProject ? '<span class="badge">' . Html::encode($model->depProject->name) . '</span>' : '-';
				},
				'filter' => \common\models\Project::getList(true),
				'format' => 'raw',
			],
            'dep_email',
            [
                'class' => \common\components\grid\EmailSelect2Column::class,
                'attribute' => 'dep_email_list_id',
                'relation' => 'emailList',
            ],
            [
                'class' => DepartmentColumn::class,
                'attribute' => 'dep_dep_id',
                'relation' => 'depDep',
            ],
			[
				'label' => 'User Groups',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
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
				'attribute' => 'dep_source_id',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
					return $model->depSource ? $model->depSource->name : '-';
				},
				'filter' => \common\models\Sources::getList(true)
			],
            ['class' => BooleanColumn::class, 'attribute' => 'dep_enable'],
            ['class' => BooleanColumn::class, 'attribute' => 'dep_default'],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'dep_updated_user_id',
                'relation' => 'depUpdatedUser',
            ],
			[
				'attribute' => 'dep_updated_dt',
				'value' => static function (\common\models\DepartmentEmailProject $model) {
					return $model->dep_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dep_updated_dt)) : '-';
				},
				'format' => 'raw'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
