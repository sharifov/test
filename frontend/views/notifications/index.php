<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\NotificationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('notifications', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-index">

    <h1><i class="fa fa-comment-o"></i> <?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('notifications', 'Create Notifications'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'n_id',
            //'n_user_id',
            [
                'attribute' => 'n_user_id',
                'value' => function(\common\models\Notifications $model){
                    return $model->nUser->username;
                },
                'filter' => \common\models\Employee::getList()
            ],
            [
                'attribute' => 'n_type_id',
                'value' => function(\common\models\Notifications $model){
                    return '<span class="label label-default">'.$model->getType().'</span>';
                },
                'format' => 'raw',
                'filter' => \common\models\Notifications::getTypeList()
            ],
            'n_title',
            'n_message:ntext',
            'n_new:boolean',
            'n_deleted:boolean',
            'n_popup:boolean',
            'n_popup_show:boolean',
            [
                'attribute' => 'n_read_dt',
                'value' => function (\common\models\Notifications $model) {
                    return $model->n_read_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_read_dt)) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'n_created_dt',
                'value' => function (\common\models\Notifications $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_created_dt));
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
