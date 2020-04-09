<?php

use sales\model\user\entity\payment\UserPayment;
use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;
use common\components\grid\userPayment\UserPaymentCategoryIdColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\grid\userPayment\UserPaymentStatusIdColumn;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\user\entity\payment\search\UserPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'rowOptions'=> static function(UserPayment $model){
			return ['class' => $model->getRowClass()];
		},
        'columns' => [
            'upt_id',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'upt_assigned_user_id',
                'relation' => 'uptAssignedUser',
                'placeholder' => 'Select User',
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
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'upt_date',
                'format' => 'date'
			],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'upt_created_user_id',
                'relation' => 'uptCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'upt_updated_user_id',
                'relation' => 'uptUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'upt_created_dt',
            ],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'upt_updated_dt',
			],
			[
                'attribute' => 'upt_payroll_id',
				'value' => static function (UserPayment $model) {
					$count = $model->upt_payroll_id ? 1 : 0;
					$route = Url::toRoute(['/user-payroll-crud/view',  'id' => $model->upt_payroll_id]);
					return ($count ? Html::a($model->upt_payroll_id, $route, [
						'target' => '_blank',
						'data-pjax' => 0
					]) : null);
				},
				'format' => 'raw'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
