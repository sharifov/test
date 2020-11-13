<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserOnlineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Onlines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-online-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Online', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => static function (\common\models\UserOnline $model) {
            if ($model->uo_idle_state) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'User ID',
                'value' => static function(\common\models\UserOnline $model) {
                    return $model->uo_user_id;
                },
            ],

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'label' => 'User',
                'attribute' => 'uo_user_id',
                'relation' => 'uoUser',
                'placeholder' => 'Select User',
            ],

            'uo_idle_state:boolean',
            [
                //'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'uo_idle_state_dt',
                'value' => static function (\common\models\UserOnline $model) {
                    return $model->uo_idle_state_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->uo_idle_state_dt), 'php: Y-m-d [H:i:s]')  : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'uo_idle_state_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                        'endDate' => date('Y-m-d', time())
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                    'containerOptions' => [
                        'class' => (array_key_exists('uo_idle_state_dt', $searchModel->errors)) ? 'has-error' : null,
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],

            /*[
                'class' => \common\components\grid\UserColumn::class,
                'attribute' => 'uo_user_id',
                'relation' => 'uoUser',
            ],*/
            [
                //'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'uo_updated_dt',
                'value' => static function (\common\models\UserOnline $model) {
                    return $model->uo_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->uo_updated_dt), 'php: Y-m-d [H:i:s]')  : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'uo_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                        'endDate' => date('Y-m-d', time())
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                    'containerOptions' => [
                        'class' => (array_key_exists('uo_updated_dt', $searchModel->errors)) ? 'has-error' : null,
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],



            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
