<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;

/* @var $this yii\web\View */
/* @var $searchModel src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Business Extra Queue Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-business-extra-queue-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Business Extra Queue Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'lbeqr_key',
            'lbeqr_name',
            'lbeqr_description:text',
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'lbeqr_updated_user_id',
                'relation' => 'lbeqrUpdatedUser',
                'placeholder' => 'Updated User'
            ],
            'lbeqr_duration',
            'lbeqr_start_time',
            'lbeqr_end_time',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, LeadBusinessExtraQueueRule $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lbeqr_id' => $model->lbeqr_id]);
                },
                'visibleButtons' => [
                    'delete' => static function (LeadBusinessExtraQueueRule $model) {
                        return $model->lbeqr_type_id !== LeadBusinessExtraQueueRule::TYPE_ID_REPEATED_PROCESS_RULE;
                    },
                ]
            ],
        ],
    ]); ?>


</div>
