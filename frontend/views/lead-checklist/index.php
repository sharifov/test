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

            'lc_type_id',
            'lc_lead_id',
            'lc_user_id',
            'lc_notes',
            'lc_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
