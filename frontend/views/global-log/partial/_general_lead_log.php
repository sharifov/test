<?php

use sales\entities\log\GlobalLogSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

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
	'columns' => [
		[
			'header' => 'Who made the changes',
			'attribute' => 'gl_app_user_id',
			'value' => static function (\sales\entities\log\GlobalLog $model) {
				if ($model->user->username) {
					return $model->user->username;
				}

				if ($model->user->au_name) {
					return $model->user->au_name;
				}

				return 'Console';
			}
		],
		'glModel',
		[
			'header' => 'Changed Attributes',
			'attribute' => 'gl_formatted_attr',
			'value' => static function (\sales\entities\log\GlobalLog $model) use ($view) {
				if ($model->gl_formatted_attr) {
					return $view->render('_formatted_attributes', [
						'formattedAttributes' => json_decode((string)$model->gl_formatted_attr, true)
					]);
				} else {
					return '------';
				}
			},
			'filter' => false,
			'enableSorting' => false,
			'format' => 'html'
		],
		'gl_created_at'
		//['class' => 'yii\grid\SerialColumn'],
//		'id',
//		'first_name',
//		[
//			'header' => 'Phones',
//			'attribute' => 'client_phone',
//			'value' => function (\common\models\Client $model) {
//
//				$phones = $model->clientPhones;
//				$data = [];
//				if ($phones) {
//					foreach ($phones as $k => $phone) {
//						$data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code>';
//					}
//				}
//
//				$str = implode('<br>', $data);
//				return '' . $str . '';
//			},
//			'format' => 'raw',
//			'contentOptions' => ['class' => 'text-left'],
//		],
//
//		[
//			'header' => 'Emails',
//			'attribute' => 'client_email',
//			'value' => function (\common\models\Client $model) {
//
//				$emails = $model->clientEmails;
//				$data = [];
//				if ($emails) {
//					foreach ($emails as $k => $email) {
//						$data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode($email->email) . '</code>';
//					}
//				}
//
//				$str = implode('<br>', $data);
//				return '' . $str . '';
//			},
//			'format' => 'raw',
//			'contentOptions' => ['class' => 'text-left'],
//		],
//
//		[
//			'header' => 'Leads',
//			'value' => function (\common\models\Client $model) {
//
//				$leads = $model->leads;
//				$data = [];
//				if ($leads) {
//					foreach ($leads as $lead) {
//						$data[] = '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $lead->id, ['leads/view', 'id' => $lead->id], ['target' => '_blank', 'data-pjax' => 0]) . ' (IP: ' . $lead->request_ip . ')';
//					}
//				}
//
//				$str = '';
//				if ($data) {
//					$str = '' . implode('<br>', $data) . '';
//				}
//
//				return $str;
//			},
//			'format' => 'raw',
//			//'options' => ['style' => 'width:100px']
//		],
//
//		[
//			'attribute' => 'created',
//			'value' => function (\common\models\Client $model) {
//				return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
//			},
//			'format' => 'html',
//		],
//
//		['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'client'],
	],
]);

Pjax::end();