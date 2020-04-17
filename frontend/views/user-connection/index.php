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
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'uc_id',
            'uc_connection_uid',
            'uc_connection_id',
            //'uc_user_id',
            [
                'attribute' => 'uc_user_id',
                'value' => static function (\common\models\UserConnection $model) {
                    return  ($model->ucUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->ucUser->username) : $model->uc_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],
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
