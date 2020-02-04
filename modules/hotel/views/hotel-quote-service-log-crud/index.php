<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\models\search\HotelQuoteServiceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Quote Service Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-quote-service-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel Quote Service Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'hqsl_id',
            'hqsl_hotel_quote_id',
            'hqsl_action_type_id',
            'hqsl_status_id',
            'hqsl_message:ntext',
            //'hqsl_created_user_id',
            //'hqsl_created_dt',
            //'hqsl_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
