<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var $dataProvider ActiveDataProvider
 * @var $phone string
 * @var $clientId int
 */
Pjax::begin(['id' => 'pjax-client-same-phone', 'timeout' => 2000, 'enablePushState' => false, 'clientOptions' => ['method' => 'POST', 'data' => [
	'phone' => $phone,
	'clientId' => $clientId
]]]);

echo GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => null,
	'columns' => [
		//['class' => 'yii\grid\SerialColumn'],
		'id',
		'first_name',
		[
			'header' => 'Phones',
			'attribute' => 'client_phone',
			'value' => static function (\common\models\Client $model) {

				$phones = $model->clientPhones;
				$data = [];
				if ($phones) {
					foreach ($phones as $k => $phone) {
						$data[] = '<i class="fa fa-phone"></i> <code>' . Html::encode($phone->phone) . '</code>';
					}
				}

				$str = implode('<br>', $data);
				return '' . $str . '';
			},
			'format' => 'raw',
			'contentOptions' => ['class' => 'text-left'],
		],

		[
			'header' => 'Emails',
			'attribute' => 'client_email',
			'value' => static function (\common\models\Client $model) {

				$emails = $model->clientEmails;
				$data = [];
				if ($emails) {
					foreach ($emails as $k => $email) {
						$data[] = '<i class="fa fa-envelope"></i> <code>' . Html::encode($email->email) . '</code>';
					}
				}

				$str = implode('<br>', $data);
				return '' . $str . '';
			},
			'format' => 'raw',
			'contentOptions' => ['class' => 'text-left'],
		],

		[
			'header' => 'Leads',
			'value' => static function (\common\models\Client $model) {

				$leads = $model->leads;
				$data = [];
				if ($leads) {
					foreach ($leads as $lead) {
						$data[] = '<i class="fa fa-link"></i> ' . Html::a('lead: ' . $lead->id, ['leads/view', 'id' => $lead->id], ['target' => '_blank', 'data-pjax' => 0]) . ' (IP: ' . $lead->request_ip . ')';
					}
				}

				$str = '';
				if ($data) {
					$str = '' . implode('<br>', $data) . '';
				}

				return $str;
			},
			'format' => 'raw',
			//'options' => ['style' => 'width:100px']
		],

		[
			'attribute' => 'created',
			'value' => static function (\common\models\Client $model) {
				return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
			},
			'format' => 'html',
		],

		['class' => 'yii\grid\ActionColumn', 'template' => '{view}', 'controller' => 'client'],
	],
]);

Pjax::end();


