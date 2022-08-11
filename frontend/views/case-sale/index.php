<?php

use common\models\CaseSale;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Sales';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-sale-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Sale', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['timeout' => 5000, 'scrollTo' => 0]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>

    <?= $searchModel->filterCount ? 'Find <b>' . $searchModel->filterCount . '</b> items' : null ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['case-sale/index']),
        'layout' => "{items}",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'css_cs_id',
                'enableSorting' => false,
            ],
            'css_sale_id',
            'css_sale_book_id',
            'css_sale_pnr',
            'css_sale_pax',

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'css_sale_created_dt'
            ],

            /*[
                'attribute' => 'css_sale_created_dt',
                'value' => function(\common\models\CaseSale $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->css_sale_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'css_sale_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            //'css_sale_data',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'css_created_user_id',
                'relation' => 'cssCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'css_updated_user_id',
                'relation' => 'cssUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'css_created_dt'
            ],

            /*[
                'attribute' => 'css_created_dt',
                'value' => function(\common\models\CaseSale $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->css_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'css_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'css_updated_dt'
            ],

            /*[
                'attribute' => 'css_updated_dt',
                'value' => function(\common\models\CaseSale $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->css_updated_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'css_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/
            [
                'label' => 'Need sync bo',
                'attribute' => 'css_need_sync_bo',
                'filter' => [0 => 'No', 1 => 'Yes'],
                'value' => static function (CaseSale $model) {
                    return Yii::$app->formatter->asBoolean($model->css_need_sync_bo);
                },
                'format' => 'raw',
                'enableSorting' => false,
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]); ?>

    <?php Pjax::end(); ?>

</div>
