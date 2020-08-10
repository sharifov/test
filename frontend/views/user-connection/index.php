<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserConnectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Connections';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-connection-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /*= Html::a('Create User Connection', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],

        'rowOptions' => static function (\common\models\UserConnection $model) {
            if ($model->uc_idle_state) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'uc_id',
            'uc_connection_uid',
            'uc_connection_id',
            'uc_app_instance',
            //'uc_user_id',

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'uc_user_id',
                'relation' => 'ucUser',
                'placeholder' => 'Select User',
            ],
            [
                'attribute' => 'uc_sub_list',
                'value' => static function (\common\models\UserConnection $model) {
                    if ($model->uc_sub_list && $subList = @json_decode($model->uc_sub_list, true)) {
                        return Html::encode(implode(', ', $subList));
                    }
                    return  '-';
                },
                'format' => 'raw',
            ],
//            'uc_sub_list',
            'uc_lead_id',
            'uc_case_id',
            'uc_user_agent',
            'uc_controller_id',
            'uc_action_id',
            [
                'attribute' => 'uc_page_url',
                'value' => static function (\common\models\UserConnection $model) {
                    return  $model->uc_page_url ? '<i class="fa fa-link"></i> ' .Html::a('Link', $model->uc_page_url, ['target' => '_blank', 'data-pjax' => 0, 'title' => Html::encode($model->uc_page_url)]) : '-';
                },
                'format' => 'raw',
            ],
            'uc_ip',
            //'uc_window_state:boolean',
            [
                'class' => \common\components\grid\BooleanColumn::class,
                'attribute' => 'uc_window_state',
            ],
            [
                'attribute' => 'uc_window_state_dt',
                'value' => static function (\common\models\UserConnection $model) {
                    return $model->uc_window_state_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->uc_window_state_dt), 'php: Y-m-d H:i:s') : $model->uc_window_state_dt;
                },
                'format' => 'raw',
            ],


            'uc_idle_state:boolean',
            [
                'attribute' => 'uc_idle_state_dt',
                'value' => static function (\common\models\UserConnection $model) {
                    return $model->uc_idle_state_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->uc_idle_state_dt), 'php: Y-m-d H:i:s') : $model->uc_idle_state_dt;
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'uc_created_dt',
                'value' => static function (\common\models\UserConnection $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->uc_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'uc_created_dt',
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
                'label' => 'Duration',
                'value' => static function (\common\models\UserConnection $model) {
                    return Yii::$app->formatter->asRelativeTime(strtotime($model->uc_created_dt));
                },
                'format' => 'raw'
            ],

            //'uc_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
