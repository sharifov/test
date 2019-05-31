<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadChecklistTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Checklist Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-checklist-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Checklist Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'lct_id',
            'lct_key',
            'lct_name',
            'lct_description',
            'lct_enabled:boolean',
            'lct_sort_order',
            [
                'attribute' => 'lct_updated_dt',
                'value' => function(\common\models\LeadChecklistType $model) {
                    return $model->lct_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lct_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'lct_updated_user_id',
                'value' => function (\common\models\LeadChecklistType $model) {
                    return  $model->lctUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->lctUpdatedUser->username) : $model->lct_updated_user_id;
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
