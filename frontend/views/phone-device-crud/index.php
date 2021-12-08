<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\voip\phoneDevice\device\PhoneDeviceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Devices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-device-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php /*
    <p>
        <?= Html::a('Create Phone Device', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    */ ?>

    <?php Pjax::begin(['id' => 'pjax-phone-device']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'pd_id', 'options' => ['style' => 'width:80px']],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'pd_user_id',
                'relation' => 'user',
            ],
            ['attribute' => 'pd_device_identity', 'options' => ['style' => 'width:180px']],
            ['attribute' => 'pd_connection_id', 'options' => ['style' => 'width:180px']],
            ['attribute' => 'pd_name', 'options' => ['style' => 'width:250px']],
            [
                'class' => \common\components\grid\BooleanColumn::class,
                'attribute' => 'pd_status_device',
                'options' => ['style' => 'width:130px'],
            ],
            [
                'class' => \common\components\grid\BooleanColumn::class,
                'attribute' => 'pd_status_speaker',
                'options' => ['style' => 'width:130px'],
            ],
            [
                'class' => \common\components\grid\BooleanColumn::class,
                'attribute' => 'pd_status_microphone',
                'options' => ['style' => 'width:150px'],
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pd_updated_dt',
                'format' => 'byUserDatetimeWithSeconds',
                'options' => ['style' => 'width:220px'],
            ],

            ['class' => 'yii\grid\ActionColumn', 'options' => ['style' => 'width:100px']],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
