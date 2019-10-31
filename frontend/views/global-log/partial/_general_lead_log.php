<?php

use sales\entities\log\GlobalLogSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\GlobalLog;

/**
 * @var $dataProvider ActiveDataProvider
 * @var $searchModel GlobalLogSearch
 * @var $lid int
 * @var $this \yii\web\View
 */
Pjax::begin(['id' => 'pjax-general-lead-log', 'formSelector' => 'GlobalLogSearch', 'timeout' => 2000, 'enablePushState' => false, 'clientOptions' => ['method' => 'post', 'data' => [
	'lid' => $lid,
]]]);

$view = $this;

echo GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => false,
	'tableOptions' => [
		'class' => 'table table-striped table-bordered table-responsive'
	],
	'columns' => [
		[
			'header' => 'Who made the changes',
			'attribute' => 'gl_app_user_id',
			'value' => static function (GlobalLog $model) {
				$template = '<i class="fa fa-user"></i> ';

				if ($model->user) {
					if ($model->user->username) {
						$template .= $model->user->username . ' <br>(' . implode(', ', $model->user->getRoles()) .')';
					} elseif ($model->user->au_name) {
						$template .= $model->user->au_name;
					}
				} else {

					$template .= 'Console';
				}

				$template .= '<br><br> <i class="fa fa-calendar"></i> ' . $model->gl_created_at;

				return $template;
			},
			'format' => 'html',
			'options' => [
				'width' => '15%'
			]

		],
		[
			'attribute' => 'gl_model',
			'value' => static function (GlobalLog $model) {
				return $model->getModelName() . ' (#' . $model->gl_obj_id . ')';
			},
			'options' => [
				'width' => '12%'
			]
		],
		[
			'header' => 'Changed Attributes',
			'attribute' => 'gl_formatted_attr',
			'value' => static function (GlobalLog $model) use ($view) {
				if ($model->gl_formatted_attr) {
					return $view->render('_formatted_attributes', [
						'model' => $model
					]);
				}

				return 'Data log not formatted yet';
			},
			'filter' => false,
			'enableSorting' => false,
			'format' => 'html'
		]
	],
]);

Pjax::end();