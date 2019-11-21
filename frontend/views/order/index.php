<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'or_id',
            'or_gid',
            'or_uid',
            'or_name',
            'or_lead_id',
            'or_description:ntext',
            'or_status_id',
            'or_pay_status_id',
            'or_app_total',
            'or_app_markup',
            'or_agent_markup',
            'or_client_total',
            'or_client_currency',
            'or_client_currency_rate',
            'or_owner_user_id',
            'or_created_user_id',
            'or_updated_user_id',

            [
                'attribute' => 'or_created_dt',
                'value' => static function(\common\models\Order $model) {
                    return $model->or_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->or_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'or_updated_dt',
                'value' => static function(\common\models\Order $model) {
                    return $model->or_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->or_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
            //'or_created_dt',
            //'or_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
