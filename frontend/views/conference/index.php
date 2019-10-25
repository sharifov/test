<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ConferenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conferences';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cf_id',

            [
                    'attribute' => 'cf_cr_id',
                    'value' => function(\common\models\Conference $model) {
                        return $model->cfCr ? $model->cfCr->cr_name : '-';
                    },
                    'filter' => \common\models\ConferenceRoom::getList()
            ],
            'cf_sid',
            'cf_status_id',
            'cf_options:ntext',
            //'cf_created_dt',
            //'cf_updated_dt',

            [
                'attribute' => 'cf_created_dt',
                'value' => function(\common\models\Conference $model) {
                    return $model->cf_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cf_created_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => \dosamigos\datepicker\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cf_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            [
                'attribute' => 'cf_updated_dt',
                'value' => function(\common\models\Conference $model) {
                    return $model->cf_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cf_updated_dt)) : '';
                },
                'format' => 'raw',
                'filter' => \dosamigos\datepicker\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cf_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
