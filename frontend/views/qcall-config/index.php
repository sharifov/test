<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QcallConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qcall Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qcall-config-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add new record', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'qc_status_id',
            [
                'attribute' => 'qc_status_id',
                'value' => function (\common\models\QcallConfig $model) {
                    return  \common\models\Lead::getStatus($model->qc_status_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Lead::getStatusList()
            ],
            'qc_call_att',

            'qc_time_from',
            'qc_time_to',
            'qc_client_time_enable:boolean',

//            [
//                'attribute' => 'qc_created_user_id',
//                'value' => function (\common\models\QcallConfig $model) {
//                    return  $model->qcCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->qcCreatedUser->username) : $model->qc_created_user_id;
//                },
//                'format' => 'raw',
//                'filter' => \common\models\Employee::getActiveUsersList()
//            ],

            [
                'attribute' => 'qc_updated_user_id',
                'value' => function (\common\models\QcallConfig $model) {
                    return  $model->qcUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->qcUpdatedUser->username) : $model->qc_updated_user_id;
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getActiveUsersList()
            ],
            //'c_created_dt',
//            [
//                'attribute' => 'qc_created_dt',
//                'value' => function (\common\models\QcallConfig $model) {
//                    return $model->qc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->qc_created_dt)) : '-';
//                },
//                'format' => 'raw'
//            ],
            [
                'attribute' => 'qc_updated_dt',
                'value' => function (\common\models\QcallConfig $model) {
                    return $model->qc_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->qc_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
