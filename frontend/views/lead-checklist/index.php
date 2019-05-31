<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadChecklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Checklists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-checklist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Checklist', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'lc_type_id',
            [
                'attribute' => 'lc_type_id',
                'value' => function (\common\models\LeadChecklist $model) {
                    return  $model->lcType ? $model->lcType->lct_name : $model->lc_type_id;
                },
                'filter' => \common\models\LeadChecklistType::getList()
                //'format' => 'raw'
            ],

            'lc_lead_id',

            'lc_notes',

            [
                'attribute' => 'lc_created_dt',
                'value' => function(\common\models\LeadChecklist $model) {
                    return $model->lc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lc_created_dt)) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'lc_user_id',
                'value' => function (\common\models\LeadChecklist $model) {
                    return  $model->lcUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->lcUser->username) : $model->lc_user_id;
                },
                'format' => 'raw'
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
