<?php

use src\model\coupon\entity\coupon\Coupon;
use yii\grid\ActionColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\coupon\CouponStatusColumn;
use common\components\grid\coupon\CouponTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel src\model\coupon\entity\coupon\search\CouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            'c_id',
            'c_code',
            'c_amount',
            'c_currency_code',
            'c_percent',
            ['class' => BooleanColumn::class, 'attribute' => 'c_reusable'],
            'c_reusable_count',
            'c_used_count',
            ['class' => BooleanColumn::class, 'attribute' => 'c_public'],
            ['class' => CouponStatusColumn::class],
            ['class' => BooleanColumn::class, 'attribute' => 'c_disabled'],
            ['class' => CouponTypeColumn::class],
            [
                'attribute' => 'c_start_date',
                'value' => static function (Coupon $model) {
                    if ($model->c_start_date) {
                        return '<i class="fa fa-calendar"></i> ' . date('Y-M-d', strtotime($model->c_start_date));
                    }
                    return Yii::$app->formatter->nullDisplay;
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'c_start_date',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date',
                        'readonly' => '1',
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],
            [
                'attribute' => 'c_exp_date',
                'value' => static function (Coupon $model) {
                    if ($model->c_exp_date) {
                        return '<i class="fa fa-calendar"></i> ' . date('Y-M-d', strtotime($model->c_exp_date));
                    }
                    return Yii::$app->formatter->nullDisplay;
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'c_exp_date',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date',
                        'readonly' => '1',
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],

            ['class' => DateTimeColumn::class, 'attribute' => 'c_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'c_updated_dt'],
            ['class' => UserSelect2Column::class, 'attribute' => 'c_created_user_id', 'relation' => 'createdUser'],
            ['class' => UserSelect2Column::class, 'attribute' => 'c_updated_user_id', 'relation' => 'updatedUser'],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
