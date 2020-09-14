<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatVisitorData\entity\search\ClientChatVisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Visitor Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-visitor-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Visitor Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cvd_id',
            'cvd_visitor_rc_id',
            'cvd_country',
            'cvd_region',
            'cvd_city',
            'cvd_latitude',
            //'cvd_longitude',
            //'cvd_url:url',
            //'cvd_title',
            //'cvd_referrer',
            //'cvd_timezone',
            //'cvd_local_time',
            //'cvd_data',
            //'cvd_created_dt',
            //'cvd_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
