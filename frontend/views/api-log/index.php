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
        <?php //= Html::a('Create Api Log', ['create'], ['class' => 'btn btn-success']) ?>
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
                    return '<b>'.Html::encode($model->al_action).'</b>';
                },
                'format' => 'raw',
                'filter' => \common\models\ApiLog::getActionFilter()
            ],

            [
                'label' => 'Relative Time',
                'value' => static function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '' . Yii::$app->formatter->asRelativeTime(strtotime($model->al_request_dt)) : '-';
                },
                //'format' => 'raw'
            ],

            //'al_request_data:ntext',

            [
                'attribute' => 'al_request_data',
                'format' => 'raw',
                'value' => function(\common\models\ApiLog $model) {
                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_request_data, true));
                    //if($data) $data = end($data);
                    $str = \yii\helpers\StringHelper::truncate(Html::encode($data), 1600, '...', null, false);
                    $str = preg_replace("~CA(\w+)~", '<b style="color: darkred">${0}</b>', $str);

                    $str = str_replace('CallSid', '<b style="color: green">CallSid</b>', $str);

                    $str = str_replace('CallStatus', '<b style="color: #2b3f63">CallStatus</b>', $str);



                    return $data ? '<small>'.$str.'</small>' : '-';
                },
            ],

            //'al_request_dt',

            [
                'attribute' => 'al_request_dt',
                'value' => static function (\common\models\ApiLog $model) {
                    return $model->al_request_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->al_request_dt), 'php:Y-m-d [H:i:s]') : '-';
                },
                'format' => 'raw'
            ],

            //'al_response_data:ntext',
//            [
//                'attribute' => 'al_response_data',
//                'format' => 'html',
//                'value' => function(\common\models\ApiLog $model) {
//                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true));
//                    //if($data) $data = end($data);
//                    return $data ? '<pre style="font-size: 10px">'.(\yii\helpers\StringHelper::truncate($data, 500, '...', null, true)).'</pre>' : '-';
//                },
//            ],

            [
                'attribute' => 'al_response_data',
                'format' => 'raw',
                'value' => function(\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize(mb_strlen($model->al_response_data), 1);
                    //$data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true));
                    //if($data) $data = end($data);
                    //return $data ? '<small>'.\yii\helpers\StringHelper::truncate(Html::encode($data), 500, '...', null, false).'</small>' : '-';
                },
            ],

            //'al_response_dt',
//            [
//                'attribute' => 'al_response_dt',
//                'value' => static function (\common\models\ApiLog $model) {
//                    return $model->al_response_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->al_response_dt), 'php:Y-m-d [H:i:s]') : '-';
//                },
//                'format' => 'raw'
//            ],

            [
                'attribute' => 'al_execution_time',
                //'format' => 'html',
                'value' => function(\common\models\ApiLog $model) {
                    return $model->al_execution_time;
                },
            ],
            [
                'attribute' => 'al_memory_usage',
                'format' => 'raw',
                'value' => function(\common\models\ApiLog $model) {
                    return Yii::$app->formatter->asShortSize($model->al_memory_usage, 2);
                },
            ],

            [
                'attribute' => 'al_db_execution_time',
                'value' => function(\common\models\ApiLog $model) {
                    return $model->al_db_execution_time;
                },
            ],

            [
                'attribute' => 'al_db_query_count',
                'value' => function(\common\models\ApiLog $model) {
                    return $model->al_db_query_count;
                },
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
