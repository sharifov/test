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

            'lbeqr_id',
            'lbeqr_key',
            'lbeqr_name',
            'lbeqr_description:ntext',
            'lbeqr_params_json',
            //'lbeqr_updated_user_id',
            //'lbeqr_created_dt',
            //'lbeqr_updated_dt',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LeadBusinessExtraQueueRule $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'lbeqr_id' => $model->lbeqr_id]);
                }
            ],
        ],
    ]); ?>


</div>
