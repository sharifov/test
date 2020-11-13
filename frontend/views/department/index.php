<?php

use common\models\Department;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\widgets\Pjax;
use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Departments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Create Department', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'dep_id',
            'dep_key',
            'dep_name',

            [
                'attribute' => 'dep_params',
                'value' => static function (Department $model) {

                    try {
                        $val = Json::decode($model->dep_params);
                        return '<pre>'.print_r($val, true).'</pre>';
                    } catch (Throwable $e) {
                        return 'Json decode error';
                    }
                },
                'format' => 'raw',
                //'filter' => false
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'dep_updated_user_id',
                'relation' => 'depUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'dep_updated_dt'
            ],

            /*[
                'attribute' => 'dep_updated_dt',
                'value' => static function (\common\models\Department $model) {
                    return $model->dep_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dep_updated_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'dep_updated_dt',
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
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
