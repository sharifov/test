<?php

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

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('notifications', 'Read All'), ['all-read'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => Yii::t('notifications', 'Are you sure you want to mark read all notifications?'),
                'method' => 'post',
            ],
        ]) ?>

        <?= Html::a(Yii::t('notifications', 'Delete All'), ['all-delete'], [
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
            if($model->n_type_id == 4) {
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
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'n_id',
            'n_new:boolean',
            //'n_user_id',
            [
                'attribute' => 'n_type_id',
                //'format' => 'html',
                'value' => function(\common\models\Notifications $model){
                    return $model->getType();
                },
                'filter' => \common\models\Notifications::getTypeList()
            ],
            'n_title',
            'n_message:ntext',

            //'n_deleted:boolean',
            //'n_popup:boolean',
            //'n_popup_show:boolean',
            'n_read_dt:datetime',
            'n_created_dt:datetime',

            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                //'controller' => 'order-shipping',
                'template' => '{view2} {soft-delete}',

                /*'visibleButtons' => [
                    'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },
                    'update' => function ($model, $key, $index) {
                        return User::hasPermission('updateOrder');
                    },
                    'delete' => function ($model, $key, $index) {
                        return User::hasRole(['admin']);
                    },
                    'soft-delete' => function ($model, $key, $index) {
                        return User::hasPermission('deleteOrder');
                    },
                ],*/

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
