<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use src\model\voip\phoneDevice\log\PhoneDeviceLog;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\voip\phoneDevice\log\PhoneDeviceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Device Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-device-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-phone-device-log', 'timeout' => 7000, 'enablePushState' => true, 'scrollTo' => 0]); ?>

    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
      //  'pjax' => false,
        'columns' => [
            [
                'attribute' => 'pdl_id',
                'options' => ['style' => 'width:80px'],
                'visible' => $searchModel->isVisible('pdl_id'),
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'pdl_user_id',
                'relation' => 'user',
                'options' => ['style' => 'width:200px'],
                'visible' => $searchModel->isVisible('pdl_user_id'),
            ],
            [
                'attribute' => 'pdl_device_id',
                'options' => ['style' => 'width:180px'],
                'visible' => $searchModel->isVisible('pdl_device_id'),
            ],
            ['class' => \common\components\grid\PhoneDeviceLogLevelColumn::class, 'visible' => $searchModel->isVisible('pdl_level')],
            ['attribute' => 'pdl_message', 'visible' => $searchModel->isVisible('pdl_message')],
            [
                'attribute' => 'pdl_error',
                'value' => static function (PhoneDeviceLog $log) {
                    if (!$log->pdl_error) {
                        return null;
                    }
                    return '<pre><small>' . VarDumper::dumpAsString($log->pdl_error, 10, false) . '</small></pre>';
                },
                'format' => 'raw',
                'visible' => $searchModel->isVisible('pdl_error'),
            ],
            [
                'attribute' => 'pdl_stacktrace',
                'options' => ['style' => 'width:250px'],
                'visible' => $searchModel->isVisible('pdl_stacktrace'),
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pdl_timestamp_dt',
                'format' => 'ntext',
                'options' => ['style' => 'width:220px'],
                'visible' => $searchModel->isVisible('pdl_timestamp_dt'),
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pdl_created_dt',
                'format' => 'byUserDatetimeWithSeconds',
                'options' => ['style' => 'width:220px'],
                'visible' => $searchModel->isVisible('pdl_created_dt'),
            ],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}&nbsp;&nbsp;&nbsp;{delete}', 'options' => ['style' => 'width:70px'],],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
