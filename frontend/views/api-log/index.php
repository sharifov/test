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

            'al_id',
            [
                'attribute' => 'al_action',
                'value' => function(\common\models\ApiLog $model) {
                    return $model->al_action;
                },
                'filter' => \common\models\ApiLog::getActionFilter()
            ],
            //'al_request_data:ntext',

            [
                'attribute' => 'al_request_data',
                'format' => 'html',
                'value' => function(\common\models\ApiLog $model) {
                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_request_data, true));
                    //if($data) $data = end($data);
                    return $data ? '<small>'.\yii\helpers\StringHelper::truncate($data, 200, '...', null, true).'</small>' : '-';
                },
            ],

            'al_request_dt',
            //'al_response_data:ntext',
            [
                'attribute' => 'al_response_data',
                'format' => 'html',
                'value' => function(\common\models\ApiLog $model) {
                    $data = \yii\helpers\VarDumper::dumpAsString(@json_decode($model->al_response_data, true));
                    //if($data) $data = end($data);
                    return $data ? '<small>'.\yii\helpers\StringHelper::truncate($data, 500, '...', null, true).'</small>' : '-';
                },
            ],
            'al_response_dt',
            'al_user_id',
            'al_ip_address',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
