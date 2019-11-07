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
				$template = '';
				if ($model->gl_app_user_id) {
					if ($model->isAppFrontend() && $model->userFrontend) {
						$template .= '<i class="fa fa-user"></i> ';
						$template .= \yii\helpers\Html::encode($model->userFrontend->username) . ' (' . $model->gl_app_user_id . ')<br>(' . implode(', ', $model->userFrontend->getRoles()) . ')';
					} elseif ($model->isAppWebApi() && $model->userApi) {
						$template .= 'WebAPI: ' . \yii\helpers\Html::encode($model->userApi->au_name) . '(' . $model->gl_app_user_id . ')';
					} else {
						$template .= 'UserId: ' . $model->gl_app_user_id;
					}
				} elseif ($model->isAppConsole()) {
					$template .= 'Console';
				} else {
					$template .= 'Unknown App';
				}

				$template .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->gl_created_at));

				return $template;
			},
			'format' => 'raw',
			'options' => [
				'width' => '15%'
			]

		],
		[
			'attribute' => 'gl_model',
			'value' => static function (GlobalLog $model) {
				return ($model->getModelName() ?: $model->gl_model) . ' (#' . $model->gl_obj_id . ')';
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
			'format' => 'raw'
		]
	],
]);

Pjax::end();