<?php

use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProfitSplitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Profit Split';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-split-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-bars"></i> Sold Lead without Profit Split', ['sold-lead-list'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'ps_id',
            [
                'attribute' => 'ps_lead_id',
                'format' => 'raw',
                'value' => function (\common\models\ProfitSplit $model) {
                    return '<i class="fa fa-link"></i> ' .
                         Html::a($model->ps_lead_id, [
                            'lead/view', 'gid' => $model->psLead->gid
                        ], [
                            'data-pjax' => 0,
                            'target' => '_blank'
                        ]);
                }
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ps_user_id',
                'relation' => 'psUser',
                'placeholder' => 'Select User',
            ],
            'ps_percent',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, \common\models\ProfitSplit $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->ps_id]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
