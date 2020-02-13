<?php

use sales\yii\grid\UserColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\userPayment\UserPaymentCategoryIdColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use sales\yii\grid\userPayment\UserPaymentStatusIdColumn;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\payment\search\UserPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'upt_id',
			[
				'class' => UserColumn::class,
				'attribute' => 'upt_assigned_user_id',
				'relation' => 'uptAssignedUser'
			],
            [
                'class' => UserPaymentCategoryIdColumn::class,
				'attribute' => 'upt_category_id',
				'relation' => 'uptCategory'
            ],
            [
                'class' => UserPaymentStatusIdColumn::class,
                'attribute' => 'upt_status_id',
            ],
            'upt_amount',
            'upt_description',
            'upt_date',
			[
				'class' => UserColumn::class,
				'attribute' => 'upt_created_user_id',
				'relation' => 'uptAssignedUser'
			],
			[
				'class' => UserColumn::class,
				'attribute' => 'upt_updated_user_id',
				'relation' => 'uptAssignedUser'
			],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'upt_created_dt',
            ],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'upt_updated_dt',
			],
            'upt_payroll_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
