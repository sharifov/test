<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApiLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Api Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create Api Log', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Delete All', ['delete-all'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all items?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'al_id',

            [
                'attribute' => 'al_id',
                'value' => function(\common\models\ApiLog $model) {
                    return $model->al_id;
                },
                'options' => ['style' => 'width:100px']
            ],

            [
                'attribute' => 'al_action',
                'value' => function(\common\models\ApiLog $model) {
                    return $model->al_action;
                },
                'filter' => \common\models\ApiLog::getActionFilter()
            ],

            [
                'label' => 'Relative Time',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '' . Yii::$app->formatter->asRelativeTime(strtotime($model->al_request_dt)) : '-';
                },
                //'format' => 'raw'
            ],

            //'al_request_data:ntext',

            [
                'attribute' => 'al_request_data',
                'format' => 'html',
                'value' => function(\common\models\ApiLog $model) {
                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_request_data, true));
                    //if($data) $data = end($data);
                    return $data ? '<pre style="font-size: 10px">'.(\yii\helpers\StringHelper::truncate($data, 200, '...', null, true)).'</pre>' : '-';
                },
            ],

            //'al_request_dt',

            [
                'attribute' => 'al_request_dt',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->al_request_dt)) : '-';
                },
                'format' => 'raw'
            ],

            //'al_response_data:ntext',
            [
                'attribute' => 'al_response_data',
                'format' => 'html',
                'value' => function(\common\models\ApiLog $model) {
                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true));
                    //if($data) $data = end($data);
                    return $data ? '<pre style="font-size: 10px">'.(\yii\helpers\StringHelper::truncate($data, 500, '...', null, true)).'</pre>' : '-';
                },
            ],
            //'al_response_dt',
            [
                'attribute' => 'al_response_dt',
                'value' => function (\common\models\ApiLog $model) {
                    return $model->al_response_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->al_response_dt)) : '-';
                },
                'format' => 'raw'
            ],
            //'al_user_id',

            [
                'attribute' => 'al_user_id',
                //'format' => 'html',
                'value' => function(\common\models\ApiLog $model) {
                    $apiUser = \common\models\ApiUser::findOne($model->al_user_id);
                    return $apiUser ? $apiUser->au_name . ' ('.$model->al_user_id.')' : $model->al_user_id;
                },
                'filter' => \common\models\ApiUser::getList()
            ],

            'al_ip_address',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
