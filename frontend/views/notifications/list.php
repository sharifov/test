<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\NotificationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('notifications', 'My Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-list">


    <h1><i class="fa fa-comment-o"></i> <?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-check"></i> Make Read All', ['all-read'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to mark read all notifications?'),
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a('<i class="fa fa-times"></i> Delete All', ['all-delete'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to delete all notifications?'),
                'method' => 'post',
            ],
        ]) ?>

    </p>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-bordered table-condensed'],
        'rowOptions' => function (\common\models\Notifications $model, $index, $widget, $grid) {
            /*if($model->n_type_id == 4) {
                return ['style' => 'background-color:#f2dede'];
            }
            if($model->n_type_id == 3) {
                return ['style' => 'background-color:#fcf8e3'];
            }
            if($model->n_type_id == 1) {
                return ['style' =>  'background-color:#dff0d8'];
            }
            if($model->n_type_id == 2) {
                return ['style' =>  'background-color:#d9edf7'];
            }*/

            if($model->n_new) {
                return ['class' =>  'warning'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'n_id',

            //'n_user_id',

            //'n_title',

            [
                'attribute' => 'n_title',
                'value' => static function (\common\models\Notifications $model) {
                    return Html::a($model->n_title, ['notifications/view2', 'id' => $model->n_id]);
                },
                'format' => 'raw'
            ],

            'n_message:ntext',

            'n_new:boolean',

            [
                'attribute' => 'n_type_id',
                //'format' => 'html',
                'value' => function(\common\models\Notifications $model){
                    return '<span class="label label-default">'.$model->getType().'</span>';
                },
                'format' => 'raw',
                'filter' => \common\models\Notifications::getTypeList()
            ],

            //'n_deleted:boolean',
            //'n_popup:boolean',
            //'n_popup_show:boolean',


            [
                'attribute' => 'n_read_dt',
                'value' => static function (\common\models\Notifications $model) {
                    return $model->n_read_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_read_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'n_read_dt',
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
                'attribute' => 'n_created_dt',
                'value' => static function (\common\models\Notifications $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'n_created_dt',
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

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                //'controller' => 'order-shipping',
                'template' => '{view2} {soft-delete}',

                'buttons' => [
                    'view2' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                            'title' => Yii::t('notifications', 'View'),
                        ]);
                    },
                    'soft-delete' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                            'title' => Yii::t('notifications', 'Delete'),
                            'data' => [
                                'confirm' => Yii::t('notifications', 'Are you sure you want to delete this message?'),
                                //'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
