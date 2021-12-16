<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\client\notifications\sms\entity\search\ClientNotificationSmsListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Notification Sms Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-notification-sms-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Notification Sms', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-notification-sms-list', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cnsl_id',
            ['class' => \common\components\grid\clientNotification\ClientNotificationSmsListStatusColumn::class],
            'cnsl_from_phone_id',
            'cnsl_to_client_phone_id',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnsl_start',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnsl_end',
                'limitEndDay' => false,
            ],
            'cnsl_sms_id',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnsl_created_dt',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnsl_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
