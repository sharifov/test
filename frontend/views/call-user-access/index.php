<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallUserAccessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call User Accesses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-user-access-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call User Access', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'cua_call_id',

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'cua_user_id',
                'relation' => 'cuaUser',
                'placeholder' => 'Select User',
            ],

            //'cua_status_id',
            [
                'attribute' => 'cua_status_id',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return Html::encode($model->getStatusTypeName());
                },
                'format' => 'raw',
                'filter' => \common\models\CallUserAccess::getStatusTypeList()
            ],
            [
                'attribute' => 'cua_created_dt',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return $model->cua_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cua_created_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cua_created_dt',
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
                'attribute' => 'cua_updated_dt',
                'value' => static function (\common\models\CallUserAccess $model) {
                    return $model->cua_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cua_updated_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cua_updated_dt',
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
