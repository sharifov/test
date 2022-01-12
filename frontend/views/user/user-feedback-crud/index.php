<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userFeedback\entity\search\UserFeedbackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Feedbacks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-feedback-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Feedback', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'uf_id',
            'uf_type_id',
            'uf_status_id',
            'uf_title',
            'uf_message:ntext',
            //'uf_data_json',
            //'uf_created_dt',
            //'uf_updated_dt',
            //'uf_created_user_id',
            //'uf_updated_user_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, UserFeedback $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
