<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\voip\phoneDevice\PhoneDeviceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Device Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-device-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Device Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-phone-device-log']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pdl_id',
            'pdl_user_id',
            'pdl_device_id',
            'pdl_level',
            'pdl_message',
            //'pdl_error',
            'pdl_timestamp_ts:datetime',
            //'pdl_created_dt',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
