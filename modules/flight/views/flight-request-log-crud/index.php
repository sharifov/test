<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightRequestLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Request Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-request-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Request Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'flr_id',
            'flr_fr_id',
            'flr_status_id_old',
            'flr_status_id_new',
            'flr_description',
            //'flr_created_dt',
            //'flr_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
