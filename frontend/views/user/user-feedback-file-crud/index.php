<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use modules\user\userFeedback\entity\UserFeedbackFile;

/* @var $this yii\web\View */
/* @var $searchModel modules\user\userFeedback\entity\search\UserFeedbackFileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Feedback Files';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-feedback-file-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Feedback File', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'uff_id',
            'uff_uf_id',
            'uff_mimetype',
            'uff_size',
            'uff_filename',
            //'uff_title',
            //'uff_blob',
            //'uff_created_dt',
            //'uff_created_user_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, UserFeedbackFile $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'uff_id' => $model->uff_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
