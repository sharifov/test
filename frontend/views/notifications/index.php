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

    <h1><?= Html::encode($this->title) ?></h1>
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
                //'format' => 'html',
                'value' => function(\common\models\Notifications $model){
                    return $model->nUser->username;
                },
                'filter' => \common\models\Employee::getList()
            ],
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
            'n_new:boolean',
            'n_deleted:boolean',
            'n_popup:boolean',
            'n_popup_show:boolean',
            'n_read_dt',
            'n_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
