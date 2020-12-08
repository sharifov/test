<?php

use yii\grid\ActionColumn;
use sales\model\clientChatHold\entity\ClientChatHold;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use common\components\grid\DateTimeColumn;

/* @var yii\web\View $this */
/* @var sales\model\clientChatHold\entity\ClientChatHoldSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Chat Hold';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-hold-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Hold', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cchd_cch_id',
            'cchd_cch_status_log_id',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cchd_start_dt',
                'format' => 'byUserDateTime'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cchd_deadline_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
