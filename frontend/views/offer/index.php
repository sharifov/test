<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\OfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Offers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Offer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'of_id',
            'of_gid',
            'of_uid',
            'of_name',
            'of_lead_id',
            'of_status_id',

            'of_client_currency',
            'of_client_currency_rate',
            'of_app_total',
            'of_client_total',

            'of_owner_user_id',
            'of_created_user_id',
            'of_updated_user_id',

            [
                'attribute' => 'of_created_dt',
                'value' => static function(\common\models\Offer $model) {
                    return $model->of_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->of_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'of_updated_dt',
                'value' => static function(\common\models\Offer $model) {
                    return $model->of_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->of_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],

            //'of_created_dt',
            //'of_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
