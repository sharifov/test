<?php

use src\model\leadStatusReason\entity\LeadStatusReason;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadStatusReason\entity\LeadStatusReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Status Reasons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-status-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Status Reason', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'lsr_id',
            'lsr_key',
            'lsr_name',
            'lsr_description',
            'lsr_enabled:booleanByLabel',
            'lsr_comment_required:booleanByLabel',
//            'lsr_params',
            'lsr_created_user_id:username',
            'lsr_updated_user_id:username',
            'lsr_created_dt:byUserDateTime',
            'lsr_updated_dt:byUserDateTime',
            [
                'class' => ActionColumn::class,
                'urlCreator' => static function ($action, LeadStatusReason $model, $key, $index, $column): string {
                    return Url::toRoute([$action, 'lsr_id' => $model->lsr_id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
