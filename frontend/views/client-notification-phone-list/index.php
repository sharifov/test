<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\client\notifications\phone\entity\search\ClientNotificationPhoneListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Notification Phone Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-notification-phone-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Notification Phone', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-notification-phone-list', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cnfl_id',
            ['class' => \common\components\grid\clientNotification\ClientNotificationPhoneListStatusColumn::class],
            'cnfl_from_phone_id',
            'cnfl_to_client_phone_id',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnfl_start',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnfl_end',
                'limitEndDay' => false,
            ],
            //'cnfl_message:ntext',
            //'cnfl_file_url:url',
            'cnfl_call_sid',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnfl_created_dt',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cnfl_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
