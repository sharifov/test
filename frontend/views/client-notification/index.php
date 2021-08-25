<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\client\notifications\client\entity\search\ClientNotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Notifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-notification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Notification', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-notification']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cn_id',
            'cn_client_id',
            ['class' => \common\components\grid\clientNotification\ClientNotificationTypeColumn::class],
            'cn_object_id',
            ['class' => \common\components\grid\clientNotification\ClientNotificationCommunicationTypeColumn::class],
            //'cn_communication_object_id',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cn_created_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
