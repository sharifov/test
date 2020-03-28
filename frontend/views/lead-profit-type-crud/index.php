<?php

use common\models\LeadProfitType;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use sales\yii\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadProfitTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Profit Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-profit-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Lead Profit Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'lpt_profit_type_id',
                'value' => static function (LeadProfitType $model) {
                    return LeadProfitType::getProfitTypeName($model->lpt_profit_type_id);
                },
                'filter' => LeadProfitType::getProfitTypeList()
            ],
            'lpt_diff_rule:percentInteger',
            'lpt_commission_min:percentInteger',
            'lpt_commission_max:percentInteger',
            'lpt_commission_fix:percentInteger',
			[
				'class' => \sales\yii\grid\UserSelect2Column::class,
				'attribute' => 'lpt_created_user_id',
				'relation' => 'createdUser',
				'url' => '/employee/list-ajax',
			],
			[
				'class' => \sales\yii\grid\UserSelect2Column::class,
				'attribute' => 'lpt_updated_user_id',
				'relation' => 'updatedUser',
				'url' => '/employee/list-ajax',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'lpt_created_dt',
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'lpt_updated_dt',
			],


			['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
