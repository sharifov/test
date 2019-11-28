<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'pr_id',
            //'pr_type_id',
            [
                'attribute' => 'pr_type_id',
                'value' => static function (\common\models\Product $model) {
                    return $model->prType ? $model->prType->pt_name : $model->pr_type_id;
                },
                'filter' => \common\models\ProductType::getList(false)
            ],
            'pr_name',
            //'pr_lead_id',

            [
                'attribute' => 'pr_lead_id',
                'value' => static function (\common\models\Product $model) {
                    return Html::a($model->pr_lead_id, ['lead/view', 'gid' => $model->prLead->gid], [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
                },
                'format' => 'raw'
            ],

            'pr_description:ntext',
            'pr_status_id',
            'pr_service_fee_percent',
            'pr_created_user_id',
            'pr_updated_user_id',
            //'pr_created_dt',
            //'pr_updated_dt',

            [
                'attribute' => 'pr_created_dt',
                'value' => static function(\common\models\Product $model) {
                    return $model->pr_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pr_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'pr_updated_dt',
                'value' => static function(\common\models\Product $model) {
                    return $model->pr_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->pr_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
