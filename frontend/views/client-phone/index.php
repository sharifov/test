<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClentPhoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Phones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-phone-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Client Phone', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Client',
                'attribute' => 'client_id',
                'value' => static function (\common\models\ClientPhone $model) {
                    $client = $model->client;
                    if($client->id) {
                        return '<span class="label label-info"> <i class="fa fa-link"></i> ' .  Html::encode($client->full_name). '</span>';
                    } else {
                        return 'not set';
                    }
                },
                'format' => 'raw'
            ],
            //'client_id',
            'phone',
            'cp_title',
            [
                'attribute' => 'type',
                'value' => static function (\common\models\ClientPhone $model) {
                    return $model::getPhoneTypeLabel($model->type);
                },
                'format' => 'raw',
                'filter' =>  \common\models\ClientPhone::getPhoneTypeList()
            ],
            [
                'attribute' => 'created',
                'value' => static function (\common\models\ClientPhone $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
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
            //'updated',
            //'comments:ntext',
            'is_sms:boolean',
            //'validate_dt',
            [
                'attribute' => 'validate_dt',
                'value' => static function (\common\models\ClientPhone $model) {
                    return $model->validate_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->validate_dt)) : null;
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'validate_dt',
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

            ['class' => ActionColumn::class],
        ],
    ]); ?>

</div>
