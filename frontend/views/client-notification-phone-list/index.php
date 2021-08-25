<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\client\notifications\phone\entity\search\ClientNotificationPhoneListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Notification Phone Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-notification-phone-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Notification Phone List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-notification-phone-list']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cnfl_id',
            ['class' => \common\components\grid\clientNotification\ClientNotificationPhoneListStatusColumn::class],
            'cnfl_from_phone_id',
            'cnfl_to_client_phone_id',
            'cnfl_start',
            //'cnfl_end',
            //'cnfl_message:ntext',
            //'cnfl_file_url:url',
            //'cnfl_data',
            //'cnfl_call_sid',
            //'cnfl_created_dt',
            //'cnfl_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
